<?php

namespace Tests\Feature\Purchase;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /** @test */
    public function test_redirects_to_item_list_after_purchase()
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

        $response = $this->actingAs($buyer)->get('/purchase/success/' . $item->id . '?session_id=cs_123');

        $response->assertRedirect(route('index'));
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
