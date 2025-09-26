<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\SoldItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_all_items_are_displayed()
    {
        Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 3;
        });
    }

    /** @test */
    public function test_sold_items_are_marked_as_sold()
    {
        Item::factory()->has(SoldItem::factory(), 'soldItem')->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function test_user_does_not_see_their_own_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userItem = Item::factory()->create(['seller_id' => $user->id]);
        $otherUserItem = Item::factory()->create(['seller_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) use ($userItem, $otherUserItem) {
            return $items->contains($otherUserItem) && !$items->contains($userItem);
        });
    }
}
