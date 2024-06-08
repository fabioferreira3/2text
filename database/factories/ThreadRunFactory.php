<?php

namespace Database\Factories;

use App\Domain\Thread\Enum\RunStatus;
use App\Domain\Thread\Thread;
use App\Domain\Thread\ThreadRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadRunFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ThreadRun::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'thread_id' => Thread::factory()->create(),
            'assistant_id' => $this->faker->uuid(),
            'run_id' => $this->faker->uuid(),
            'status' => RunStatus::QUEUED,
            'completed_at' => null,
            'failed_at' => null,
            'canceled_at' => null,
            'meta' => []
        ];
    }
}
