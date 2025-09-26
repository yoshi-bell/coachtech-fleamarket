<?php

namespace Tests\Feature\Item;

use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    /** @test */
    public function test_can_list_an_item()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $categories = \App\Models\Category::take(2)->get();
        $condition = Condition::first();

        $file = UploadedFile::fake()->image('item.jpg');

        $itemData = [
            'name' => 'New Awesome Item',
            'description' => 'This is a description.',
            'brand' => 'Awesome Brand',
            'price' => 12345,
            'condition_id' => $condition->id,
            'category_ids' => $categories->pluck('id')->toArray(),
            'img_url' => $file,
        ];

        $response = $this->actingAs($user)->post(route('sell.store'), $itemData);

        $this->assertDatabaseHas('items', [
            'name' => 'New Awesome Item',
            'price' => 12345,
            'seller_id' => $user->id,
        ]);

        $item = Item::first();
        $this->assertCount(2, $item->categories);
        Storage::disk('public')->assertExists('item_images/' . $file->hashName());

        $response->assertRedirect(route('item.show', $item));
    }
}
