<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_email_is_required_for_login()
    {
        $response = $this->post('/custom-login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_password_is_required_for_login()
    {
        $response = $this->post('/custom-login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test1@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post('/custom-login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->post('/custom-login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/');
    }
}
