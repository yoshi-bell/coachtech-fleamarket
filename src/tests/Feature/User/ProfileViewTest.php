<?php

namespace Tests\Feature\User;

use App\Models\Item;
use App\Models\Profile;
use App\Models\SoldItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileViewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /** @test */
    public function test_can_view_own_profile_info()
    {
        // Create a user with a profile and 1 listed item
        $user = User::factory()
            ->has(Profile::factory())
            ->has(Item::factory(['name' => 'My Listed Item']), 'items')
            ->create();

        // Create an item sold by another user, but purchased by our main user
        $purchasedItem = Item::factory()->create(['name' => 'My Purchased Item']);
        SoldItem::factory()->create([
            'item_id' => $purchasedItem->id,
            'buyer_id' => $user->id,
        ]);

        // Check the 'sell' tab
        $response = $this->actingAs($user)->get(route('mypage.show'));

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('My Listed Item');
        $response->assertDontSee('My Purchased Item');

        // Check the 'buy' tab
        $response = $this->actingAs($user)->get(route('mypage.show', ['page' => 'buy']));

        $response->assertStatus(200);
        $response->assertSee('My Purchased Item');
        $response->assertDontSee('My Listed Item');
    }
}
