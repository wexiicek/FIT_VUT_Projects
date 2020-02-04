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

use Illuminate\Http\Request;

Auth::routes(['verify' => true]);

Route::get('/', 'EventController@showInstances')->name('home');
Route::get('filter', 'EventController@filter_results')->name('filter');
Route::get('events', 'EventController@showEvents')->name('events');
Route::post('events/create', 'EventController@createEvent')->name('create_event_post');
Route::get('events/create', 'EventController@indexEvents')->name('create_event_get')->middleware('director');
Route::post('events/add', 'EventController@createInstance')->name('create_event_instance_post');
Route::get('events/add', 'EventController@indexInstances')->name('create_event_instance_get');
Route::get('event/{id}', 'EventController@showEvent')->name('show_event');
Route::get('event/{id}/get', 'EventController@getEvent')->name('get_event');
Route::post('event/{id}/delete', 'EventController@deleteEvent')->name('delete_event');
Route::get('event/{id}/getInstance', 'EventController@getInstance')->name('get_instance');
Route::post('event/{id}/update', 'EventController@updateEvent')->name('update_event');
Route::post('event/{id}/deleteInstance', 'EventController@deleteInstance')->name('delete_instance');
Route::post('event/{id}/updateInstance', 'EventController@updateInstance')->name('update_instance');

Route::get('user/{username}','UserProfileController@show')->name('user_profile')->middleware('verified');
Route::post('user/{username}/update', 'UserProfileController@update')->name('user_profile_update')->middleware('verified');
Route::post('/user/{username}/cancel_ticket', 'UserProfileController@cancelTicket')->name('cancel_ticket_post');
Route::get('/user/{username}/get', 'UserProfileController@returnUser')->name('fetch_user');
Route::post('/user/{username}/delete', 'UserProfileController@deleteUser')->name('delete_user_post');

Route::get('event/{id}/buy', 'TicketController@index')->name('buy_ticket_get');
Route::post('event/{id}/buy', 'TicketController@purchaseTicket')->name('buy_ticket_post');

Route::get('room/create', 'RoomController@index')->name('create_room');
Route::post('room/create', 'RoomController@create')->name("create_room_post");
Route::get('room/{id}/get', 'RoomController@getRoom')->name('get_room');
Route::post('room/{id}/update', 'RoomController@updateRoom')->name('update_room');
Route::post('/event/{id}/seat', 'RoomController@reserveSeat')->name("reserve_seat_post");
Route::post('/event/{id}/cancel_seats', 'RoomController@cancelSeats')->name('cancel_seat_post');
Route::post('room/{id}/delete', 'RoomController@deleteRoom')->name('delete_room')->middleware('director');

Route::get('admin', 'AdminController@index')->name('admin_panel')->middleware('admin');
Route::get('manage_tickets', 'AdminController@manageTickets')->name('manage_tickets')->middleware('cashier');
Route::get('manage_users', 'AdminController@manageUsers')->name('manage_users')->middleware('admin');
Route::get('manage_events', 'AdminController@manageEvents')->name('manage_events')->middleware('director');
Route::get('manage_rooms', 'AdminController@manageRooms')->name('manage_rooms')->middleware('director');
Route::get('manage_instances', 'AdminController@manageInstances')->name('manage_instances')->middleware('director');
Route::post('confirm_ticket', 'AdminController@confirmTicket')->middleware('cashier');
Route::post('create_user', 'AdminController@user_create')->name('create_user')->middleware('admin');



