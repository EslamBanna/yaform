<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'unauth'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/sign-up', [UserController::class, 'signup']);
});

Route::group(['prefix' => 'auth', 'middleware' => 'checkAuth:api-user'], function () {
    Route::post('/update-user-info', [UserController::class, 'updateUserInfo']);
    Route::post('/get-user-info', [UserController::class, 'getUserInfo']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/create-form',[FormController::class,'createForm']);
    Route::get('/get-forms',[FormController::class,'getForms']);
    Route::post('/get-form-questions',[FormController::class,'getFormQuestions']);
    Route::post('/delete-form',[FormController::class,'deleteForm']);
    Route::post('/edit-form',[FormController::class,'editForm']);
    Route::post('/search-forms',[FormController::class,'searchForms']);
    Route::post('/update-response-msg',[FormController::class,'updateResponseMsg']);

});