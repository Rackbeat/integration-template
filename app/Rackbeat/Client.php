<?php namespace App\Rackbeat;

use App\Economic\Models\Order;
use GuzzleHttp\Client as curl;

class Client
{
	protected $curl;

	public const STATE_CREATED = 1;
	public const STATE_UPDATED = 2;

	public function __construct( $token = '' ) {
		$this->curl = new curl( [
			'base_uri' => config( 'rackbeat.endpoint' ),
			'headers'  => [
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'User-Agent'    => 'Internal ' . config('app.name') . ' e-conomic integration',
				'Authorization' => 'Bearer ' . $token
			]
		] );
	}

	public function self() {
		return json_decode( $this->curl->get( 'self' )->getBody()->getContents() );
	}

	public function refreshToken() {
		return json_decode( $this->curl->post( 'tokens/replace-current' )->getBody()->getContents() );
	}

	public function getSetting( $identifier, $default = null ) {
		try {
			return json_decode( $this->curl->get( "settings/{$identifier}" )->getBody()->getContents() )->value ?? $default;
		} catch ( \Exception $exception ) {
			return $default;
		}
	}

	public function setupPluginSettingsPage( $pluginSlug, $url ) {
		return json_decode( $this->curl->post( "plugins/{$pluginSlug}/set-settings-url", [
			'json' => [
				'settings_url' => $url
			]
		] )->getBody()->getContents() );
	}

	public function setupWebhook( $event, $url ) {
		return json_decode( $this->curl->post( 'webhooks', [
			'json' => [
				'event' => $event,
				'url'   => $url
			]
		] )->getBody()->getContents() );
	}

	public static function getToken( $code = '', $clientId = null, $clientSecret = null ) {
		$curl = new curl();

		try {
			$request = $curl->post( config( 'rackbeat.domain' ) . '/oauth/token', [
				'headers'     => [
					'Accept'       => 'application/json',
					'Content-Type' => 'application/x-www-form-urlencoded',
				],
				'form_params' => [
					'grant_type'    => 'authorization_code',
					'client_id'     => $clientId ?? config( 'rackbeat.client_id' ),
					'client_secret' => $clientSecret ?? config( 'rackbeat.client_secret' ),
					'code'          => $code
				],
			] );

			return json_decode( $request->getBody()->getContents() );
		} catch ( \Exception $exception ) {
			dd( $exception );
		}
	}

	public static function init( $token ) {
		return new self( $token );
	}
}