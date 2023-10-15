<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Auth\RegisterController;
// use App\Http\Controllers\Auth\LoginController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/register', [RegisterController::class, 'register']);
// Route::post('/login', [LoginController::class, 'login']);

Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login')->middleware('clear-expired-tokens');

Route::middleware('auth:sanctum')->group(function () {
    //LogOut user
    Route::get('/logout', 'AuthController@logout');

    //Users route
    Route::get('/user', 'UserController@profile');
    Route::post('/user/{id}', 'UserController@update');
    Route::post('/user/status/{id}', 'UserController@setStatus');
    Route::delete('/user/{id}', 'UserController@destroy');
    Route::get('/users', 'UserController@list');

    Route::post('/email', 'MailController@sendEmail');

    Route::post('/val', 'ExchangeController@valData');
});
