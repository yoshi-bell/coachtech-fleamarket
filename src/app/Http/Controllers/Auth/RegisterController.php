<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\RegisterRequest;
use Illuminate\Routing\Controller;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(RegisterRequest $request)
    {
        // FortifyのCreateNewUserアクションを使用してユーザーを作成
        $creator = new CreateNewUser();
        $user = $creator->create($request->validated());

        // ログイン状態にする
        auth()->login($user);

        // ログイン後にメール認証画面へリダイレクト
        return redirect()->route('verification.notice');
    }
}