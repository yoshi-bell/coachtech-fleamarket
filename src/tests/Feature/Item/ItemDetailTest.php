<?php

namespace Tests\Feature\Item;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\Like;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    /** @test */
    public function test_required_information_is_displayed()
    {
        $seller = User::factory()->has(Profile::factory())->create();
        $commenter = User::factory()->has(Profile::factory())->create();

        $item = Item::factory()
            ->for($seller, 'seller')
            ->has(Like::factory()->count(3))
            ->has(Comment::factory(['comment' => 'This is a test comment.', 'user_id' => $commenter->id]))
            ->create();

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee($item->brand);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($item->condition->content);
        $response->assertSee('3'); // Like count
        $response->assertSee('1'); // Comment count
        $response->assertSee('This is a test comment.');
        $response->assertSee($commenter->name);
    }

    /** @test */
    public function test_multiple_categories_are_displayed()
    {
        $categories = \App\Models\Category::take(2)->get();
        $condition = \App\Models\Condition::first();

        $item = Item::factory()
            ->hasAttached($categories)
            ->create(['condition_id' => $condition->id]);

        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee($categories[0]->content);
        $response->assertSee($categories[1]->content);
    }
}
