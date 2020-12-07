<?php

use App\Http\Controllers\AccountInfoController;
use App\Http\Controllers\Api\TagsController;
use App\Http\Controllers\Api\UserAvatarController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\BestPostsController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LockedThreadsController;
use App\Http\Controllers\PinnedThreadsController;
use App\Http\Controllers\PollResultsController;
use App\Http\Controllers\PollsController;
use App\Http\Controllers\PollVotesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionInvoicesController;
use App\Http\Controllers\ThreadsController;
use App\Http\Controllers\ThreadSubscriptionsController;
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

Route::get('/home', [HomeController::class, 'index'])->name('home');

/* Threads */
Route::resource('threads', 'ThreadsController')->only(['create', 'store']);
Route::get('/threads/search', [SearchController::class, 'show'])->name('threads.search');
Route::get('/threads/{channel?}', [ThreadsController::class, 'index'])->name('threads.index');
Route::get('/threads/{channel}/{thread}', [ThreadsController::class, 'show'])->name('threads.show');
Route::patch('/threads/{channel}/{thread}', [ThreadsController::class, 'update'])->name('threads.update');
Route::delete('/threads/{channel}/{thread}', [ThreadsController::class, 'destroy'])->name('threads.destroy');

Route::post('/locked-threads/{thread}', [LockedThreadsController::class, 'store'])->name('threads.lock');
Route::delete('/locked-threads/{thread}', [LockedThreadsController::class, 'destroy'])->name('threads.unlock');

Route::post('pinned-threads/{thread}', [PinnedThreadsController::class, 'store'])->name('threads.pin');
Route::delete('pinned-threads/{thread}', [PinnedThreadsController::class, 'destroy'])->name('threads.unpin');

Route::post('/threads/{channel}/{thread}/subscriptions', [ThreadSubscriptionsController::class, 'store'])->name('threads.subscribe');
Route::delete('/threads/{channel}/{thread}/subscriptions', [ThreadSubscriptionsController::class, 'destroy'])->name('threads.unsubscribe');

/* Posts */
Route::get('/threads/{channel}/{thread}/posts', [PostsController::class, 'index'])->name('posts.index');
Route::post('/threads/{channel}/{thread}/posts', [PostsController::class, 'store'])->name('posts.store');
Route::apiResource('posts', 'PostsController')->only(['update', 'destroy']);

Route::post('/posts/{post}/best', [BestPostsController::class, 'store'])->name('posts.mark_best');
Route::delete('/posts/{post}/best', [BestPostsController::class, 'destroy'])->name('posts.unmark_best');

Route::post('/posts/{post}/favorites', [FavoritesController::class, 'store'])->name('posts.favorite');
Route::delete('/posts/{post}/favorites', [FavoritesController::class, 'destroy'])->name('posts.unfavorite');

Route::post('/attachments', [AttachmentsController::class, 'store'])->name('attachments.store');
Route::delete('/attachments/{attachment}', [AttachmentsController::class, 'destroy'])->name('attachments.destroy');

/* Polls */
Route::prefix('/threads/{channel}/{thread}/poll')->group(function () {
    Route::get('/', [PollsController::class, 'show'])->name('polls.show');
    Route::get('/create', [PollsController::class, 'create'])->name('polls.create');
    Route::post('/', [PollsController::class, 'store'])->name('polls.store');
    Route::put('/', [PollsController::class, 'update'])->name('polls.update');
    Route::delete('/', [PollsController::class, 'destroy'])->name('polls.destroy');

    Route::put('/vote', [PollVotesController::class, 'update'])->name('poll_votes.update');

    Route::get('/results', [PollResultsController::class, 'show'])->name('poll_results.show');
});

/* Profiles */
Route::apiResource('profiles', 'ProfilesController')->only(['index', 'show', 'update']);

Route::apiResource('occupations', 'OccupationsController')->only(['store', 'update', 'destroy']);
Route::apiResource('courses', 'CoursesController')->only(['store', 'update', 'destroy']);

/* Companies */
Route::apiResource('companies', 'CompaniesController')->except('destroy');

/* Notifications */
Route::apiResource('notifications', 'UserNotificationsController')->only(['index', 'destroy']);

/* Api */
Route::namespace('Api')->prefix('/api')->name('api.')->group(function () {
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');

    Route::post('/users/{user}/avatar', [UserAvatarController::class, 'store'])->name('users.avatar.store');

    Route::get('/tags/{type?}', [TagsController::class, 'index'])->name('tags.index');
});

/* Account */
Route::prefix('/account')->group(function () {
    Route::prefix('/info')->name('account.')->group(function () {
        Route::get('/', [AccountInfoController::class, 'edit'])->name('edit');
        Route::patch('/', [AccountInfoController::class, 'update'])->name('update');
    });

    Route::prefix('/subscription')->name('subscription.')->group(function () {
        Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        Route::get('/', [SubscriptionController::class, 'edit'])->name('edit');
        Route::patch('/', [SubscriptionController::class, 'update'])->name('update');

        Route::get('/invoice/{invoiceId}', [SubscriptionInvoicesController::class, 'show'])
            ->name('invoices.show');

        Route::resource('payment-methods', 'PaymentMethodsController')
            ->only(['create', 'store', 'update', 'destroy']);
    });
});
