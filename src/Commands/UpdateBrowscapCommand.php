<?php

namespace Opengis\LogVisits\Commands;

use Illuminate\Console\Command;
use Opengis\LogVisits\LogVisits;

class UpdateBrowscapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log-visits:update-browscap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the browscap file cache from the latest file available';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating browscap file...');

        LogVisits::updateBrowscap();

        $this->info('Browscap file up to date and cached.');

        return 0;
    }
}
