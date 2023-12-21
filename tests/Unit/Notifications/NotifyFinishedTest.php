<?php

namespace Tests\Unit\Jobs\Contact;

use App\Enums\DocumentType;
use App\Jobs\Contact\NotifyFinished;
use App\Mail\FinishedProcessEmail;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

describe('NotifiyFinished notification', function () {
    it('sends email', function () {
        Mail::fake();

        $user = User::factory()->create();
        $document = Document::factory()->create(['type' => DocumentType::BLOG_POST->value]);

        $job = new NotifyFinished($document, $user->id);
        $job->handle();

        Mail::assertSent(FinishedProcessEmail::class, function ($mail) use ($user, $document) {
            return $mail->hasTo($user->email) &&
                $mail->data['subject'] === "Your " . DocumentType::BLOG_POST->label() . " is ready!" &&
                $mail->data['name'] === $user->name && $mail->data['jobName'] === $document->type->label();
        });
    });
})->group('notifications');
