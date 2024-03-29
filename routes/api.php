<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * admin routes
 */
Route::group([], function () {
    Route::apiResource('admin',  App\Http\Controllers\AdminController::class);
    // Route::post('/update-password',  [\App\Http\Controllers\ResetPasswordController::class, 'updatePassword']);
    // Route::post('/reset-link',  [\App\Http\Controllers\ResetPasswordController::class, 'sendResetLink']);
    // Route::post('/reset',  [\App\Http\Controllers\ResetPasswordController::class, 'reset']);
    Route::post('/admin-login',  [\App\Http\Controllers\AdminController::class, 'adminLogin']);
    Route::post('/admin-logout',  [\App\Http\Controllers\ClientController::class, 'adminLogout']);
});

/**
 * client routes
 */
Route::group([], function () {
    Route::apiResource('clients',  App\Http\Controllers\ClientController::class);
    Route::post('/update-password',  [\App\Http\Controllers\ResetPasswordController::class, 'updatePassword']);
    Route::post('/reset-link',  [\App\Http\Controllers\ResetPasswordController::class, 'sendResetLink']);
    Route::post('/reset',  [\App\Http\Controllers\ResetPasswordController::class, 'reset']);
    Route::post('/client-login',  [\App\Http\Controllers\ClientController::class, 'clientLogin']);
    Route::post('/client-logout',  [\App\Http\Controllers\ClientController::class, 'clientLogout']);
    Route::post('/update-meta',  [\App\Http\Controllers\ClientController::class, 'createOrUpdateMeta']);
    Route::post('/upload-photo',  [\App\Http\Controllers\ClientController::class, 'photoUpload']);
    Route::post('/upload-profile-picture',  [\App\Http\Controllers\ClientController::class, 'uploadProfilePic']);
    Route::get('/auth-user',  [\App\Http\Controllers\ClientController::class, 'authUser']);
    Route::put('/update-auth-user',  [\App\Http\Controllers\ClientController::class, 'updateAuthUser']);
    Route::get('/photos/{username}',  [\App\Http\Controllers\ClientController::class, 'getPhotos']);
});

/**
 * messages routes
 */

Route::group([], function () {
    Route::apiResource('threads',  App\Http\Controllers\MessagesController::class);
    Route::post('threads/{id}',  [App\Http\Controllers\MessagesController::class, 'update']);
    Route::get('/get-threads', [\App\Http\Controllers\MessagesController::class, 'getThreads']);
});


/**
 * notifications routes
 */

Route::group([], function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationsController::class, 'getNotifications']);
});


/**
 * views routes
 */

Route::group([], function () {
    Route::get('/viewers', [\App\Http\Controllers\ViewsController::class, 'getViewers']);
});


/**
 * likes routes
 */

Route::group([], function () {
    Route::get('/likers', [\App\Http\Controllers\LikeController::class, 'getLikers']);
    Route::get('/likes', [\App\Http\Controllers\LikeController::class, 'getLikes']);
    Route::post('/like', [\App\Http\Controllers\LikeController::class, 'like']);
    Route::post('/unlike', [\App\Http\Controllers\LikeController::class, 'unlike']);
});


/**
 * likes routes
 */

Route::group([], function () {
    Route::get('/likers', [\App\Http\Controllers\LikeController::class, 'getLikers']);
    Route::get('/likes', [\App\Http\Controllers\LikeController::class, 'getLikes']);
    Route::post('/like', [\App\Http\Controllers\LikeController::class, 'like']);
    Route::post('/unlike', [\App\Http\Controllers\LikeController::class, 'unlike']);
});


/**
 * friend routes
 */

Route::group([], function () {
    Route::get('/friends/{username}', [\App\Http\Controllers\FriendController::class, 'getFriends']);
    Route::get('/friends', [\App\Http\Controllers\FriendController::class, 'getAuthUserFriends']);
    Route::get('/friend-requests', [\App\Http\Controllers\FriendController::class, 'getFriendRequests']);
    Route::get('/my-friend-requests', [\App\Http\Controllers\FriendController::class, 'myFriendRequests']);
    Route::post('/send-friend-request', [\App\Http\Controllers\FriendController::class, 'sendFriendRequest']);
    Route::post('/accept-friend-request', [\App\Http\Controllers\FriendController::class, 'acceptFriendRequest']);
    Route::post('/reject-friend-request', [\App\Http\Controllers\FriendController::class, 'rejectFriendRequest']);
});

Route::get('/test', [App\Http\Controllers\ClientController::class, 'test']);
