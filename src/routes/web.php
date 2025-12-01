<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//==============================================================
// 1. Public Routes (No Authentication Required)
//==============================================================
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');


//==============================================================
// 2. Authentication Routes
//==============================================================
// Note: Standard Fortify routes for login (GET), register (GET), logout etc. are handled by Fortify.
// These are the custom implementations for the POST actions.
////RequestFormによるバリデーションを行ってからFortifyによる会員登録のためのカスタムルート
Route::post('/register', RegisterController::class)->name('register');
//RequestFormによるバリデーションを行ってからFortifyによるログインのためのカスタムルート
Route::post('/custom-login', LoginController::class)->name('custom.login');


//==============================================================
// 3. Authenticated Routes (user must be logged in and verified)
//==============================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Sell (Item Listing)
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // Mypage / Profile
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage.show');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Purchase Flow
    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/success/{item}', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::patch('/purchase/address/{item}', [AddressController::class, 'update'])->name('purchase.address.update');

    // Interactions (Like, Comment)
    Route::post('/like/{item}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item}', [LikeController::class, 'destroy'])->name('like.destroy');
    Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comment.store');

    // Chat
    Route::get('/chat/{item}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/{item}', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])->name('chat.destroy');

    // Rating
    Route::post('/rating/{item}', [RatingController::class, 'store'])->name('rating.store');
});
