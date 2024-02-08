<?php

namespace Database\Factories;

use App\Models\ChatThread;
use App\Models\ChatThreadIteration;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatThreadIterationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChatThreadIteration::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'chat_thread_id' => ChatThread::factory()->create(),
            'origin' => $this->faker->randomElement(['user', 'sys']),
            'response' => $this->faker->sentence(),
        ];
    }
}
