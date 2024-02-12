<?php

namespace Tests\Unit\Jobs\Contact;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Support\Notifications\Channels\CustomMailChannel;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->notification = new WelcomeNotification();
});

describe('WelcomeNotification', function () {
    test('via method', function () {
        expect($this->notification->via($this->user))
            ->toBeArray()
            ->toBe([CustomMailChannel::class]);
    });

    it('sets notification data', function () {
        expect($this->notification->toCustomMail($this->user))
            ->toBeArray()
            ->toBe([
                'recipients' => [[
                    'name' => $this->user->name,
                    'email' => $this->user->email
                ]],
                'payload' => [
                    'first_name' => $this->user->name
                ],
                'template_id' => 'd-0809ad8ab17e423d8401acff70dd6eee'
            ]);
    });
})->group('notifications');
