<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');
Route::get('downloadfile', 'HomeController@downloadfile');
Route::get('/upload', 'UploadController@index');
Route::post('uploadfiles', ['as' => 'files.upload', 'uses' => 'UploadController@upload'])->before('ban');

