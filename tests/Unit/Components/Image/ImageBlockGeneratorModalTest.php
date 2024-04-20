<?php

use App\Jobs\DispatchDocumentTasks;
use App\Livewire\Image\ImageBlockGeneratorModal;
use App\Models\Document;
use App\Models\DocumentContentBlock;
use App\Models\DocumentTask;
use App\Models\MediaFile;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->be($this->authUser);
    $this->document = Document::factory()->create([
        'meta' => [
            'img_prompt' => 'Image prompt',
            'img_style' => 'image_style'
        ]
    ]);
    $this->mediaFiles = MediaFile::factory(4)->create([
        'meta' => [
            'document_id' => $this->document->id
        ]
    ]);

    $this->contentBlock = DocumentContentBlock::factory()->create([
        'type' => 'media_file_image',
        'content' => $this->mediaFiles[0]->id,
        'document_id' => $this->document->id
    ]);
    $this->component = actingAs($this->authUser)->livewire(ImageBlockGeneratorModal::class, [
        'contentBlock' => $this->contentBlock,
    ]);
});

describe(
    'Image Block Generator modal component',
    function () {
        it('renders the image block generator modal', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.image.image-block-generator-modal')
                ->assertSet('processing', false)
                ->assertSet('samples', 1)
                ->assertSet('processId', '');
            expect($this->component->previewImgs['original']->id)->toBe($this->mediaFiles[0]->id);
            expect($this->component->previewImgs['variants']->pluck('id')->toArray())
                ->toMatchArray($this->mediaFiles->pluck('id')->toArray());
        });

        it('toggles the modal', function () {
            $this->component->call('toggleModal')
                ->assertDispatched('toggleImageGenerator');
        });

        it('selects an image', function () {
            foreach ($this->mediaFiles as $mediaFile) {
                $this->component->call('selectImage', $mediaFile->id)
                    ->assertDispatched(
                        'imageSelected',
                        mediaFileId: $mediaFile->id
                    );
            }
        });

        it('refreshes the images when the process is finished', function ($hasSiblings, $groupFinished, $samples) {
            $processGroupId = $this->component->processGroupId;

            MediaFile::factory(10)->create([
                'meta' => [
                    'document_id' => $this->document->id,
                    'process_group_id' => $processGroupId
                ]
            ]);

            $this->component->set('samples', $samples)->call('onProcessFinished', [
                'has_siblings' => $hasSiblings,
                'process_group_id' => $processGroupId,
                'group_finished' => $groupFinished
            ])->assertSet('processing', false)
                ->assertDispatched('refreshImages')
                ->assertDispatched('alert', type: 'success', message: __('alerts.images_generated'));

            expect($this->component->previewImgs['variants'])
                ->toBeArray()
                ->toHaveCount($samples);
        })->with([
            [
                'has_siblings' => false,
                'group_finished' => false,
                'samples' => 2
            ],
            [
                'has_siblings' => true,
                'group_finished' => true,
                'samples' => 6
            ]
        ]);

        it('sets the processing state', function () {
            expect($this->component->processId)->toBe('');
            $this->component->call('setProcessingState')
                ->assertSet('processing', true)
                ->assertDispatched(
                    'alert',
                    type: 'info',
                    message: __('alerts.generating_images')
                );
            expect($this->component->processId)->toBeUuid();
        });

        it('previews an image', function () {
            $mediaFile = MediaFile::first();
            $this->component->call('previewImage', $mediaFile->id)
                ->assertDispatched('openLinkInNewTab', $mediaFile->file_url);
        });

        it('sets the main (original) image', function () {
            $mediaFile = MediaFile::first();
            $this->component->call('setOriginalPreviewImage', [
                'file_url' => $mediaFile->file_url
            ]);
            expect($this->component->previewImgs['original']->id)->toBe($mediaFile->id);
        });

        it('downloads an image', function () {
            $file = (new FileFactory)->image('test.jpg');
            $path = Storage::disk('local')->putFile($file);
            $mediaFile = MediaFile::first();
            $mediaFile->update([
                'file_path' => $path,
            ]);
            $this->component->call('downloadImage', $mediaFile->id)
                ->assertFileDownloaded($mediaFile->file_path);
        });

        it('validates the prompt', function () {
            $this->component->set('prompt', '')
                ->call('validateParams')
                ->assertDispatched(
                    'alert',
                    type: 'error',
                    message: __('alerts.image_description')
                )
                ->set('prompt', 'some prompt')
                ->call('validateParams')
                ->assertNotDispatched(
                    'alert',
                    type: 'error',
                    message: __('alerts.image_description')
                );
        });

        it('generates new images', function ($samples) {
            $processGroupId = $this->component->processGroupId;
            $this->component->set('samples', $samples)
                ->call('generateNewImages')
                ->assertSet('action', __('modals.new_images'))
                ->assertSet('processing', true)
                ->assertDispatched(
                    'alert',
                    type: 'info',
                    message: __('alerts.generating_images')
                );

            $this->assertDatabaseCount('document_tasks', $samples);
            $documentTasks = DocumentTask::all();
            $documentTasks->each(function ($task) use ($processGroupId) {
                expect($task->process_group_id)->toBe($processGroupId);
                expect($task->document_id)->toBe($this->document->id);
                expect($task->meta['prompt'])->toBe('Image prompt');
                expect($task->meta['height'])->toBe(1024);
                expect($task->meta['width'])->toBe(1024);
            });
            expect($this->contentBlock->document->getMeta('img_prompt'))->toBe('Image prompt');
            expect($this->component->processId)->toBeUuid();
            Bus::assertDispatched(DispatchDocumentTasks::class, function ($job) {
                return $job->document->id === $this->document->id;
            });
        })->with([1, 3, 5]);

        it('doesnt generate new images when prompt is empty', function () {
            $this->component->set('prompt', '')
                ->call('generateNewImages')
                ->assertSet('processing', false)
                ->assertNotDispatched(
                    'alert',
                    type: 'info',
                    message: __('alerts.generating_images')
                );

            $this->assertDatabaseCount('document_tasks', 0);
            Bus::assertNotDispatched(DispatchDocumentTasks::class);
        });
    }
)->group('image');
