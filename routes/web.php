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

// Route::get('/auth/login', 'App\Http\Controllers\Auth\AuthenticationController@show_login_form')->name('signin');
Route::get('/auth/login', [App\Http\Controllers\Auth\AuthenticationController::class, 'show_login_form'])->name('login-page');
Route::post('/user/login', [App\Http\Controllers\Auth\AuthenticationController::class, 'user_login'])->name('user.login');

// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
