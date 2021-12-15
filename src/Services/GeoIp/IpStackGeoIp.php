<?php

namespace Opengis\LogVisits\Services\GeoIp;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\IpUtils;

class IpStackGeoIp implements GeoIpInterface
{
    protected $baseUrl;

    protected $ip;

    private $privateIps = [
        '127.0.0.1',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
    ];

    private function ipIsPublic(): bool
    {
        return ! IpUtils::checkIp($this->ip, $this->privateIps);
    }

    public function __construct($ip = null)
    {
        $this->ip = $ip;

        $this->baseUrl = config('log-visits.ipstack.url') . '/' . $ip . '?access_key=' . config('log-visits.ipstack.key');
    }

    public static function ip($ip): self
    {
        return new self($ip);
    }

    public function getCountryCode(): string
    {
        $url = $this->baseUrl . '&output=json&language=en&fields=country_code';

        return Cache::store(config('log-visits.cache-store'))->remember($url, now()->addDay(), function () use ($url) {
            try {
                $json = Http::get($url)->json();
                if (isset($json['country_code'])) {
                    if (! is_null($json['country_code'])) {
                        return strtolower($json['country_code']);
                    }
                }

                return config('log-visits.ip-metadata-default-country-code');
            } catch (\Throwable $th) {
                return config('log-visits.ip-metadata-default-country-code');
            }
        });
    }

    public function getMetadata(): array
    {
        if (! $this->ipIsPublic()) {
            return [
                'ip' => $this->ip,
                'private' => true,
            ];
        }

        $url = $this->baseUrl . '&output=json&language=' . config('log-visits.ip-metadata-language') . '&hostname=1';

        return Cache::store(config('log-visits.cache-store'))->remember($url, now()->addDay(), function () use ($url) {
            try {
                $json = Http::get($url)->json();

                if (isset($json['success']) && ! $json['success']) {
                    $json['ip'] = $this->ip;
                }

                return $json;
            } catch (\Throwable $th) {
                return [
                    'ip' => $this->ip,
                    'success' => false,
                    'error' => [
                        'info' => $th->getMessage(),
                    ],
                ];
            }
        });
    }
}
