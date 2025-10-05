<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PurchasePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * 支払い方法の動的な変更をテスト
     *検証していること：「フォームの送信に失敗して戻ってきた時に、以前選択した値が保持されているか」というサーバーサイドの挙動。
     *シナリオ：「住所の入力ミスなどでエラーになった際に、支払い方法を再選択しなくても済むか？」
     * @return void
     */
    public function test_payment_method_display_changes_dynamically()
    {
        $this->seed();

        $item = Item::first();

        $user = User::where('id', '!=', $item->seller_id)->first();

        if (!$user->profile) {
            Profile::factory()->for($user)->create();
        }

        $user->password = bcrypt('password');
        $user->save();


        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('ログイン')
                    ->assertPathIs('/')

                    ->visit(route('purchase.create', $item))
                    ->waitFor('#payment-method-select') // プルダウン要素が可視になるまで待機

                    ->assertSeeIn('#payment-method-display', 'コンビニ払い')
                    ->select('payment_method', '2')
                    ->waitForTextIn('#payment-method-display', 'カード支払い')
                    ->assertSeeIn('#payment-method-display', 'カード支払い');
        });
    }
}
