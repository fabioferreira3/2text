<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\UnitTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UnitTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory()->create(),
            'amount' => $this->faker->randomNumber(3),
            'meta' => []
        ];
    }
}
