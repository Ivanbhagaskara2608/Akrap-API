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

// secretary
Route::prefix('secretary')->middleware(['auth:api', 'isSecretary'])->group(function() {
    Route::post('schedule/add', '\App\Http\Controllers\ScheduleController@create');
    Route::get('presence/show/{scheduleId}', '\App\Http\Controllers\PresenceController@show');
    Route::post('presence/store', '\App\Http\Controllers\PresenceController@store');
    Route::post('presence/edit', '\App\Http\Controllers\PresenceController@edit');
});

// treasurer

// all users authenticated
Route::middleware(['auth:api'])->group(function() {
    Route::get('schedule', '\App\Http\Controllers\ScheduleController@index');
    Route::post('logout', '\App\Http\Controllers\UserController@logout');
    Route::get('presence/history', '\App\Http\Controllers\PresenceController@index');
    Route::post('presence', '\App\Http\Controllers\PresenceController@presence');
});
