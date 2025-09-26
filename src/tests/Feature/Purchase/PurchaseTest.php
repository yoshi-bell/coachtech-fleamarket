<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
        $this->seed(\Database\Seeders\CategorySeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_validation_error_if_purchasing_without_address()
    {
        $user = User::factory()->create(); // User without a profile/address
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post('/purchase/' . $item->id, [
            'payment_method' => '1'
        ]);

        $response->assertSessionHasErrors('shipping_address');
    }

    /** @test */
    public function test_payment_method_is_required()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post('/purchase/' . $item->id, [
            // 'payment_method' => '2' // Intentionally omitted
        ]);

        $response->assertSessionHasErrors('payment_method');
    }

    /** @test */
    public function test_payment_method_selection_is_reflected_on_validation_error()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $item = Item::factory()->create();

        // From a page that has the form
        $from = route('purchase.create', $item);

        // Submit the form with payment_method=2 but trigger a validation error elsewhere
        // (shipping_address is required in the FormRequest but not submitted)
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
        $followResponse->assertSee('クレジットカード');
    }

    /** @test */
    public function test_user_can_initiate_purchase()
    {
        $user = User::factory()->has(Profile::factory())->create();
        $item = Item::factory()->create();

        $mockSession = Mockery::mock('alias:' . Session::class);
        $mockSession->shouldReceive('create')->once()->andReturn((object)['url' => 'https://stripe.com/test_url']);

        $response = $this->actingAs($user)->post('/purchase/' . $item->id, ['payment_method' => '2']); // 2 for card

        $response->assertStatus(303);
        $response->assertRedirect('https://stripe.com/test_url');
    }

    /** @test */
    public function test_purchased_item_is_marked_as_sold()
    {
        [$item, $buyer] = $this->simulate_successful_purchase();

        $response = $this->get('/');
        $response->assertSee('Sold');
    }

    /** @test */
    public function test_purchased_item_is_added_to_user_profile()
    {
        [$item, $buyer] = $this->simulate_successful_purchase();

        $response = $this->actingAs($buyer)->get(route('mypage.show', ['page' => 'buy']));
        $response->assertSee($item->name);
    }

    private function simulate_successful_purchase()
    {
        $buyer = User::factory()->create();
        $item = Item::factory()->create();

        $mockSession = Mockery::mock('alias:' . Session::class);
        $mockSession->shouldReceive('retrieve')->once()->andReturn((object)[
            'payment_intent' => 'pi_123',
            'metadata' => (
                (object)[
                    'item_id' => $item->id,
                    'buyer_id' => $buyer->id,
                    'postcode' => '123-4567',
                    'address' => 'Test Address',
                    'building' => 'Test Building',
                ]
            )
        ]);

        $mockPaymentIntent = Mockery::mock('alias:' . PaymentIntent::class);
        $mockPaymentIntent->shouldReceive('retrieve')->once()->andReturn((object)['payment_method_types' => ['card']]);

        $this->actingAs($buyer)->get('/purchase/success/' . $item->id . '?session_id=cs_123');

        return [$item, $buyer];
    }
}
