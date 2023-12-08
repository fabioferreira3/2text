<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\Language;
use App\Models\Account;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->sentences(3, true),
            'title' => $this->faker->sentence(),
            'type' => DocumentType::BLOG_POST,
            'meta' => [
                'user_id' => User::factory()->create()->id,
            ],
            'language' => Language::ENGLISH,
            'word_count' => 100,
            'account_id' => Account::factory()->create()
        ];
    }
}
