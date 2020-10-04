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

/* Threads */
Route::resource('threads', 'ThreadsController')->only(['create', 'store']);
Route::get('/threads/search', 'SearchController@show')->name('threads.search');
Route::get('/threads/{channel?}', 'ThreadsController@index')->name('threads.index');
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show')->name('threads.show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update')->name('threads.update');
Route::delete('/threads/{channel}/{thread}', 'ThreadsController@destroy')->name('threads.destroy');

Route::post('/locked-threads/{thread}', 'LockedThreadsController@store')->name('threads.lock');
Route::delete('/locked-threads/{thread}', 'LockedThreadsController@destroy')->name('threads.unlock');

Route::post('pinned-threads/{thread}', 'PinnedThreadsController@store')->name('threads.pin');
Route::delete('pinned-threads/{thread}', 'PinnedThreadsController@destroy')->name('threads.unpin');

Route::post('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@store')->name('threads.subscribe');
Route::delete('/threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionsController@destroy')->name('threads.unsubscribe');

/* Posts */
Route::get('/threads/{channel}/{thread}/posts', 'PostsController@index')->name('posts.index');
Route::post('/threads/{channel}/{thread}/posts', 'PostsController@store')->name('posts.store');
Route::apiResource('posts', 'PostsController')->only(['update', 'destroy']);

Route::post('/posts/{post}/best', 'BestPostsController@store')->name('posts.mark_best');
Route::delete('/posts/{post}/best', 'BestPostsController@destroy')->name('posts.unmark_best');

Route::post('/posts/{post}/favorites', 'FavoritesController@store')->name('posts.favorite');
Route::delete('/posts/{post}/favorites', 'FavoritesController@destroy')->name('posts.unfavorite');

/* Profiles */
Route::get('/profiles/{user}', 'ProfilesController@show')->name('profiles.show');

/* Notifications */
Route::apiResource('notifications', 'UserNotificationsController')->only(['index', 'destroy']);

/* Polls */
Route::get('/poll/{poll}', 'PollsController@show')->name('polls.show');
Route::get('/threads/{channel}/{thread}/poll', 'PollsController@index')->name('polls.index');
Route::get('/threads/{channel}/{thread}/poll/create', 'PollsController@create')->name('polls.create');
Route::post('/threads/{channel}/{thread}/poll', 'PollsController@store')->name('polls.store');
Route::post('/poll/{poll}/lock', 'PollsController@lock')->name('polls.lock');
Route::put('/poll/{poll}/update', 'PollsController@update')->name('polls.update');

Route::get('/threads/{channel}/{thread}/poll/options', 'PollOptionsController@index')->name('poll_options.index');
Route::post('/threads/{channel}/{thread}/poll/poll-option', 'PollOptionsController@store')->name('poll_options.store');
Route::put('/poll-option/{pollOption}/update', 'PollOptionsController@update')->name('poll_options.update');
Route::delete('/poll-option/{pollOption}/delete', 'PollOptionsController@destroy')->name('poll_options.destroy');

Route::get('/threads/{channel}/{thread}/poll/vote', 'PollVotesController@show')->name('poll_votes.show');
Route::get('/threads/{channel}/{thread}/poll/vote/create', 'PollVotesController@create')->name('poll_votes.create');
Route::post('/poll/{poll}/vote/{pollOption}', 'PollVotesController@store')->name('poll_votes.store');
Route::delete('/poll-vote/{pollVote}', 'PollVotesController@destroy')->name('poll_votes.destroy');

Route::get('/threads/{channel}/{thread}/poll/votes', 'PollVotesController@index')->name('poll_votes.index');
Route::get('/threads/{channel}/{thread}/poll/results', 'PollsController@results')->name('poll.results');

/* Api */
Route::namespace('Api')->prefix('/api')->name('api.')->group(function () {
    Route::get('/users', 'UsersController@index')->name('users.index');

    Route::get('/user-invitations', 'UserInvitationsController@index')->name('user-invitations.index');

    Route::post('/users/{user}/avatar', 'UserAvatarController@store')->name('users.avatar.store');
});
