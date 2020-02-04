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


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/flights', 'SearchController@search')->name('search_flights');
Route::post('/book', 'OrderController@book')->name('book_flight');
Route::get('/profile/{username}', 'UserController@index')->name('profile');
Route::post('/profile/{username}/edit', 'UserController@edit')->name('profile_edit');
Route::post('/profile/{username}/address', 'UserController@edit_address')->name('profile_edit_address');
Route::post('/profile/{username}/passenger', 'UserController@add_passenger')->name('passenger_create');
Route::get('/passenger', 'UserController@get_passenger')->name('get_passenger');
Route::get('/thanks', 'OrderController@thanks')->name('thanks');
