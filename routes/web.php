<?php

require base_path('routes/onboarding.php');

//Route::get('/', function () {
//});

Route::get('/settings/{userAccountId}/{connection}', 'ConnectionSettingsController@view')->name('settings-primecargo');
Route::post('/settings/{userAccountId}/{connection}', 'ConnectionSettingsController@store')->name('settings-post');
