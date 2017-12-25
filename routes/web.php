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

Route::get('/{denomination}', 'HomeController@index');
Route::get('/', 'HomeController@index');
Route::get('/api/nearbyChurchesView', 'MainController@nearbyChurchesView');

Route::get('/api/{controller}/{function}', 'APIController@json');
Route::get('/api/{function}', 'APIController@jsonDefaultController');

Route::resource('church', 'ChurchController');
Route::resource('denomination', 'DenominationController');
Route::resource('region', 'RegionController');