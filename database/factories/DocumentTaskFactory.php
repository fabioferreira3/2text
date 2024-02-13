<?php

namespace Database\Factories;

use App\Enums\DocumentTaskEnum;
use App\Models\Document;
use App\Models\DocumentTask;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocumentTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentTask::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $documentTask = DocumentTaskEnum::CREATE_OUTLINE;
        return [
            'name' => $documentTask->value,
            'document_id' => Document::factory()->create(),
            'process_id' => Str::uuid(),
            'process_group_id' => null,
            'job' => $documentTask->getJob(),
            'status' => 'ready',
            'meta' => [],
            'order' => 1
        ];
    }
}
