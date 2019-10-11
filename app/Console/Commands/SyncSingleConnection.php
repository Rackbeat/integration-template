<?php

namespace App\Console\Commands;

use App\Connection;
use App\Jobs\ExampleSyncJob;
use Illuminate\Console\Command;

class SyncSingleConnection extends Command
{
	protected $signature = 'sync:single {--connection=*}';

	protected $description = 'Sync single connections';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		/** @var Connection $connection */
		$connection = Connection::where( 'id', $this->option( 'connection' ) )->firstOrFail();
		$connection->startSync();
	}
}
