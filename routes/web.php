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

Auth::routes();

//Overrides standard method for logout, for logging out inside the vue spa
Route::get('/logout', function () {
    Auth::logout();
    return Redirect::to('login');
});

//Overrides register name
//[TODO] Redirect if first run already happened (middleware that checks if file exists)
Route::get('firstrun', 'Auth\RegisterController@showRegistrationForm')->name('firstrun');
Route::post('firstrun', 'Auth\RegisterController@register');

Route::get('/{any}', 'AppController@index')
    ->where('any', '.*')
    ->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home');
