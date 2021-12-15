<?php

namespace Opengis\LogVisits\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Opengis\LogVisits\LogVisits
 */
class LogVisits extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-log-visits';
    }
}
