<?php

namespace Tests\Unit\Events;

use App\Events\UserCreated;
use App\Listeners\HandleNewUserAdminNotification;
use App\Listeners\HandleWelcomeNotification;
use App\Mail\NewUserEmail;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

describe('UserCreated event', function () {
    it('triggers an admin notification', function () {
        Event::fake(UserCreated::class);
        Mail::fake();

        $user = User::factory()->create();
        $event = new UserCreated($user);
        $listener = new HandleNewUserAdminNotification();
        $listener->handle($event);

        Mail::assertSent(NewUserEmail::class, function ($mail) use ($user) {
            return $mail->hasTo('contact@experior.ai') &&
                $mail->data['email'] === $user->email &&
                $mail->data['name'] === $user->name;
        });
    });

    it('triggers a welcome email notification', function () {
        Event::fake(UserCreated::class);
        Notification::fake();

        $user = User::factory()->create();
        $event = new UserCreated($user);
        $listener = new HandleWelcomeNotification();
        $listener->handle($event);

        Notification::assertSentTo($user, WelcomeNotification::class);
    });
})->group('events');
