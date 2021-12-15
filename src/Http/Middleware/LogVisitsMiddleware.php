<?php

namespace Opengis\LogVisits\Http\Middleware;

use Closure;
use Opengis\LogVisits\LogVisits;

class LogVisitsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        LogVisits::logVisit();

        return $response;
    }
}
