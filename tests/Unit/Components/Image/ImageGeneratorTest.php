<?php

use App\Enums\DocumentTaskEnum;
use App\Enums\DocumentType;
use App\Exceptions\InsufficientUnitsException;
use App\Jobs\DispatchDocumentTasks;
use App\Livewire\Image\ImageGenerator;
use App\Models\Document;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(ImageGenerator::class);
});

describe(
    'Image Generator component',
    function () {
        it('renders the image generator modal', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.image.generator');
        });

        it('fails to generate image when with insufficient units', function () {
            $this->authUser->account->update(['units' => 5]);
            $this->component
                ->call('generate')
                ->assertDispatched('alert', type: 'error', message: __('alerts.image_description'))
                ->set('prompt', 'test')
                ->set('samples', 7)
                ->call('generate')
                ->assertDispatched('alert', type: 'error', message: __('alerts.insufficient_units'));

            $this->authUser->account->update(['units' => 7]);
            $this->component->call('generate')
                ->assertNotDispatched('alert', type: 'error', message: __('alerts.insufficient_units'));
        });

        it('register image generation tasks', function () {
            Bus::fake(DispatchDocumentTasks::class);
            $this->authUser->account->update(['units' => 999]);
            $this->component
                ->set('prompt', 'test')
                ->set('samples', 7)
                ->call('generate');
            $document = Document::firstWhere('type', DocumentType::GENERIC);

            for ($i = 1; $i <= 7; $i++) {
                $this->assertDatabaseHas(
                    'document_tasks',
                    [
                        'document_id' => $document->id,
                        'name' => DocumentTaskEnum::GENERATE_IMAGE,
                        'meta->prompt' => 'test',
                        'meta->height' => '1024',
                        'meta->width' => '1024'
                    ]
                );
            }

            Bus::assertDispatched(DispatchDocumentTasks::class, 1);
        });
    }
)->group('image');
