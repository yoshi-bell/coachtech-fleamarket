<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Chat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChatTest extends TestCase
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
     * ユーザーがチャットメッセージを送受信できることをテストする
     *
     * @return void
     */
    public function test_users_can_send_and_receive_chat_messages()
    {
        Storage::fake('public'); // publicディスクをモックする

        // データの準備
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->for($seller, 'seller')->create();
        $soldItem = SoldItem::factory()->for($item)->for($buyer, 'buyer')->create();

        // --- 購入者からのメッセージ送信 ---
        $this->actingAs($buyer);

        $messageText = '購入者からのメッセージです';
        $imageFile = UploadedFile::fake()->image('buyer_message.jpg');

        $response = $this->post(route('chat.store', $item->id), [
            'message' => $messageText,
            'image' => $imageFile,
        ]);

        // データベースにメッセージが保存されたことを確認
        $this->assertDatabaseHas('chats', [
            'sold_item_id' => $soldItem->id,
            'sender_id' => $buyer->id,
            'message' => $messageText,
            'image_path' => 'chat_images/' . $imageFile->hashName(),
        ]);
        // ストレージに画像ファイルが保存されたことを確認
        Storage::disk('public')->assertExists('chat_images/' . $imageFile->hashName());
        $response->assertRedirect(route('chat.index', $item->id));

        // 送信直後はread_atがnullであることを確認
        $buyerChat = Chat::where('sender_id', $buyer->id)->where('sold_item_id', $soldItem->id)->first();
        $this->assertNotNull($buyerChat); // 確実にチャットが存在することを確認
        $this->assertNull($buyerChat->read_at);


        // --- 出品者からのメッセージ送信 ---
        $this->actingAs($seller);

        $messageTextSeller = '出品者からの返信です';
        $imageFileSeller = UploadedFile::fake()->image('seller_reply.png');

        $responseSeller = $this->post(route('chat.store', $item->id), [
            'message' => $messageTextSeller,
            'image' => $imageFileSeller,
        ]);

        $this->assertDatabaseHas('chats', [
            'sold_item_id' => $soldItem->id,
            'sender_id' => $seller->id,
            'message' => $messageTextSeller,
            'image_path' => 'chat_images/' . $imageFileSeller->hashName(),
        ]);
        Storage::disk('public')->assertExists('chat_images/' . $imageFileSeller->hashName());
        $responseSeller->assertRedirect(route('chat.index', $item->id));

        // --- 出品者がチャット画面を開いた後、購入者からのメッセージが既読になっていることを確認 ---
        $responseChatPage = $this->get(route('chat.index', $item->id));
        $responseChatPage->assertStatus(200);
        $responseChatPage->assertSee($messageText);
        $responseChatPage->assertSee($messageTextSeller);
        $responseChatPage->assertSee($imageFile->hashName()); // 画像ファイル名が表示されることで存在を確認
        $responseChatPage->assertSee($imageFileSeller->hashName());

        $buyerChat->refresh(); // データベースから最新の状態を再読み込み
        $this->assertNotNull($buyerChat->read_at); // read_atがnullではないことを確認
    }
}
