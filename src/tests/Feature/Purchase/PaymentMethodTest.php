<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    /** @test */
    public function test_payment_method_selection_is_reflected_on_validation_error()
    {
        $user = User::factory()->create(); // Create user WITHOUT a profile
        $item = Item::factory()->create();

        // From a page that has the form
        $from = route('purchase.create', $item);

        // Submit the form with payment_method=2 but trigger a validation error elsewhere
        // (shipping_address is required because the user has no profile)
        $response = $this->actingAs($user)->from($from)->post('/purchase/' . $item->id, [
            'payment_method' => '2' // 2 = Credit Card
        ]);

        // Assert we are redirected back to the form
        $response->assertRedirect($from);

        // Follow the redirect and check the re-rendered page
        $followResponse = $this->get($response->headers->get('Location'));

        // Assert the dropdown has the correct option selected
        $followResponse->assertSee('<option value="2" selected>', false);

        // Assert the summary span shows the correct text
        $followResponse->assertSee('カード支払い');
    }
}
