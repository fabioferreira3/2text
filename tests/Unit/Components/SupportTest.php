<?php

use App\Livewire\Support\Support;

use function Pest\Laravel\{actingAs};

beforeEach(function () {
    $this->component = actingAs($this->authUser)->livewire(Support::class);
});

describe(
    'Support component',
    function () {
        it('renders the support page view', function () {
            $this->component->assertStatus(200)
                ->assertViewIs('livewire.support.support')
                ->assertSet('tool', [])
                ->assertSet('messageSent', false);
        });

        it('fails validation with invalid reason', function ($reason) {
            $this->component
                ->set('reason', 'eita')
                ->call('submit')
                ->assertHasErrors(['reason' => 'in'])
                ->set('reason', $reason)
                ->call('submit')
                ->assertHasNoErrors(['reason']);
        })->with(['Help', 'Feedback', 'Bug', 'Billing', 'Suggestion', 'Other']);

        it('fails validation with invalid tool', function ($tool) {
            $this->component
                ->set('tool', ['video_tool', $tool])
                ->call('submit')
                ->assertHasErrors(['tool'])
                ->set('tool', [$tool])
                ->call('submit')
                ->assertHasNoErrors(['tool']);
        })->with([
            'Social Media',
            'Blog Post',
            'AI Image',
            'Paraphraser',
            'Text to Audio',
            'Transcription',
            'Summarizer',
            'Insight Hub'
        ]);

        it('fails validation with empty message', function () {
            $this->component
                ->set('message', '')
                ->call('submit')
                ->assertHasErrors(['message' => 'required'])
                ->set('message', 'some message')
                ->call('submit')
                ->assertHasNoErrors(['message']);
        });

        it('creates a comm after submiting a valid form', function () {
            $this->component
                ->set('reason', 'Bug')
                ->set('message', 'some message')
                ->call('submit')
                ->assertHasNoErrors()
                ->assertSet('messageSent', true);

            $this->assertDatabaseHas('comms', [
                'user_id' => $this->authUser->id,
                'meta->reason' => 'Bug',
                'meta->tool' => null,
                'content' => 'some message',
                'type' => 'user'
            ]);
        });
    }
);
