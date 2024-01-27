<?php

namespace App\Packages\SendGrid;

use Illuminate\Support\Facades\Log;
use SendGrid\Mail\Mail;

class SendGrid
{
    protected Mail $mail;
    protected \SendGrid $sendGrid;

    public function __construct()
    {
        $this->mail = new Mail();
        $this->mail->setFrom(config('sendgrid.from'), 'Experior AI');
        $this->sendGrid = new \SendGrid(config('sendgrid.api_key'));
    }

    public function sendDynamicMessage(array $recipients, array $contentPayload, $templateId = null)
    {
        foreach ($recipients as $recipient) {
            $this->mail->addTo(
                $recipient['email'],
                $recipient['name'] ?? '',
                $contentPayload
            );
            if (isset($contentPayload['attachment'])) {
                $this->mail->addAttachment(
                    base64_encode(file_get_contents($contentPayload['attachment']['path'])),
                    $contentPayload['attachment']['mimetype'],
                    $contentPayload['attachment']['filename'],
                    'attachment'
                );
            }
        }
        if ($templateId) {
            $this->mail->setTemplateId($templateId);
        }

        return $this->sendMessage();
    }

    public function sendSimpleMessage(string $recipient, string $subject, string $content)
    {
        $this->mail->addTo($recipient);
        $this->mail->setSubject($subject);
        $this->mail->addContent('text/plain', $content);
        return $this->sendMessage();
    }

    public function sendMessage()
    {
        try {
            $response = $this->sendGrid->send($this->mail);
            $this->log($response);
            return $response;
        } catch (\Exception $e) {
            Log::error("{$e->getCode()} - {$e->getMessage()}", [
                'context' => 'sendgrid'
            ]);
            return new \Exception($e->getMessage());
        }
    }

    protected function log($response)
    {
        $recipients = json_encode($this->mail->getPersonalization()->getTos());
        $subject = json_encode($this->mail->getGlobalSubject());
        Log::info(
            "StatusCode: {$response->statusCode()} - Response Body: {$response->body()} - Subject: {$subject} - Recipients: {$recipients}",
            [
                'context' => 'sendgrid'
            ]
        );
    }
}
