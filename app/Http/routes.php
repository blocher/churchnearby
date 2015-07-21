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

Route::get('/{denomination}', 'HomeController@index');
Route::get('/', 'HomeController@index');
Route::get('/api/nearbyChurchesView', 'MainController@nearbyChurchesView');

Route::get('/api/{controller}/{function}', 'APIController@json');
Route::get('/api/{function}', 'APIController@jsonDefaultController');

Route::resource('church', 'ChurchController');
Route::resource('denomination', 'DenominationController');
Route::resource('region', 'RegionController');
