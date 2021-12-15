<?php

namespace Opengis\LogVisits\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Opengis\LogVisits\Models\PageVisit;

class PageVisitFactory extends Factory
{
    protected $model = PageVisit::class;

    public function definition()
    {

        return [
            'full_url' => $this->faker->url(),
            'ip' => $this->faker->ipv4(),
            'request_method' => 'GET',
            'browser' => 'Chrome',
            'platform' => 'Win10',
            'browser_metadata' => '{"parent": "Chrome 96.0", "browser": "Chrome", "comment": "Chrome 96.0", "version": "96.0", "istablet": "", "platform": "Win10", "device_type": "Desktop", "ismobiledevice": "", "browser_name_regex": "~^mozilla/5.0 (.*windows nt 10.0.*) applewebkit.* (.*khtml.*like.*gecko.*) chrome/96.0.*safari/.*$~", "browser_name_pattern": "Mozilla/5.0 (*Windows NT 10.0*) applewebkit* (*khtml*like*gecko*) Chrome/96.0*Safari/*"}',
            'server_vars' => '{"full_url": "https://domain.test", "x_real_ip": null, "http_accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9", "remote_addr": "10.0.2.2", "http_referer": null, "query_string": "", "request_method": "GET", "http_connection": "keep-alive", "http_user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36", "server_software": "nginx/1.21.4", "http_accept_charset": null, "http_accept_encoding": "gzip, deflate, br", "http_accept_language": "en-US,en;q=0.9,fr;q=0.8", "http_x_forwarded_for": null}',
            'ip_metadata' => '{"ip": "10.0.2.2", "zip": null, "city": null, "type": "ipv4", "currency": {"code": null, "name": null, "plural": null, "symbol": null, "symbol_native": null}, "hostname": null, "latitude": null, "location": {"is_eu": null, "capital": null, "languages": null, "geoname_id": null, "calling_code": null, "country_flag": null, "country_flag_emoji": null, "country_flag_emoji_unicode": null}, "longitude": null, "time_zone": {"id": null, "code": null, "gmt_offset": null, "current_time": null, "is_daylight_saving": null}, "connection": {"asn": null, "isp": null}, "region_code": null, "region_name": null, "country_code": null, "country_name": null, "continent_code": null, "continent_name": null}',
            'visited_at' => now(),
        ];
    }
}
