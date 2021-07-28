<?php

namespace App\Console;

use App\Console\Commands\SyncAllConnections;
use App\Console\Commands\SyncSingleConnection;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncAllConnections::class,
        SyncSingleConnection::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule( Schedule $schedule )
    {
        $schedule->command( 'sync:all' )
                 ->everyThirtyMinutes();
    }
}
