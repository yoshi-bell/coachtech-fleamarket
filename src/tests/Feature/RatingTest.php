<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Rating;

class RatingTest extends TestCase
{
    use RefreshDatabase; // 各テスト後にデータベースをリフレッシュ

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 購入者が評価を送信できることをテストする
     *
     * @return void
     */
    public function test_buyer_can_submit_rating()
    {
        // データの準備
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create(); // 出品者に関連付ける
        $soldItem = SoldItem::factory()->for($item)->for($buyer, 'buyer')->create(); // 商品と購入者に関連付ける

        // 購入者として認証
        $this->actingAs($buyer);

        // 評価を送信 (rateは1〜5の整数)
        $response = $this->post(route('rating.store', $soldItem->id), [
            'rating' => 5,
        ]);

        // データベースに評価が保存されたことを確認
        $this->assertDatabaseHas('ratings', [
            'sold_item_id' => $soldItem->id,
            'rater_id' => $buyer->id,
            'rated_user_id' => $seller->id,
            'rating' => 5,
        ]);

        // 期待されるリダイレクト先（今回は商品一覧ページ）
        $response->assertRedirect(route('index'));
        $response->assertSessionHas('message', '評価を送信しました。取引完了です！');
    }

    /**
     * 出品者側で、購入者による評価後にチャット画面で評価モーダルが自動表示されることをテストする
     *
     * @return void
     */
    public function test_seller_sees_rating_modal_after_buyer_rates()
    {
        // データの準備
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create();
        $soldItem = SoldItem::factory()->for($item)->for($buyer, 'buyer')->create();

        // 購入者が評価済みであることを示す評価レコードを作成
        Rating::factory()->create([
            'sold_item_id' => $soldItem->id,
            'rater_id' => $buyer->id,
            'rated_user_id' => $seller->id,
            'rating' => 5,
        ]);

        // 出品者としては未評価であることを確認 (assertDatabaseMissingはここでは直接使わないが、状態として)

        // 出品者として認証
        $this->actingAs($seller);

        // チャット画面にアクセス
        $response = $this->get(route('chat.index', $item->id));

        // ページが正常に表示されることを確認
        $response->assertStatus(200);

        // 評価モーダルを自動表示するためのJavaScriptコードが含まれていることを確認
        $response->assertSee('openRatingModal()');

        // 「取引を完了する」ボタンがページに含まれていないことを確認（出品者には表示されないため）
        $response->assertDontSee('取引を完了する');
    }
}
