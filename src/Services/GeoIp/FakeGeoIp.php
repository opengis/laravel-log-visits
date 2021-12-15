<?php

namespace Opengis\LogVisits\Services\GeoIp;

class FakeGeoIp implements GeoIpInterface
{
    public static function ip($ip): self
    {
        return new self();
    }

    public function getCountryCode(): string
    {
        return 'ca';
    }

    public function getMetadata(): array
    {
        return [];
    }
}
