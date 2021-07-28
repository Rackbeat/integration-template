<?php namespace App\Jobs;

use App\Connection;
use App\Map\CustomerGroupMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LasseRafn\Economic\Models\CustomerGroup;

class ExampleSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Connection */
    protected $integrationConnection;

    public function __construct( Connection $integrationConnection )
    {
        $this->integrationConnection = $integrationConnection;
    }

    public function handle()
    {
        // todo...
    }
}
