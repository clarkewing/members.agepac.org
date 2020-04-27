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

Route::post('/locked-threads/{thread}', 'LockedThreadsController@store')->name('threads.lock');
Route::delete('/locked-threads/{thread}', 'LockedThreadsController@destroy')->name('threads.unlock');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')->name('threads.subscribe');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')->name('threads.unsubscribe');

Route::get('/threads/{channel}/{thread}/replies', 'RepliesController@index')->name('replies.index');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store')->name('replies.store');
Route::apiResource('replies', 'RepliesController')->only([
    'update', 'destroy',
]);

Route::post('/replies/{reply}/best', 'BestRepliesController@store')->name('replies.mark_best');

Route::post('/replies/{reply}/favorites', 'FavoritesController@store')->name('replies.favorite');
Route::delete('/replies/{reply}/favorites', 'FavoritesController@destroy')->name('replies.unfavorite');

Route::get('/profiles/{user}', 'ProfilesController@show')->name('profiles.show');

Route::apiResource('notifications', 'UserNotificationsController')->only(['index', 'destroy']);

Route::get('/api/users', 'Api\UsersController@index');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store');
