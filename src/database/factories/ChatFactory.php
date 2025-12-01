<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition()
    {
        return [
            'sender_id' => User::factory(),
            'sold_item_id' => \App\Models\SoldItem::factory(),
            'message' => $this->faker->sentence(),
            'image_path' => null,
        ];
    }
}
