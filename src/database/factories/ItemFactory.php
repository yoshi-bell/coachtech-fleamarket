<?php

namespace Database\Factories;

use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'seller_id' => User::factory(),
            'condition_id' => $this->faker->numberBetween(1, 4),
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'img_url' => 'dummy.jpg',
        ];
    }
}
