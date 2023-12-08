<?php

use App\Models\User;

beforeEach(function () {
    $this->request = $this->actingAs(User::factory()->create());
});

describe('Routes', function () {
    test('it renders the main dashboard component', function () {
        $this->request->get('/dashboard')->assertSeeLivewire('dashboard');
    });

    test('it renders the social media dashboard component', function () {
        $this->request->get('/social-media-post')->assertSeeLivewire('social-media-post.dashboard');
    });

    test('it renders the blog dashboard component', function () {
        $this->request->get('/blog')->assertSeeLivewire('blog.dashboard');
    });

    test('it renders the summarizer dashboard component', function () {
        $this->request->get('/summarizer')->assertSeeLivewire('summarizer.dashboard');
    });

    test('it renders the audio transcription dashboard component', function () {
        $this->request->get('/transcription')->assertSeeLivewire('audio-transcription.dashboard');
    });

    test('it renders the paraphraser dashboard component', function () {
        $this->request->get('/paraphraser')->assertSeeLivewire('paraphraser.dashboard');
    });

    test('it renders the inquiry hub dashboard component', function () {
        $this->request->get('/inquiry-hub')->assertSeeLivewire('inquiry-hub.dashboard');
    });
});
