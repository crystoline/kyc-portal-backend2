<?php

use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;
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

Route::group(['prefix'=> '/auth'], static function (){

    Route::post('/token', 'AuthController@auth')->name('login');
    Route::post('/reset-password', 'AuthController@resetPassword')->name('reset-password-link');
    Route::post('/complete-password-reset', 'AuthController@completePasswordReset')->name('reset-password');
    Route::group([ 'middleware' => 'auth:api'], static function(){
        Route::get('/logout', 'AuthController@logout')->name('logout');
        Route::get('/logout-all', 'AuthController@logoutFromAll')->name('logout-all');
    });
});



Route::group(['middleware' => 'auth:api'/*, 'filter-null'*/], static  function(){
    Route::post('/users/{id}/toggle-status', 'UserController@toggleStatus')->name('user.toggle-status');
    Route::post('users/assign-agents', 'UserController@assignAgents')->name('user.assign-agents');
    Route::resource('users', 'UserController')->except('delete');
    Route::post('agents/{id}/mark-as-created', 'AgentController@markAsCreated')->name('agents.mark-as-created');
    Route::post('agents/bulk-upload', 'AgentController@uploadAgent')->name('agents.bulk-upload');
    Route::resource('agents', 'AgentController')->except('delete');
    Route::resource('groups', 'GroupController')->except('delete');

    Route::post('verifications/delete-single-document/{id}', 'VerificationController@deleteDocument')->name('verifications.document.delete');
    Route::post('verifications/{id}/upload-single-document', 'VerificationController@uploadSingleDocument')->name('verifications.document.upload');
    Route::post('verifications/{id}/approval', 'VerificationController@verificationApproval')->name('verifications.approval');
    Route::post('verifications/{id}/publish', 'VerificationController@publish')->name('verifications.publish');
    Route::post('verifications/{id}/telephone/send-code', 'VerificationController@verifyTelephone')->name('verifications.telephone.send-verification-code');
    Route::post('verifications/{id}/telephone/verify', 'VerificationController@verifyTelephoneConfirmation')->name('verifications.telephone.verify');
    Route::post('verifications/{id}/bvn_data', 'VerificationController@bvnData')->name('verifications.bvn-data');
    Route::post('verifications/bvn_verification_id}/verify_bvn', 'VerificationController@verifyBvn')->name('verifications.verify-bvn');
    Route::post('verifications/{id}/account_name_enquiry', 'VerificationController@nameEnquiry')->name('verifications.account-name-enquiry');
    Route::resource('verifications', 'VerificationController')
        ->middleware([ 'create-verification'])->except('delete');

    Route::get('dashboard/all_users', 'DashboardController@totalUsers')->name('dashboard.user-all') ;
    Route::get('dashboard/all_users_by_group', 'DashboardController@totalUsersByGroup')->name('dashboard.all-users') ;
    Route::get('dashboard/all_agents', 'DashboardController@totalAgent')->name('dashboard.all-agents') ;
    Route::get('dashboard/all_principal_agents', 'DashboardController@principalAgents')->name('dashboard.all-principal-agents') ;
    Route::get('dashboard/all_sole_agents', 'DashboardController@soleAgents')->name('dashboard.all-sole-agents') ;
    Route::get('dashboard/pending_verification/{me?}', 'DashboardController@pendingVerifications')->name('dashboard.pending-verification') ;
    Route::get('dashboard/monthly_verifications/{me?}', 'DashboardController@monthlyVerification')->name('dashboard.monthly-verification') ;
    Route::get('dashboard/all_monthly_verifications/{me?}', 'DashboardController@monthlyAllVerifications')->name('dashboard.all-monthly-verification') ;
    Route::get('dashboard/all_verification_periods/{me?}', 'DashboardController@byVerifictionExcercise')->name('dashboard.verification-periods') ;


    Route::get('setting', 'SettingController@index')->name('setting.all');
    Route::post('setting', 'SettingController@store')->name('setting.store');

    Route::post('permissions/tasks', 'PermissionController@tasks')->name('permissions.tasks');
    Route::post('permissions/generate-tasks', 'PermissionController@generateTasks')->name('permissions.generate-tasks');
    Route::post('permissions/default', 'PermissionController@defaultPermissions')->name('permissions.default');
    Route::get('permissions', 'PermissionController@index')->name('permissions.all');
    Route::post('permissions', 'PermissionController@store')->name('permissions.store');
    Route::put('permissions/{group}', 'PermissionController@update')->name('permissions.update');
    Route::resource('agent_types', 'AgentTypeController');

});
Route::resource('banks', 'BankController')->only(['index', 'show']);
Route::resource('lgas', 'LgaController');
Route::resource('states', 'StateController') ->except('delete');
Route::get('agents/bulk-upload/download-template', 'AgentController@downloadUploadTemplate')->name('agents.bulk-upload-template');

Route::get('test-mail', static function (){
    Mail::to('adekunle.adekoya@upperlink.ng')->send(new TestMail());
    return 'sent';
});




//Route::resource('bank_types', 'BankTypeController')->only(['index', 'show']);



Route::resource('verification_periods', 'VerificationPeriodAPIController')->except(['delete']);

Route::resource('device_owners', 'DeviceOwnerAPIController')->except('delete');

Route::resource('territories', 'TerritoryAPIController')->except('delete');

