<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /** @test */
    public function test_user_can_like_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)->postJson('/like/' . $item->id);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function test_user_can_unlike_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user)->postJson('/like/' . $item->id); // First, like the item

        $this->actingAs($user)->deleteJson('/like/' . $item->id); // Then, unlike it

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function test_is_liked_status_is_passed_to_view()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // Initially, not liked
        $response = $this->actingAs($user)->get('/item/' . $item->id);
        $response->assertViewHas('isLiked', false);

        // Like the item
        $this->actingAs($user)->postJson('/like/' . $item->id);

        // Now, should be liked
        $response = $this->actingAs($user)->get('/item/' . $item->id);
        $response->assertViewHas('isLiked', true);
    }
}
