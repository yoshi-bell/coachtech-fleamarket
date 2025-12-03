<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\SoldItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition()
    {
        return [
            'rater_id' => User::factory(),
            'rated_user_id' => User::factory(),
            'sold_item_id' => SoldItem::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
        ];
    }
}
