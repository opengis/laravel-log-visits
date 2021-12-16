<?php

use Opengis\LogVisits\LogVisits;
use Opengis\LogVisits\Models\PageVisit;

it('can fetch ip metadata from ipstack', function () {
    config(['log-visits.ip-metadata-service' => 'ipstack']);
    config(['log-visits.ipstack.url' => 'https://api.ipstack.com']);
    config(['log-visits.ipstack.key' => env('IPSTACK_KEY')]);

    $this->assertCount(0, PageVisit::get());

    LogVisits::logVisit('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36', '37.19.213.105');

    $this->assertCount(1, PageVisit::get());
    $this->assertContains(PageVisit::first()->platform, ['Win10', 'Unknown']);
    $this->assertTrue(isset(collect(json_decode(PageVisit::first()->ip_metadata))->toArray()['ip']));
    $this->assertEquals('37.19.213.105', collect(json_decode(PageVisit::first()->ip_metadata))->toArray()['ip']);

    if (env('IPSTACK_KEY')) {
        $this->assertEquals('CA', collect(json_decode(PageVisit::first()->ip_metadata))->toArray()['country_code']);
    }
});

it('can fetch country from ipstack', function () {
    config(['log-visits.ip-metadata-service' => 'ipstack']);
    config(['log-visits.ipstack.url' => 'https://api.ipstack.com']);
    // config(['log-visits.ipstack.key' => '']);

    $this->assertEquals('us', LogVisits::getCountryCode());
});
