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


Route::get('routes', 'Api\IndexController@showRoutesGroup');

//Testing role middleware
Route::middleware(['auth:api', 'role:Super Admin'])->get('/hello', function () {
    return response()->json(["message" => "hello"]);
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::middleware("firstrun")->post('signup', 'Api\AuthController@signup');

    Route::post('login', 'Api\AuthController@login');

    Route::get('installed', 'Api\AuthController@isInstalled');

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'Api\AuthController@logout');
    });
});

Route::group(
    [
        'as' => 'index-',
        'middleware' => 'auth:api'
    ],
    function () {
        //Route::name('dashboard')->get('/dashboard', 'Api\IndexController@getDashboard');
        Route::name('users')->get('/users', 'Api\IndexController@getAllUsers');
        Route::name('roles')->middleware(["can:role-list"])->get('/roles', 'Api\IndexController@getAllRoles');
        Route::name('permissions')->middleware(["can:perm-list"])->get('/permissions', 'Api\IndexController@getPermissions');
    }
);

Route::group(
    [
        'as' => 'views-',
        'middleware' => 'auth:api'
    ],
    function () {
        Route::name('account')->get('account', 'Api\AuthController@user');
        Route::name('user')->get('user', 'Api\IndexController@getUser');
        Route::name('role')->middleware(["can:role-list"])->get('role', 'Api\IndexController@getRoleById');
    }
);

Route::middleware(["auth:api"])->get('account/permissions', 'Api\AuthController@getAllPermissionsAttribute');

Route::group(
    [
        'prefix' => 'user',
        'middleware' => 'auth:api'
    ],
    function () {
        Route::middleware(["can:user-create"])->post('create', 'Api\UserController@createUser');
        Route::middleware(["can:user-edit"])->put('edit', 'Api\UserController@editUser');
        Route::middleware(["can:user-delete"])->delete('delete', 'Api\UserController@deleteUser');
    }
);

Route::group(
    [
        'prefix' => 'role',
        'middleware' => 'auth:api'
    ],
    function () {
        Route::middleware(["can:role-create"])->post('create', 'Api\RoleController@createRole');
        Route::middleware(["can:role-edit"])->put('edit', 'Api\RoleController@editRole');
        Route::middleware(["can:role-delete"])->delete('delete', 'Api\RoleController@deleteRole');
    }
);
