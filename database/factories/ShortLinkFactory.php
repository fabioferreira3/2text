<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShortLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShortLink::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory()->create(),
            'link' => $this->faker->url(),
            'target_url' => $this->faker->url(),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year')
        ];
    }
}
