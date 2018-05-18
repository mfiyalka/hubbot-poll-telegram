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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/telegram/callback', 'AuthController@handleTelegramCallback')->name('auth.telegram.handle');

Route::group(['middleware'	=>	'telegramAuth'], function () {
    Route::get('/result/{id}', 'ResultController@index');
    Route::get('/polls', 'PollController@index');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
});
