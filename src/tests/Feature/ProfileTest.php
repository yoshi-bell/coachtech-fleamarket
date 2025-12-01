<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use App\Models\Chat;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Conditionを作成
        \App\Models\Condition::create(['content' => '新品、未使用']);
    }

    public function test_profile_transaction_tab_displays_correct_items()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->markEmailAsVerified();

        // Case 1: User is buyer (Transaction in progress)
        $seller1 = User::factory()->create();
        $item1 = Item::factory()->create(['seller_id' => $seller1->id, 'name' => 'Bought Item', 'condition_id' => 1]);
        SoldItem::create([
            'item_id' => $item1->id,
            'buyer_id' => $user->id,
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
        ]);

        // Case 2: User is seller (Transaction in progress)
        $buyer2 = User::factory()->create();
        $item2 = Item::factory()->create(['seller_id' => $user->id, 'name' => 'Sold Item', 'condition_id' => 1]);
        SoldItem::create([
            'item_id' => $item2->id,
            'buyer_id' => $buyer2->id,
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
        ]);

        // Case 3: User is buyer (Transaction completed/rated)
        $seller3 = User::factory()->create();
        $item3 = Item::factory()->create(['seller_id' => $seller3->id, 'name' => 'Rated Item', 'condition_id' => 1]);
        $soldItem3 = SoldItem::create([
            'item_id' => $item3->id,
            'buyer_id' => $user->id,
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
        ]);
        Rating::create([
            'rater_id' => $user->id,
            'rated_user_id' => $seller3->id,
            'sold_item_id' => $soldItem3->id,
            'rating' => 5,
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.show', ['page' => 'transaction']));

        $response->assertStatus(200);
        $response->assertSee('Bought Item');
        $response->assertSee('Sold Item');
        $response->assertDontSee('Rated Item');
    }

    public function test_profile_transaction_tab_shows_unread_message_count()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->markEmailAsVerified();
        $seller = User::factory()->create();

        $item = Item::factory()->create(['seller_id' => $seller->id, 'name' => 'Chat Item', 'condition_id' => 1]);
        $soldItem = SoldItem::create([
            'item_id' => $item->id,
            'buyer_id' => $user->id,
            'postcode' => '123-4567',
            'address' => 'Test Address',
            'building' => 'Test Building',
        ]);

        // Unread message from seller
        Chat::create([
            'sold_item_id' => $soldItem->id,
            'sender_id' => $seller->id,
            'message' => 'Hello',
        ]);

        // Read message from seller
        Chat::create([
            'sold_item_id' => $soldItem->id,
            'sender_id' => $seller->id,
            'message' => 'Read me',
            'read_at' => now(),
        ]);

        // Message sent by user (should not count)
        Chat::create([
            'sold_item_id' => $soldItem->id,
            'sender_id' => $user->id,
            'message' => 'My reply',
        ]);

        $response = $this->actingAs($user)
            ->get(route('mypage.show', ['page' => 'transaction']));

        $response->assertStatus(200);
        // Check for badge with count 1
        $response->assertSee('<div class="item-card__badge">', false);
        $response->assertSee('1');
    }
}
