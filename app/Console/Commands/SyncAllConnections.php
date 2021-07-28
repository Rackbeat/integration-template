<?php

namespace App\Console\Commands;

use App\Connection;
use Illuminate\Console\Command;

class SyncAllConnections extends Command
{
    protected $signature = 'sync:all';

    protected $description = 'Sync all active connections';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Connection $connection */
        foreach ( Connection::active()->get() as $connection ) {
            $connection->startSync();
        }
    }
}
