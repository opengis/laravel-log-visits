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
    | Use the native PHP get_browser function
    | The browscap directive of the [browscap] section of your php.ini must be set
    | Authorized values are: native and cache
    |
    */
    'get-browser-source' => env('VISIT_BROWSER_SOURCE', 'cache'),

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
    | Default ip metadata cache strategy (in days)
    |
    */
    'ip-metadata-cache-days' => env('VISIT_IP_METADATA_CACHE_DAYS', 30),

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
