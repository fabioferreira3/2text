<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Thread;
use App\Models\ThreadMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ThreadMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'thread_id' => Thread::factory()->create(),
            'role' => 'user',
            'content' => [],
            'attachments' => []
        ];
    }
}
