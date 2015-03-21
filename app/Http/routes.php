<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', 'CollectionController@showCollection');
Route::get('/refresh/{user}', 'CollectionController@refreshCollection');

Route::get('home', 'HomeController@index');

Route::get('/search', 'SearchController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
