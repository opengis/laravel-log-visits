<?php

namespace Opengis\LogVisits\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Opengis\LogVisits\LogVisits;
use Opengis\LogVisits\Models\PageVisit;

class LogPageVisitJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user_agent;

    public $server_vars;

    public $ip;

    public $user;

    public $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($serverVars, $ip, $user, $date, $user_agent = null)
    {
        $this->server_vars = $serverVars;
        $this->ip = $ip;
        $this->user = $user;
        $this->user_agent = $user_agent ? $user_agent : $this->server_vars['server']['HTTP_USER_AGENT'];
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $browserInfo = LogVisits::getBrowser($this->user_agent);

        $ip_metadata = LogVisits::getIpMetadata($this->ip);

        PageVisit::create([
            'full_url' => $this->server_vars['full_url'],
            'ip' => $this->ip,
            'request_method' => $this->server_vars['server']['REQUEST_METHOD'],
            'browser' => $browserInfo['browser'],
            'platform' => $browserInfo['platform'],
            'browser_metadata' => json_encode($browserInfo),
            'server_vars' => json_encode($this->server_vars),
            'ip_metadata' => json_encode($ip_metadata),
            'user_id' => optional($this->user)->id,
            'visited_at' => $this->date,
        ]);
    }
}
