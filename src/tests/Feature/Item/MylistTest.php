<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\Like;
use App\Models\SoldItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /** @test */
    public function test_only_liked_items_are_displayed()
    {
        $user = User::factory()->create();

        $likedItem = Item::factory()->create();
        $notLikedItem = Item::factory()->create();

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) use ($likedItem, $notLikedItem) {
            return $items->contains($likedItem) && !$items->contains($notLikedItem);
        });
    }

    /** @test */
    public function test_sold_items_are_marked_as_sold_in_mylist()
    {
        $user = User::factory()->create();

        $soldItem = Item::factory()->has(SoldItem::factory(), 'soldItem')->create();
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $soldItem->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /** @test */
    public function test_unauthenticated_user_sees_all_items_on_mylist_tab()
    {
        Item::factory()->count(5)->create();

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 5;
        });
    }
}
