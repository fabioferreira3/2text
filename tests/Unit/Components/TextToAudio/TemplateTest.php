<?php

use App\Livewire\TextToAudio\Template;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Template::class);
});

describe(
    'Text to Audio Template component',
    function () {
        it('renders the text to audio template view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.common.template');
        });

        it('redirects to create new text to audio', function () {
            $this->component->call('execute')->assertRedirect('/text-to-audio/new');
        });
    }
)->group('text-to-audio');
