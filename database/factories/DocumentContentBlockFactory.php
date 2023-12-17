<?php

namespace Database\Factories;

use App\Enums\Language;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentContentBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentContentBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'document_id' => Document::factory()->create(),
            'type' => 'text',
            'content' => 'some content',
            'prompt' => null,
            'prefix' => null,
            'order' => 1
        ];
    }
}
