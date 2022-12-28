<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ApI\UserController;


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
        Route::get('index/{num?}/{last?}','API\UserController@index')->name('index');
        Route::get('attendance/history/{id?}','API\UserController@attendanceHistory')->name('history');
       
    });
    Route::apiResource('user', 'UserController')->except(['index']);
    // Route::get('get-users',[AuthController::class, 'getUsers']);

});
Route::post('attendance/mark','API\UserController@attendanceMark')->name('attendanceMark');

