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

Route::get('export', 'UsersController@export')->name('export');
Route::get('importExportView', 'UsersController@importExportView');
Route::post('importLibrary', 'UsersController@importLibrary')->name('importLibrary');

Route::post('importViewNoLibrary', 'UsersController@importNoLibrary')->name('importNoLibrary');
Route::get('viewNoLibrary', 'UsersController@viewNoLibrary')->name('viewNoLibrary');

Route::get('pagination', 'PaginationController@index');
