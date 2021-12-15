<?php

namespace Opengis\LogVisits;

use Illuminate\Routing\Router;
use Opengis\LogVisits\Commands\UpdateBrowscapCommand;
use Opengis\LogVisits\Http\Middleware\LogVisitsMiddleware;
use Opengis\LogVisits\Services\GeoIp\GeoIpInterface;
use Opengis\LogVisits\Services\GeoIp\IpStackGeoIp;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LogVisitsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-log-visits')
            ->hasConfigFile()
            ->hasMigration('create_page_visits_log_table')
            ->hasCommand(UpdateBrowscapCommand::class);

        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('log-visits', LogVisitsMiddleware::class);

        $this->app->bind(GeoIpInterface::class, function () {
            return new IpStackGeoIp();
        });
    }
}
