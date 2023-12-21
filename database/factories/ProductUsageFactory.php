<?php

namespace Database\Factories;

use App\Enums\AIModel;
use App\Models\Account;
use App\Models\ProductUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductUsageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductUsage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory()->create(),
            'user_id' => User::factory()->create(),
            'model' => $this->faker->randomElement(AIModel::getValues()),
            'prompt_token_usage' => $this->faker->randomNumber(4),
            'completion_token_usage' => $this->faker->randomNumber(4),
            'total_token_usage' => $this->faker->randomNumber(4),
            'cost' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
