<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

// First route
Route::get('secure', 'CartController@stripeSCA')->name('cart.new');
// Stripe.js is called before post route
Route::post('charge', 'CartController@check')->name('cart.charge');


