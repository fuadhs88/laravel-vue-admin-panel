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

/* if (!file_exists(storage_path('installed.json'))) {
    Route::middleware('firstrun')->group(function () {
        Auth::routes();
    });
} else {
    Auth::routes(["register" => false]);
} */

//Overrides standard method for logout, for logging out inside the vue spa
//Route::get('/logout', 'Auth\LoginController@logout', redirect('login'))->name('logout');

Route::get('/{any}', 'AppController@index')
    ->where('any', '^(?!api).*$')
    //->middleware('auth')
;

Route::get('/', 'HomeController@index')->name('home');
