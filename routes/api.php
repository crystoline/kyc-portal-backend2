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

Route::middleware('auth:api')->get('/user', static function (Request $request) {
    return $request->user();
});
Route::group(['prefix'=> '/auth'], static function (){

    Route::post('/reset-password', 'AuthController@resetPassword');
    Route::post('/complete-password-reset', 'AuthController@completePasswordReset');
    Route::group([ 'middleware' => 'auth:api'], static function(){
        Route::get('/logout', 'AuthController@logout');
        Route::get('/logout-all', 'AuthController@logoutFromAll');
    });
});

Route::resource('groups', 'GroupAPIController')->except('delete');


Route::post('/users/{id}/toggle-status', 'UserAPIController@toggleStatus');
Route::resource('users', 'UserAPIController')->except('delete');

Route::resource('agents', 'AgentAPIController')->except('delete');

Route::resource('verifications', 'VerificationAPIController')
    ->middleware(['filter-null', 'create-verification'])
    ->except('delete');