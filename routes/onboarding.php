<?php

/* --------------------------------------------------------
 * Start onboarding flow
 * ----------------------------------------------------- */
Route::get( '/begin', function ( \Illuminate\Http\Request $request ) {
	// 'token' is a integration request token from RB.

	if ( ! $request->has( 'token' ) ) {
		return redirect()->back();
	}

	// todo start integration onboarding. Example:
	return redirect()->away(
		'https://some-integration-onboarding-endpoint.dev?redirect_uri='
		. route( 'authorize-integration', $request->get( 'token' ) )
	);
} );

/* --------------------------------------------------------
 * After integration "accepted"
 * ----------------------------------------------------- */
Route::get( '/authorize/{integrationToken}', function ( \Illuminate\Http\Request $request, $integrationToken ) {
	$integrationWasSetupCorrectly = true; // todo check if the integration (oauth?) response was valid.


	if ( ! $integrationWasSetupCorrectly ) {
		// If integration was not setup or oauth denied, we will cancel the RB request
		\App\Rackbeat\Integration::cancel( $integrationToken );

		return response( 'Something went wrong' );
	}

	// We will now approve the integration request from RB and get our permanent API token
	try {
		$accessTokenRequest = \App\Rackbeat\Integration::accept( $integrationToken );
		$accessToken        = $accessTokenRequest->access_token;
		$appSlug            = $accessTokenRequest->app_slug;
		$redirectTo         = $accessTokenRequest->redirect_to ?? null;
	} catch ( Exception $exception ) {
		return response( 'Something went wrong' );
	}

	// Update an existing, or create a new connection.
	$client = \App\Rackbeat\Client::init( $accessToken );

	$rackbeatSelf = $client->self();

	$connection = \App\Connection::updateOrCreate( [
		'rackbeat_user_account_id' => $rackbeatSelf->user_account->id
	], [
		'rackbeat_token' => $accessToken,
		// todo add integration specific keys to the connection (to the connections table)
		'internal_token' => str_random( 255 )
	] );

	// Consider: do we need a settings page inside Rackbeat? Is an iframe. See resources/views/settings.blade.php
	// $client->setupPluginSettingsPage( $appSlug, route( 'settings-integration', [ $connection->rackbeat_user_account_id, $connection->internal_token ] ) );

	// consider: do we need webhooks?
	// $client->setupWebhook( 'model.event', route( 'webhook.model-event', [ $connection->rackbeat_user_account_id, $connection->internal_token ] ) );

	// Redirect back to Rackbeat if redirectTo is not null
	if ( $redirectTo ) {
		return redirect()->away( $redirectTo );
	}

	return response( 'ok' );
} )->name( 'authorize-integration' );
