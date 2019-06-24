<?php

use Illuminate\Http\Request;

Route::post('webhooks/order/shipped/{useraccountid}/{connection}', 'WebhooksController@orderShipped')->name('webhooks.order-shipped');
Route::post('webhooks/purchaseorder/updated/{useraccountid}/{connection}', 'WebhooksController@purchaseOrderUpdated')->name('webhooks.purchaseorder-updated');