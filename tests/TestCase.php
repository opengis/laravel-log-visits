<?php

namespace Opengis\LogVisits\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Opengis\LogVisits\LogVisits;
use Opengis\LogVisits\LogVisitsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Opengis\\LogVisits\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        config(['log-visits.browscap-version' => 'lite']);

        LogVisits::updateBrowscap();
    }

    protected function getPackageProviders($app)
    {
        return [
            LogVisitsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();

        $migration = include __DIR__.'/../database/migrations/create_page_visits_log_table.php.stub';
        $migration->up();
    }
}
