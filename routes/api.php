<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;


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
Route::post('register',[AuthController::class, 'register']);
Route::post('login',[AuthController::class, 'login']);


Route::middleware('auth:api')->group( function(){

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('index/{num?}/{last?}','Api\UserController@index')->name('index');
        Route::get('attendance/history/{id?}','Api\UserController@attendanceHistory')->name('history');
       
    });
    Route::apiResource('user', 'UserController')->except(['index']);
    // Route::get('get-users',[AuthController::class, 'getUsers']);

});
Route::post('attendance/mark','Api\UserController@attendanceMark')->name('attendanceMark');

