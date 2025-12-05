<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;

class ChatTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * チャット画面でメッセージを入力し、他の画面に遷移してから戻っても入力内容が保持されていることをテストする (FN009)
     *
     * @return void
     */
    public function test_message_input_is_retained_after_navigation()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create();
        $soldItem = SoldItem::factory()->for($item)->for($buyer, 'buyer')->create();

        $this->browse(function (Browser $browser) use ($buyer, $item) {
            $browser->visit('/login')
                ->type('email', $buyer->email)
                ->type('password', 'usertest') // UserFactory defined password
                ->press('ログイン')
                ->assertPathIs('/') // Assuming redirect to home after login
                ->visit(route('chat.index', $item->id))
                ->assertSee('取引画面')
                // メッセージを入力
                ->type('message', '保持されるべきメッセージ')
                // 他のページ（トップページ）に遷移
                ->visit(route('index'))
                ->assertPathIs('/')
                // 再度チャットページに戻る
                ->visit(route('chat.index', $item->id))
                // 入力内容が保持されていることを確認
                ->assertInputValue('message', '保持されるべきメッセージ');
        });
    }
}
