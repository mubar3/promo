<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/get_promodata', [App\Http\Controllers\Controller::class, 'get_promodata']);
Route::get('/add_istimewa/{id}', [App\Http\Controllers\Controller::class, 'add_istimewa']);
Route::get('/add_biasa/{id}', [App\Http\Controllers\Controller::class, 'add_biasa']);
Route::get('/get_pass212', [App\Http\Controllers\Controller::class, 'get_pass']);
Route::post('/add_promo', [App\Http\Controllers\Controller::class, 'add_promo']);
Route::get('/promo', [App\Http\Controllers\Controller::class, 'get_promo']);
Route::post('/promo', [App\Http\Controllers\Controller::class, 'edit_promo']);
Route::delete('/promo/{delete}', [App\Http\Controllers\Controller::class, 'delete_promo']);

