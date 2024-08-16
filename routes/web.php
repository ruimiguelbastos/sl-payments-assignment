<?php

use App\Http\Controllers\ProductsSubscriptionsGetHandler;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/products-subscriptions', ProductsSubscriptionsGetHandler::class);