<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Opengis\LogVisits\Http\Middleware\LogVisitsMiddleware;
use Opengis\LogVisits\LogVisits;
use Opengis\LogVisits\Models\PageVisit;

test('model can be saved', function () {
    $this->assertCount(0, PageVisit::get());

    PageVisit::factory()->create(['browser' => 'Firefox']);

    $this->assertCount(1, PageVisit::get());
    $this->assertEquals('Firefox', PageVisit::first()->browser);
});


it('logs visits to the table', function () {
    $this->assertCount(0, PageVisit::get());

    LogVisits::logVisit('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36');

    $this->assertCount(1, PageVisit::get());
    $this->assertContains(PageVisit::first()->platform, ['Win10', 'Unknown']);
});

test('the middleware logs visits', function () {
    $request = new Request();

    (new LogVisitsMiddleware())->handle($request, function ($request) {
    });

    $this->assertCount(1, PageVisit::get());
    $this->assertEquals('127.0.0.1', PageVisit::first()->ip);
});

it('updates browsecap automatically if cache has been cleared', function () {
    Artisan::call('cache:clear');

    $this->assertCount(0, PageVisit::get());

    LogVisits::logVisit('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36');

    $this->assertCount(1, PageVisit::get());
    $this->assertContains(PageVisit::first()->platform, ['Win10', 'Unknown']);
});
