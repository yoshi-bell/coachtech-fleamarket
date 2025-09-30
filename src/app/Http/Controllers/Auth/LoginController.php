<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class LoginController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // MustVerifyEmailインターフェースを実装しているか、かつ、未認証かチェック
            if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                // 未認証なら、メールを再送して、認証案内画面へ
                $user->sendEmailVerificationNotification();
                return redirect()->route('verification.notice')->with('message', 'ログインする前に、メールを確認して認証を完了してください。新しい認証メールを送信しました。');
            }

            // 認証済みなら、通常通りセッションを再生成してリダイレクト
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }
}
