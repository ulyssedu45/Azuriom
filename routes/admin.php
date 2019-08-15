<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" and "admin" middleware groups. Now create a great admin panel !
|
*/

Route::get('/', 'AdminController@index')->name('dashboard');

Route::resource('users', 'UserController')->except('show');

Route::resource('posts', 'PostController')->except('show');
Route::resource('pages', 'PageController')->except('show');