<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/<token>/webhook', function() {
    Telegram::commandsHandler(true);

    $updates = Telegram::getWebhookUpdates();

    Log::info($updates);

    return 'ok';
});

Route::get('/test', function () {
    $response = Telegram::getUpdates();

    return $response;
});
