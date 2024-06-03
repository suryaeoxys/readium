<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MediaPostController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\SubscriptionPlanController;

Auth::routes();

Route::redirect('/', '/login');
// Route::redirect('/home', '/');
Route::GET('/{slug}', [PageController::class, 'viewPage']);
Route::GET('get-child-cat/{id}',[CategoryController::class,'getChildCat'])->name('get-child-cat');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {

    Route::GET('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::RESOURCE('banks', BankController::class);
    Route::RESOURCE('users', UserController::class);
    Route::RESOURCE('products', ProductController::class);
    Route::RESOURCE('authors', AuthorController::class);
    Route::RESOURCE('publishers', PublisherController::class);
    Route::RESOURCE('subscription-plan', SubscriptionPlanController::class);
    Route::GET('deleted-user', [UserController::class, 'deletedUser'])->name('users.deleted-user');
    Route::GET('restore-user/{id}', [UserController::class, 'restoreUser'])->name('users.restore-user');
    Route::GET('force-delete-user/{id}', [UserController::class, 'forceDeleteUser'])->name('users.force-delete-user');
    Route::GET('users/change-status/{id}', [UserController::class, 'changeStatus'])->name('users.change-status');
    Route::GET('authors/change-status/{id}', [AuthorController::class, 'changeStatus'])->name('authors.change-status');
    Route::GET('publishers/change-status/{id}', [PublisherController::class, 'changeStatus'])->name('publishers.change-status');

    Route::RESOURCE('site-setting', SettingController::class);
    Route::RESOURCE('app-setting', AppSettingController::class);
    Route::RESOURCE('pages', PageController::class);
    Route::RESOURCE('categories', CategoryController::class);
    Route::RESOURCE('media-post', MediaPostController::class);
    Route::GET('categories/change-status/{id}', [CategoryController::class, 'changeStatus'])->name('categories.change-status');
    Route::GET('products/change-status/{id}', [ProductController::class, 'changeStatus'])->name('products.change-status');

    Route::RESOURCE('sliders', SliderController::class);
    Route::GET('sliders/change-status/{id}', [SliderController::class, 'changeStatus'])->name('sliders.change-status');
    Route::RESOURCE('faqs', FaqController::class);
    Route::GET('faqs/change-status/{id}', [FaqController::class, 'changeStatus'])->name('faqs.change-status');
    Route::RESOURCE('email-templates', EmailTemplateController::class);
    Route::GET('email-templates/change-status/{id}', [EmailTemplateController::class, 'changeStatus'])->name('email-templates.change-status');

    Route::RESOURCE('notifications', NotificationController::class);
    Route::RESOURCE('permissions', PermissionController::class);
    Route::RESOURCE('roles', RoleController::class);

});
