<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /** @test */
    public function test_can_search_items_by_name()
    {
        $item1 = Item::factory()->create(['name' => 'Amazing T-Shirt']);
        $item2 = Item::factory()->create(['name' => 'Incredible Mug']);

        $response = $this->get('/?keyword=Amazing');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) use ($item1, $item2) {
            return $items->contains($item1) && !$items->contains($item2);
        });
    }

    /** @test */
    public function test_search_keyword_is_retained_on_mylist_tab()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => 'Searchable Item']);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $item->id]);

        $response = $this->actingAs($user)->get('/?keyword=Searchable&tab=mylist');

        $response->assertStatus(200);
        $response->assertViewHas('keyword', 'Searchable');
        $response->assertViewHas('items', function ($items) use ($item) {
            return $items->contains($item);
        });
    }
}
