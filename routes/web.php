<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::redirect('/', '/login');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('threads', 'ThreadsController')->only([
    'create', 'store',
]);
Route::get('/threads/search', 'SearchController@show')->name('threads.search');
Route::get('/threads/{channel?}', 'ThreadsController@index')->name('threads.index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update')->name('threads.update');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy')->name('threads.destroy');

Route::post('/locked-threads/{thread}', 'LockedThreadsController@store')->name('locked-threads.store');
Route::delete('/locked-threads/{thread}', 'LockedThreadsController@destroy')->name('locked-threads.destroy');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')->name('thread-subscriptions.store');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')->name('thread-subscriptions.destroy');

Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
Route::patch('/replies/{reply}', 'RepliesController@update');
Route::delete('/replies/{reply}', 'RepliesController@destroy')->name('replies.destroy');

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('best-replies.store');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profile');
Route::get('/profiles/{user}/notifications', 'UserNotificationsController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationsController@destroy');

Route::get('/api/users', 'Api\UsersController@index');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store');
