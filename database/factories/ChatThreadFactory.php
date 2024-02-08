<?php

namespace Database\Factories;

use App\Models\ChatThread;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ChatThread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user,
            'document_id' => Document::factory()->create([
                'account_id' => $user->account_id,
                'meta' => [
                    'user_id' => $user->id
                ]
            ]),
            'name' => $this->faker->word()
        ];
    }
}
