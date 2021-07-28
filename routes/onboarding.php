<?php

/* --------------------------------------------------------
 * Start onboarding flow
 * ----------------------------------------------------- */
Route::get( '/begin', 'OnboardingController@begin' );

/* --------------------------------------------------------
 * After integration "accepted"
 * ----------------------------------------------------- */
Route::get( '/authorize/{integrationToken}', 'OnboardingController@authorizeIntegration' )->name( 'authorize-integration' );
