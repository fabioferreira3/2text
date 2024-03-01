<?php

namespace Database\Factories;

use App\Models\Comm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(),
            'context' => $this->faker->text(),
            'type' => 'sys',
            'template_id' => null,
            'meta' => ['key' => 'value']
        ];
    }
}
