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
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
});

Route::group([
    'middleware' => 'jwt.verify:Admin,User',
], function ($router) {
	Route::get('tasks', 'TaskController@index');
	Route::post('tasks', 'TaskController@store');
	Route::put('tasks/{id}', 'TaskController@update');
	Route::delete('tasks/{id}', 'TaskController@delete');
});

Route::group([
    'middleware' => 'jwt.verify:Admin,Manager',
], function ($router) {
	Route::get('users', 'UserController@index');
	Route::post('users', 'UserController@store');
	Route::put('users/{id}', 'UserController@update');
	Route::delete('users/{id}', 'UserController@delete');
});

Route::group([
    'middleware' => 'jwt.verify:Admin,Manager,User',
], function ($router) {
	Route::get('profile', 'ProfileController@index');
	Route::put('profile', 'ProfileController@update');
});

