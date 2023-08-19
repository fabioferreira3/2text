<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserEmail extends Mailable
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
        $name = 'Experior';

        return $this->view('emails.new-user')
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject('New user!')
            ->with([
                'name' => $this->data['name'],
                'email' => $this->data['email']
            ]);
    }
}
