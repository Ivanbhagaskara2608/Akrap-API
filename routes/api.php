<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', '\App\Http\Controllers\UserController@register');
Route::post('login', '\App\Http\Controllers\UserController@login');
Route::get('testing', '\App\Http\Controllers\UserController@test');

// admin
Route::prefix('admin')->middleware(['auth:api', 'isAdmin'])->group(function() {
    Route::get('schedule/all', '\App\Http\Controllers\ScheduleController@index');
    Route::post('schedule/add', '\App\Http\Controllers\ScheduleController@create');
    Route::post('schedule/store', '\App\Http\Controllers\ScheduleController@store');
    Route::post('schedule/update', '\App\Http\Controllers\ScheduleController@update');
    Route::post('schedule/delete', '\App\Http\Controllers\ScheduleController@destroy');
    Route::get('presence/show', '\App\Http\Controllers\PresenceController@show');
    // Route::post('presence/store', '\App\Http\Controllers\PresenceController@store');
    Route::post('presence/update', '\App\Http\Controllers\PresenceController@update');
    Route::get('getUsers', '\App\Http\Controllers\UserController@getAllUsers');
    Route::post('information/add', '\App\Http\Controllers\InformationController@create');
    Route::post('information/update', '\App\Http\Controllers\InformationController@update');
    Route::post('information/delete', '\App\Http\Controllers\InformationController@destroy');
});


// all users authenticated
Route::middleware(['auth:api'])->group(function() {
    Route::get('user', '\App\Http\Controllers\UserController@index');
    Route::post('user/updateUsername', '\App\Http\Controllers\UserController@updateUsername');
    Route::post('user/changePassword', '\App\Http\Controllers\UserController@changePassword');
    Route::post('user/setPrivacyCode', '\App\Http\Controllers\UserController@setPrivacyCode');
    Route::post('user/updatePrivacyCode', '\App\Http\Controllers\UserController@updatePrivacyCode');
    Route::post('user/deletePrivacyCode', '\App\Http\Controllers\UserController@deletePrivacyCode');
    Route::get('schedule', '\App\Http\Controllers\ScheduleController@show');
    Route::get('schedule/past', '\App\Http\Controllers\ScheduleController@showPast');
    Route::get('schedule/today', '\App\Http\Controllers\ScheduleController@todaySchedule');
    Route::post('logout', '\App\Http\Controllers\UserController@logout');
    Route::get('presence/history', '\App\Http\Controllers\PresenceController@index');
    Route::post('presence', '\App\Http\Controllers\PresenceController@presence');
    Route::get('information', '\App\Http\Controllers\InformationController@index');
    Route::get('information/latest', '\App\Http\Controllers\InformationController@latestInfo');
    Route::post('information/markAsRead', '\App\Http\Controllers\InformationController@markAsRead');
});
