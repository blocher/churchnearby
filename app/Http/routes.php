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

Route::get('/', 'HomeController@index');

Route::get('/json/{controller}/{function}', 'ApiController@json');
Route::get('/json/{function}', 'ApiController@jsonDefaultController');

Route::get('/jsonp/{controller}/{function}', 'ApiController@jsonp');
Route::get('/jsonp/{function}', 'ApiController@jsonpDefaultController');


Route::get('/api/nearbyChurches', 'HomeController@apiNearbyChurches');
Route::get('/api/nearbyChurchesView', 'HomeController@apiNearbyChurchesView');

Route::resource('church', 'ChurchController');
Route::resource('denomination', 'DenominationController');
Route::resource('region', 'RegionController');
