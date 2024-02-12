<?php

use App\Livewire\AudioTranscription\Template;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Template::class);
});

describe(
    'AudioTranscription Template component',
    function () {
        it('renders the audio transcription template view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.common.template');
        });

        it('redirects to create new audio transcription', function () {
            $this->component->call('execute')->assertRedirect('/transcription/new');
        });
    }
)->group('audio-transcription');
