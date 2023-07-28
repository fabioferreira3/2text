<?php

namespace App\Jobs\Contact;

use App\Enums\DocumentType;
use App\Jobs\Traits\JobEndings;
use App\Mail\FinishedProcessEmail;
use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyFinished implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    private User $user;
    private Document $document;
    private string $jobName;

    /**
     * Construct the job.
     *
     * @return void
     */
    public function __construct(Document $document, string $userId)
    {
        $this->document = $document;
        $this->jobName = $document->type->label();
        $this->user = User::findOrFail($userId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $routeName =  DocumentType::routeNames()[$this->document->type->value];
        $data = [
            'name' => $this->user->name,
            'jobName' => $this->jobName,
            'link' => route($routeName, ['document' => $this->document]),
            'subject' => "Your $this->jobName is ready!"
        ];
        Mail::to('fabio86ferreira@gmail.com')->send(new FinishedProcessEmail($data));
    }
}
