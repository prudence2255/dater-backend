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
Route::group([], function(){
    Route::apiResource('admin',  App\Http\Controllers\AdminController::class);
    Route::post('/update-password',  [\App\Http\Controllers\ResetPasswordController::class, 'updatePassword']);
    Route::post('/reset-link',  [\App\Http\Controllers\ResetPasswordController::class, 'sendResetLink']);
    Route::post('/reset',  [\App\Http\Controllers\ResetPasswordController::class, 'reset']);
    Route::post('/admin-login',  [\App\Http\Controllers\AdminController::class, 'adminLogin']);
    Route::post('/admin-logout',  [\App\Http\Controllers\ClientController::class, 'adminLogout']);
});

/**
 * client routes
 */
Route::group([], function(){
    Route::apiResource('clients',  App\Http\Controllers\ClientController::class);
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


