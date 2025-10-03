<?php

namespace Tests\Feature\Item;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    /** @test */
    public function test_logged_in_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->postJson('/comment/' . $item->id, [
            'comment' => 'This is a valid comment.'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => 'This is a valid comment.'
        ]);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->postJson('/comment/' . $item->id, [
            'comment' => 'This is a valid comment.'
        ]);

        $response->assertStatus(401); // Unauthorized
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->postJson('/comment/' . $item->id, [
            'comment' => ''
        ]);

        $response->assertStatus(422); // Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors('comment');
    }

    /** @test */
    public function test_comment_must_not_exceed_max_length()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $longComment = str_repeat('a', 256);

        $response = $this->actingAs($user)->postJson('/comment/' . $item->id, [
            'comment' => $longComment
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('comment');
    }
}
