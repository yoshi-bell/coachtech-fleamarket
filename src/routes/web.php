<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;

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
Route::get('/', [ItemController::class, 'index']); // トップページは誰でもアクセス可能に

// 認証済みユーザーのみアクセスできるルートのグループ
Route::middleware('auth')->group(function () {
    // ここにログインが必要なルートを追加していきます
    // 例:
    // Route::get('/mypage', [UserController::class, 'show']);
    // Route::post('/items/{item}/purchase', [PurchaseController::class, 'store']);

    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage.show');
});

// Fortifyの認証ルートはFortifyが自動で設定します
// Route::get('/register', ...);
// Route::post('/register', ...);
// Route::get('/login', ...);
// Route::post('/login', ...);
// Route::post('/logout', ...);