<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest; // 独自のカスタムリクエストフォーム
use Illuminate\Http\Request; // Illuminate\Http\Requestを使用
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest; // FortifyのLoginRequestをエイリアス
use Illuminate\Support\Facades\Validator; // バリデーションのために追加

class CustomLoginController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request) // 引数の型ヒントをIlluminate\Http\Requestに変更
    {
        // 1. 私たちのカスタムLoginRequestのルールとメッセージを使ってバリデーションを実行
        $validator = Validator::make(
            $request->all(),
            (new LoginRequest())->rules(),
            (new LoginRequest())->messages()
        );

        $validator->validate(); // バリデーション失敗時は自動的にリダイレクト

        // 2. Fortifyが期待するLaravel\Fortify\Http\Requests\LoginRequestのインスタンスを作成
        $fortifyRequest = new FortifyLoginRequest();
        $fortifyRequest->setContainer(app()); // コンテナをバインド
        $fortifyRequest->replace($request->all()); // リクエストデータを設定

        // 3. Fortifyの認証コントローラーを呼び出して、FortifyのRequestインスタンスを渡す
        $authController = app(AuthenticatedSessionController::class);

        return $authController->store($fortifyRequest); // FortifyのRequestインスタンスを渡す
    }
}
