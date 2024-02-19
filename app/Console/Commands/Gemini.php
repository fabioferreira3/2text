<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Gemini\Laravel\Facades\Gemini as GoogleGemini;

class Gemini extends Command
{
    protected $signature = 'start:gemini';
    protected $description = 'Start an interactive chat session';

    public function handle()
    {
        $continueChat = true;
        $chat = GoogleGemini::chat();

        while ($continueChat) {
            $userInput = $this->ask('Enter your message (type "exit" to end the chat):');

            if ($userInput === 'exit') {
                $continueChat = false;
                $this->info('Chat session ended.');
            } else {
                $result = $chat->sendMessage($userInput);
                $this->info($result->text());
            }
        }
    }
}
