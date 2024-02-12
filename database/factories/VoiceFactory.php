<?php

namespace Database\Factories;

use App\Models\Voice;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Voice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'external_id' => $this->faker->uuid(),
            'provider' => $this->faker->word(),
            'model' => $this->faker->word(),
            'name' => $this->faker->word(),
            'meta' => [],
            'preview_url' => $this->faker->url()
        ];
    }
}
