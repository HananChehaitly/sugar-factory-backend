<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\adminController;
use App\Http\Controllers\API\AuthController;

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

// Route::get('/highlighted', [UserController::class, 'highlighted'])->name('api:highlighted');
// Route::post('/login', [AuthController::class, 'login'])->name('api:login');

Route::group([
    'middleware' => 'api',

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::group([
    'middleware' => 'api',
    'middleware' => 'auth.user',
    'prefix' => 'user'

], function ($router) {

    Route::post('/add-picture', [userController::class, 'addPicture']);    
    Route::post('/remove-connection', [userController::class, 'removeConnection']);    
    Route::post('/add-favorite', [userController::class, 'addFavorite']);    
    Route::post('/block-user', [userController::class, 'blockUser']);    
    Route::post('/send-message', [userController::class, 'sendMessage']);    
    Route::post('/add-interest', [userController::class, 'addInterest']);    
    Route::post('/add-hobby', [userController::class, 'addHobby']);    
    Route::post('/notification', [userController::class, 'addNotification']);    
    Route::post('/search', [userController::class, 'searchUser']);    
    Route::get('/get-all-matches', [userController::class, 'getAllMatches']);    
    Route::post('/edit-profile', [userController::class, 'editProfile']);    
    Route::post('/edit-interset', [userController::class, 'editInterest']);    
    Route::get('/get-intersets', [userController::class, 'getInterests']);    
    Route::post('/edit-hobby', [userController::class, 'editHobby']);   
    Route::get('/get-hobbies', [userController::class, 'getHobbies']);    
    Route::post('/get-user', [userController::class, 'getUser']);   
    Route::get('/get-messages', [userController::class, 'getMessages']); 
    Route::post('/set-message-as-read', [userController::class, 'setMessageAsRead']); 
    Route::get('/get-user-notifications', [userController::class, 'getUserNotificaion']); 
    Route::post('/read-notification', [userController::class, 'readNotificaion']); 
});

Route::group([
    'middleware' => 'api',
    'middleware' => 'auth.admin',

], function ($router) {
    Route::get('/get-sent-messages', [adminController::class, 'getMessages']);    
    Route::post('/approve-message', [adminController::class, 'approveMessage']);    
    Route::post('/decline-message', [adminController::class, 'declineMessage']);    
    Route::get('/get-uploaded-images', [adminController::class, 'getImages']);    
    Route::post('/approve-image', [adminController::class, 'approvetImage']);    
    Route::post('/decline-image', [adminController::class, 'declineImage']);    

});
