<?php

namespace Opengis\LogVisits;

use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Helper\IniLoaderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Opengis\LogVisits\Jobs\LogPageVisitJob;
use Opengis\LogVisits\Services\GeoIp\IpStackGeoIp;

class LogVisits
{
    public static function updateBrowscap()
    {
        $loader = IniLoaderInterface::PHP_INI;

        switch (config('log-visits.browscap-version')) {
            case 'lite':
                $loader = IniLoaderInterface::PHP_INI_LITE;

                break;
            case 'full':
                $loader = IniLoaderInterface::PHP_INI_FULL;

                break;
            default:
                $loader = IniLoaderInterface::PHP_INI;
        }

        $cache = Cache::store(config('log-visits.cache-store'));
        $logger = Log::channel(config('log-visits.log-channel'));

        $bc = new BrowscapUpdater($cache, $logger);
        $bc->update($loader);
    }

    public static function getBrowser($user_agent = null)
    {
        $cache = Cache::store(config('log-visits.cache-store'));
        $logger = Log::channel(config('log-visits.log-channel'));

        $browscap = new Browscap($cache, $logger);

        try {
            return collect($browscap->getBrowser($user_agent))->toArray();
        } catch (\Exception $ex) {
            if ($ex->getMessage() == 'there is no active cache available, please use the BrowscapUpdater and run the update command') {
                LogVisits::updateBrowscap();

                return collect($browscap->getBrowser($user_agent))->toArray();
            }
        }
    }

    public static function getIpMetadata($ip = '127.0.0.1')
    {
        if (config('log-visits.ip-metadata-service') == 'ipstack') {
            $geoIp = new IpStackGeoIp();

            return $geoIp->ip($ip)->getMetadata();
        }

        return [
            'ip' => $ip,
            'success' => false,
            'error' => [
                'info' => 'No ip metadata service configured',
            ],
        ];
    }

    public static function logVisit($user_agent = null, $ip = null): bool
    {
        $serverVars = self::getServerVars();
        $ip = $ip ? $ip : self::getIp($serverVars);
        $user = auth()->user();
        $date = now();

        if (config('log-visits.queue_mode') == 'queue') {
            LogPageVisitJob::dispatch($serverVars, $ip, $user, $date, $user_agent)->onQueue(config('log-visits.default_queue', 'default'));

            return true;
        }

        LogPageVisitJob::dispatchSync($serverVars, $ip, $user, $date, $user_agent);

        return true;
    }

    public static function getServerVars()
    {
        return [
            'full_url' => request()->fullurl(),
            'server' => request()->server(),
            'headers' => request()->header(),
            'input' => request()->all(),
            'cookies' => request()->cookie(),
        ];
    }

    public static function getIp($server_vars = null)
    {
        if (! $server_vars) {
            $server_vars = self::getServerVars();
        }

        $ip = $server_vars['server']['REMOTE_ADDR'];

        if (isset($server_vars['server']['HTTP_X_FORWARDED_FOR'])) {
            $ip = $server_vars['server']['HTTP_X_FORWARDED_FOR'];
        }

        if (isset($server_vars['headers']['x_real_ip'])) {
            $ip = $server_vars['headers']['x_real_ip'];
        }

        return $ip;
    }
}
