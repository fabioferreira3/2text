<?php

use App\Enums\DocumentTaskEnum;
use App\Livewire\AudioTranscription\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Audio Transcription Dashboard component',
    function () {
        it('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.audio-transcription.dashboard');
        });

        it('renders the new transcription view', function () {
            $this->component->call('new')->assertRedirect('/transcription/new');
        });

        it('redirects to new transcription view', function () {
            $this->component->dispatch('invokeNew')->assertRedirect(route('new-audio-transcription'));
        });

        it('handles insufficient units event', function ($eventTask) {
            $this->component->dispatch('InsufficientUnitsValidated', (object) ['task' => $eventTask])
                ->assertDispatched('alert', type: 'error', message: __('alerts.insufficient_units'));
        })->with([
            DocumentTaskEnum::TRANSCRIBE_AUDIO->value,
            DocumentTaskEnum::TRANSCRIBE_AUDIO_WITH_DIARIZATION->value
        ]);
    }
)->group('audio-transcription');
