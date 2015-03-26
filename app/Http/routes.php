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


Route::get('/', 'CollectionController@index');
Route::get('/refresh/{user}', 'CollectionController@refreshCollection');

Route::get('/search', 'SearchController@index');

Route::get('/game/stats/{id}', 'CollectionController@getGameInfo');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
