<?php

namespace Opengis\LogVisits\Services\GeoIp;

interface GeoIpInterface
{
    public static function ip($ip): self;

    public function getCountryCode(): string;

    public function getMetadata(): array;
}
