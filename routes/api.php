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
        Route::get('routes', 'Api\IndexController@showRoutes');
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user/permissions', 'Api\AuthController@getAllPermissionsAttribute');
    });
});

Route::group(
    [
        'middleware' => 'auth:api'
    ],
    function () {
        Route::get('user', 'Api\AuthController@user');
        Route::get('user/permissions', 'Api\AuthController@getAllPermissionsAttribute');
    }
);

Route::get('routetest', 'Api\IndexController@showRoutesGroup');

Route::group(
    [
        'as' => 'index-',
        'middleware' => 'auth:api'
    ],
    function () {
        Route::name('dashboard')->middleware(["can:dashboard"])->get('/dashboard', 'Api\IndexController@getDashboard');
        Route::name('users')->middleware(["can:user-list"])->get('/users', 'Api\IndexController@getAllUsers');
        Route::name('roles')->middleware(["can:role-list"])->get('/roles', 'Api\IndexController@getAllRoles');
        Route::name('permissions')->middleware(["can:perm-list"])->get('/permissions', 'Api\IndexController@getPermissions');
        Route::name('account')->get('account', 'Api\AuthController@user');
    }
);

Route::group(
    [
        'prefix' => 'users',
        'middleware' => 'auth:api'
    ],
    function () {
        Route::middleware(["can:user-list"])->get('/users/{id}', 'Api\IndexController@getUser');
        Route::middleware(["can:user-create"])->post('create', 'Api\UserController@createUser');
        Route::middleware(["can:user-edit"])->put('{id}/edit', 'Api\UserController@editUser');
        Route::middleware(["can:user-delete"])->delete('{id}/delete', 'Api\UserController@deleteUser');
    }
);

Route::group(
    [
        'prefix' => 'roles',
        'middleware' => 'auth:api'
    ],
    function () {
        Route::middleware(["can:role-list"])->get('/{id}', 'Api\IndexController@getRole');
        Route::middleware(["can:role-create"])->post('create', 'Api\RoleController@createRole');
        Route::middleware(["can:role-edit"])->put('{id}/edit', 'Api\RoleController@editRole');
        Route::middleware(["can:role-delete"])->delete('{id}/delete', 'Api\RoleController@deleteRole');
    }
);

Route::middleware(['role: "Super Admin"'])->get('/hello', function (Request $request) {
    dd($request);
    return ["message" => "hello"];
});
