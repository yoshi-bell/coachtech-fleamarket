<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;

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

// ログインしていなくてもアクセスできるルート
Route::get('/', [ItemController::class, 'index'])->name('index'); // トップページは誰でもアクセス可能に
Route::get('/item/{item}', [ItemController::class, 'show'])->name('item.show');

// 認証済みユーザーのみアクセスできるルートのグループ
Route::middleware('auth')->group(function () {
    // 商品出品
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // プロフィール
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage.show');

    // 商品購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // Stripe決済成功
    Route::get('/purchase/success/{item}', [PurchaseController::class, 'success'])->name('purchase.success');

    // 住所変更
    Route::get('/purchase/address/{item}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::patch('/purchase/address/{item}', [AddressController::class, 'update'])->name('purchase.address.update');

    // いいね機能
    Route::post('/like/{item}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item}', [LikeController::class, 'destroy'])->name('like.destroy');

    // コメント機能
    Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comment.store');
});

Route::post('/register', RegisterController::class);
Route::post('/custom-login', [CustomLoginController::class, 'login'])->name('custom.login');

// Fortifyの認証ルートはFortifyが自動で設定します
// Route::get('/register', ...);
// Route::get('/login', ...);
// Route::post('/logout', ...);