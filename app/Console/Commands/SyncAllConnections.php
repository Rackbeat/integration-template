<?php

namespace App\Console\Commands;

use App\Connection;
use App\Jobs\ExampleSyncJob;
use App\Jobs\SyncAllPurchaseOrders;
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
	public function handle() {
		foreach ( Connection::active()->get() as $connection ) {
			dispatch( new SyncAllPurchaseOrders( $connection ) );
		}
	}
}
