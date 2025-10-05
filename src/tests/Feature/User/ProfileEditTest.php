<?php

namespace Tests\Feature\User;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_profile_edit_page_shows_existing_values()
    {
        $user = User::factory()
            ->has(Profile::factory([
                'postcode' => '123-4567',
                'address' => 'Old Address',
            ]))
            ->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('value="Old Name"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="Old Address"', false);
    }

    /** @test */
    public function test_can_update_profile_info()
    {
        Storage::fake('public');

        $user = User::factory()->has(Profile::factory())->create(['name' => 'Old Name']);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $newData = [
            'name' => 'New Name',
            'postcode' => '987-6543',
            'address' => 'New Address',
            'building' => 'New Building',
            'img_url' => $file,
        ];

        $response = $this->actingAs($user)->patch(route('profile.update'), $newData);

        $response->assertRedirect(route('mypage.show'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postcode' => '987-6543',
            'address' => 'New Address',
            'building' => 'New Building',
        ]);

        Storage::disk('public')->assertExists('profile_images/' . $file->hashName());
    }
}
