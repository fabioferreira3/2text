<?php

use App\Livewire\Summarizer\Template;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Template::class);
});

describe(
    'Summarizer Template component',
    function () {
        it('renders the summarizer template view', function () {
            $this->component->assertStatus(200)->assertViewIs('livewire.common.template');
        });

        it('redirects to create new summary', function () {
            $this->component->call('execute')->assertRedirect('/summarizer/new');
        });
    }
)->group('summarizer');
