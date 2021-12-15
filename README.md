# Log page visits with php browscap information

[![Latest Version on Packagist](https://img.shields.io/github/release/opengis/laravel-log-visits.svg?style=flat-square)](https://packagist.org/packages/opengis/laravel-log-visits)
[![PHP Version Require](http://poser.pugx.org/opengis/laravel-log-visits/require/php)](https://packagist.org/packages/opengis/laravel-log-visits)
[![Tests](https://github.com/opengis/laravel-log-visits/actions/workflows/run-tests.yml/badge.svg)](https://github.com/opengis/laravel-log-visits/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/opengis/laravel-log-visits/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/opengis/laravel-log-visits/actions/workflows/php-cs-fixer.yml)



This package allows you to easily log page request with all king of metadata information
- It uses the request to get all server variables;
- It uses a cached version of browscap.ini (http://browscap.org/) file to get browser metadata
- It optionnaly uses Ipstack (https://ipstack.com/) to get metadata information on the ip address

It uses the package browscap/browscap-php (https://github.com/browscap/browscap-php) to get the metadata for the browser.
With this package you don't have to manually download the latest browscap.ini file and reference it in you php.ini [browscap] section.
Moreover, you get the benefits of the cache which is way faster than the php get_browser native function.
You can use the artisan command log-visits:update-browscap to create the cache for the first time but if you don't, it will be automatically generated on the first request. Make sure you adjust the config log-visits.browscap-version before you run it.

For now, laravel-log-visits uses browscap/browscap-php version 5.1 which does not support PHP 8.1, version 6.0 does but requires league/flysystem 2.3.2 and laravel 8 requires league/flysystem 1.1. I plan to update the package to support PHP 8.1 on laravel 9 soon.

## Installation

You can install the package via composer:

```bash
composer require opengis/laravel-log-visits
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="log-visits-migrations"
php artisan migrate
```

You can publish the config file (optional) with:
```bash
php artisan vendor:publish --tag="log-visits-config"
```

The config file uses environment variable for all of it's options so, if you prefer, you can just set the variables in you .env. instead of publishing the file.
This is the contents of the published config file:

```php
<?php

return [
    /*
    |
    | Configure the entries table name used by the package
    |
    */
    'table-name' => env('VISIT_TABLE_NAME', 'page_visits_log'),

    /*
    |
    | Configure the cache store used by the package
    |
    */
    'cache-store' => env('VISIT_CACHE_STORE', env('CACHE_DRIVER', 'file')),

    /*
    |
    | Configure the log channel used by the package
    |
    */
    'log-channel' => env('VISIT_LOG_CHANNEL', env('LOG_CHANNEL', 'null')),

    /*
    |
    | Configure the queue mode by the package
    | Authorized values are: sync and queue
    |
    */
    'queue-mode' => env('VISIT_QUEUE_MODE', 'queue'),

    /*
    |
    | Configure the queue used by the package when queue-mode is 'queue'
    |
    */
    'default-queue' => env('VISIT_DEFAULT_QUEUE', 'default'),

    /*
    |
    | Configure the browscap file used by the package
    | See http://browscap.org/ for the options, be carefull as the full file is more than 100MB
    | Authorized values are: normal, lite or full
    |
    */
    'browscap-version' => env('VISIT_BROWSCAP_VERSION', 'normal'), // normal, lite or full

    /*
    |
    | Language code used for ip metadata service
    |
    */
    'ip-metadata-language' => env('VISIT_IP_METADATA_LANGUAGE', 'en'),

    /*
    |
    | Default country code for ip metadata service
    | This value will be returned by GeoIpInterface::getCountryCode() if
    | if the country code can't be retrieved from the api
    |
    */
    'ip-metadata-default-country-code' => env('VISIT_IP_METADATA_COUNTRY_CODE', 'us'),

    /*
    |
    | Default ip metadata service
    | Authorized values are: none or ipstack
    | If none is specified, no ip metadata will be fetched
    |
    */
    'ip-metadata-service' => env('VISIT_IP_METADATA_SERVICE', 'none'),

    /*
    |
    | Ipstatck (https://ipstack.com/) settings
    | Must be present if previous option (ip-metadata-service) is set to ipstack
    |
    */
    'ipstack' => [
        'url' => env('IPSTACK_URL', 'https://api.ipstack.com'),
        'key' => env('IPSTACK_KEY'),
    ],
];
```

## Usage


```php
use Opengis\LogVisits\LogVisits;

// Logs visits according to the config file options
LogVisits::logVisit();

// Updates the browscap.ini cache from the most recent file
LogVisits::updateBrowscap();

// Gets current request browser information metadata
$browserMetadata = LogVisits::getBrowser();

// Gets browser information metadata for the query string passed as argument
$browserMetadata = LogVisits::getBrowser('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36');

// Gets server variables for the current request
$serverVariables = LogVisits::getServerVars();

// Gets IP metadata for the current request ip
$ipMetadata = LogVisits::getIpMetadata();

// Gets IP metadata for specified IP address
$ipMetadata = LogVisits::getIpMetadata('142.250.64.142');

// Resolves the current IP
// If server variable HTTP_FORWARDED_FOR or header x_real_ip is set, it will be used over REMOTE_ADDR variable
$ip = LogVisits::getIp();
```

A middleware is provided for convenience in your routes:

```php
// This will automatically log visits for this route
Route::get('/', function () {
    return view('welcome');
})->middleware(['log-visits']);
```

You can also update the browscap.ini cache from the most recent file with this artisan command:

```bash
php artisan log-visits:update-browscap
```

You can make sure that you get a fresh version every week by adding this to your App\Console\Kernel.php file:

```php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('log-visits:update-browscap')->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [OpenGIS Development](https://github.com/opengis)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
