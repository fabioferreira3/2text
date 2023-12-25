<?php

use App\Http\Livewire\AudioTranscription\Dashboard;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Dashboard::class);
});

describe(
    'Audio Transcription Dashboard component',
    function () {
        test('renders the dashboard view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.audio-transcription.dashboard');
        });

        test('renders the new transcription view', function () {
            $this->component->call('new')->assertRedirect('/transcription/new');
        });
    }
)->group('audio-transcription');
