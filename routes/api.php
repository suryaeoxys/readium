<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderAddressController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\ReferAndEearController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\FollowingController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikesController;
use App\Http\Controllers\Api\MediaPostController;
use App\Http\Controllers\Api\GoogleBookController;

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

// Without login api's - Without Token
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::POST('verify-otp', [AuthController::class, 'verifyOtp']);
Route::GET('logout', [AuthController::class, 'logout']);
Route::POST('resend-otp', [AuthController::class, 'resendOtp']);


Route::POST('forgot-password', [AuthController::class, 'forgotPassword']);
Route::POST('reset-password', [AuthController::class, 'resetPassword']);

Route::GET('single-user', [AuthController::class, 'singleUserProfile']);

//FAQ List
Route::get('faq-list', [FaqController::class, 'list']);
// Page
Route::get('pages', [PageController::class, 'pages']);

Route::post('update-app-version', [HomeController::class, 'appVersion']);

Route::get('app-version', [HomeController::class, 'getVersion']);

// Route::GET('single-user', [AuthController::class, 'singleUserProfile']);

// Route::GET('all-users', [AuthController::class, 'allUsers']);


Route::GET('all-product', [ProductController::class, 'allProducts']);
Route::GET('book-search', [GoogleBookController::class, 'bookSearch']);
Route::GET('book-list', [GoogleBookController::class, 'bookList']);


// Route::GET('get-commetns', [CommentController::class, 'getComments']);

Route::group(['as' => 'api.', 'middleware' =>['auth:api']], function(){
    Route::GET('all-media-post', [MediaPostController::class, 'allMediaPost']);

    Route::get('chat-room-list', [App\Http\Controllers\Api\Chat\ChatController::class, 'chatRoomList']);

    // Route::post('send-message',[App\Http\Controllers\Api\Chat\ChatController::class, 'sendMessage']); 


    Route::prefix('chat')->group(function () {
        Route::post('send-message',[App\Http\Controllers\Api\Chat\ChatController::class, 'sendMessage']);
        Route::post('edit-message',[App\Http\Controllers\Api\Chat\ChatController::class, 'editMessage']);
        Route::post('delete-message',[App\Http\Controllers\Api\Chat\ChatController::class, 'deleteMessage']);
        Route::post('send-message-group',[App\Http\Controllers\Api\Chat\ChatController::class, 'sendMessageGroup']);
        Route::post('create-group',[App\Http\Controllers\Api\Chat\ChatController::class, 'createGroup']); 
        Route::post('add-member-in-group',[App\Http\Controllers\Api\Chat\ChatController::class, 'addMemberGroup']); 
        Route::post('add-restricted-word',[App\Http\Controllers\Api\Chat\ChatController::class, 'addRestrictedWord']); 
        Route::get('delete-restricted-word/{id}',[App\Http\Controllers\Api\Chat\ChatController::class, 'deleteRestrictedWord']); 
        Route::get('restricted-word-list',[App\Http\Controllers\Api\Chat\ChatController::class, 'restrictedWordList']);
        Route::get('single-chat-list/{reciever_id}',[App\Http\Controllers\Api\Chat\ChatController::class, 'singleChatList']);
        Route::get('group-chat-list/{group_id}',[App\Http\Controllers\Api\Chat\ChatController::class, 'groupChatList']);
        Route::get('seen-message',[App\Http\Controllers\Api\Chat\ChatController::class, 'seenMessage']);
        Route::get('group-seen-message',[App\Http\Controllers\Api\Chat\ChatController::class, 'groupSeenMessage']);
        Route::get('all-chat-list',[App\Http\Controllers\Api\Chat\ChatController::class, 'allList']);

        Route::get('block-user/{id}',[App\Http\Controllers\Api\Chat\ChatController::class, 'blockUser']);
        Route::post('make-admin-in-group',[App\Http\Controllers\Api\Chat\ChatController::class, 'makeAdmin']);
        Route::post('remove-from-group',[App\Http\Controllers\Api\Chat\ChatController::class, 'removeFromGroup']);
    });
    
    Route::POST('change-password',[AuthController::class, 'changePassword']);

    Route::GET('get-commetns', [CommentController::class, 'getComments']);

    Route::GET('user-profile', [AuthController::class, 'userProfile']);

    Route::GET('singel-media-post', [MediaPostController::class, 'singelMediaPost']);
    Route::GET('repost', [MediaPostController::class, 'repost']);
    Route::post('report-post', [MediaPostController::class, 'reportPost']);
    Route::GET('my-post', [MediaPostController::class, 'myPost']);

    Route::GET('all-users', [AuthController::class, 'allUsers']);

    Route::GET('friends-list', [FollowingController::class, 'friendsList']);

    Route::GET('category/{categoryId}', [CategoryController::class, 'view']);
   
    // Route::GET('user-profile', [AuthController::class, 'userProfile']);
    Route::POST('update-profile', [AuthController::class, 'updateProfile']);
    Route::GET('home', [HomeController::class, 'home']);
    Route::GET('search', [HomeController::class, 'searchResult']);
   
    Route::GET('notification-list', [NotificationController::class, 'notificationList']);
    
    Route::POST('add-media-post', [MediaPostController::class, 'addMediaPost']);

    Route::POST('add-remove-follower', [FollowingController::class, 'addRemoveFollower']);
    Route::GET('following-list', [FollowingController::class, 'followingList']);
    Route::GET('followers-list', [FollowingController::class, 'followersList']);

    Route::POST('add-remove-like', [LikesController::class, 'addRemoveLike']);
    Route::GET('list', [LikesController::class, 'list']);

    Route::POST('delete-media-post', [MediaPostController::class, 'deleteMediaPost']);

    Route::POST('add-comment', [CommentController::class, 'addComment']);

    Route::POST('delete-comment', [CommentController::class, 'deleteComment']);

    Route::POST('report-admin', [CommentController::class, 'reportOnComment']);

    Route::POST('delete-user', [AuthController::class, 'deleteUser']);

    Route::POST('add-remove-block', [CommentController::class, 'blockUser']);

    Route::GET('blocked-users-list', [CommentController::class, 'blockedUserList']);

    Route::GET('notification-count', [AuthController::class, 'notificationCount']);
    Route::GET('logout', [AuthController::class, 'logout']);
    Route::GET('delete-account', [AuthController::class, 'deleteAccount']);

});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});