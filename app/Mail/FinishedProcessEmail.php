<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinishedProcessEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $address = 'contact@experior.ai';
        $subject = $this->data['subject'];
        $name = 'Experior';

        return $this->view('emails.finished-process')
            ->from($address, $name)
            // ->cc($address, $name)
            // ->bcc($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([
                'name' => $this->data['name'],
                'link' => $this->data['link'],
                'jobName' => $this->data['jobName'],
            ]);
    }
}
