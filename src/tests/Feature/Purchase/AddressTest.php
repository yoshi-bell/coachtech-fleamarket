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

class AddressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\ConditionSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_can_update_shipping_address_and_it_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $newAddress = [
            'postcode' => '987-6543',
            'address' => 'New Test Address',
            'building' => 'New Test Building',
        ];

        $this->actingAs($user)->patch(route('purchase.address.update', ['item' => $item->id]), $newAddress);

        $response = $this->actingAs($user)->get(route('purchase.create', ['item' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('987-6543');
        $response->assertSee('New Test Address');
        $response->assertSee('New Test Building');
    }

    /** @test */
    public function test_updated_address_is_used_for_purchase()
    {
        $buyer = User::factory()->has(Profile::factory(['address' => 'Old Address']))->create();
        $item = Item::factory()->create();

        $newAddress = [
            'postcode' => '987-6543',
            'address' => 'New Purchase Address',
            'building' => 'New Purchase Building',
        ];

        $this->actingAs($buyer)->patch(route('purchase.address.update', ['item' => $item->id]), $newAddress);

        // Mock Stripe
        $mockSession = Mockery::mock('alias:' . Session::class);
        $mockSession->shouldReceive('retrieve')->once()->andReturn((object)[
            'payment_intent' => 'pi_123',
            'metadata' => (
                (object)[
                    'item_id' => $item->id,
                    'buyer_id' => $buyer->id,
                    'postcode' => $newAddress['postcode'],
                    'address' => $newAddress['address'],
                    'building' => $newAddress['building'],
                ]
            )
        ]);

        $mockPaymentIntent = Mockery::mock('alias:' . PaymentIntent::class);
        $mockPaymentIntent->shouldReceive('retrieve')->once()->andReturn((object)['payment_method_types' => ['card']]);

        // Simulate successful purchase
        $this->actingAs($buyer)->get('/purchase/success/' . $item->id . '?session_id=cs_123');

        $this->assertDatabaseHas('sold_items', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'address' => 'New Purchase Address',
        ]);
    }
}
