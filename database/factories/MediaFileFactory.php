<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MediaFile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory()->create(),
            'file_url' => $this->faker->url(),
            'file_path' => $this->faker->filePath(),
            'type' => $this->faker->randomElement(['image', 'audio']),
            'meta' => "{}"
        ];
    }
}
