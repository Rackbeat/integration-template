<?php

namespace App;

use App\Economic\Soap;
use App\Jobs\ExampleSyncJob;
use App\Jobs\SyncCustomers;
use App\Jobs\SyncPaymentTerms;
use App\Jobs\SyncProducts;
use App\Jobs\SyncSuppliers;
use App\Jobs\SyncUnits;
use App\Rackbeat\Client;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LasseRafn\Economic\Economic;

class Connection extends Model
{
	protected $guarded = [];

	protected $casts = [
		'is_active' => 'boolean',
	];

	public function getRouteKeyName() {
		return 'internal_token';
	}

	public function startSync() {
		dispatch( new ExampleSyncJob( $this ) );
	}

	public function rackbeat() {
		return new Client( $this->rackbeat_token );
	}

	public function refreshRackbeatToken() {
		$tokenResponse = $this->rackbeat()->refreshToken();

		$this->update( [ 'rackbeat_token' => $tokenResponse->token ] );
	}

	public function getSetting( $identifier, $default = null ) {
		if ( $setting = $this->connectionSettings()->whereIdentifier( $identifier )->first() ) {
			return $setting->value;
		}

		return $default;
	}

	public function getSettingFromValue( $identifier, $value = null, $default = null ) {
		if ( $setting = $this->connectionSettings()->where( 'identifier', 'LIKE', "{$identifier}%" )->whereValue( $value )->first() ) {
			return str_replace( $identifier, '', $setting->identifier );
		}

		return $default;
	}

	public function setSetting( $identifier, $value ) {
		return $this->connectionSettings()->updateOrCreate( [
			'identifier' => $identifier
		], [
			'value' => $value
		] );
	}

	public function connectionSettings() {
		return $this->hasMany( ConnectionSetting::class );
	}

	public function scopeActive( Builder $builder ) {
		return $builder->whereIsActive( 1 );
	}
}
