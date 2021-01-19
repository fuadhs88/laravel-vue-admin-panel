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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Api\AuthController@login');
    Route::middleware("firstrun")->post('signup', 'Api\AuthController@signup');

    // handle reset password form process
    Route::post('reset-password', 'AuthController@sendPasswordResetLink');

    Route::post('reset/password', 'AuthController@callResetPassword');

    Route::get('installed', 'Api\AuthController@isInstalled');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\AuthController@user');
        Route::get('user/permissions', 'Api\AuthController@getAllPermissionsAttribute');
    });
});

Route::middleware(['role: "Super Admin"'])->get('/hello', function (Request $request) {
    dd($request);
    return ["message" => "hello"];
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
