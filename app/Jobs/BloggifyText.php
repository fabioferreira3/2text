<?php

namespace App\Jobs;

use App\Models\TextRequest;
use App\Packages\ChatGPT\ChatGPT;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BloggifyText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TextRequest $textRequest;
    public ChatGPT $chatGpt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TextRequest $textRequest)
    {
        $this->textRequest = $textRequest;
        $this->chatGpt = new ChatGPT($this->textRequest->tone);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->textRequest->summary) {
            $this->generateSummary();
        }

        if (!$this->textRequest->subheaders) {
            $this->generateSubheaders();
        }

        $this->generateParagraphs();

        if (!$this->textRequest->title) {
            $this->generateTitle();
        }

        $this->generateIntro();
        $this->generateConclusion();
        if (!$this->textRequest->meta_description) {
            $this->generateMetaDescription();
        }
        $this->parseToHtmlTags();
    }

    public function generateSummary($keyword = null)
    {
        $sentences = collect(preg_split("/(?<=[.?!])\s+(?=([^\d\w]*[A-Z][^.?!]+))/", $this->textRequest->original_text, -1, PREG_SPLIT_NO_EMPTY));
        $paragraphs = collect([]);

        $sentences->chunk(12)->each(function ($chunk) use ($paragraphs) {
            $paragraphs->push($chunk);
        });

        $paragraphs = $paragraphs->map(function ($paragraph) {
            return $paragraph->join(' ');
        });

        $summarizedParagraphs = collect([]);
        $messages = collect([]);

        // Paragraphs generation
        $paragraphs->each(function ($paragraph) use (&$messages, &$summarizedParagraphs, $keyword) {
            $allContent = $messages->map(function ($message) {
                return $message['content'];
            })->join("");
            $tokenCount = $this->chatGpt->countTokens($allContent);
            $assistantContent = $messages->filter(function ($message) {
                return $message['role'] === 'assistant';
            })->map(function ($message) {
                return $message['content'];
            })->join("");

            if ($tokenCount > 3000) {
                $messages = collect([]);
                $summarizedParagraphs = collect([]);
                $response = $this->chatGpt->request([[
                    'role' => 'user',
                    'content' => "Summarize the following text using maximum of 750 words: \n\n" . $assistantContent
                ]]);
            } else {
                $instruction = "Rewrite the following text in the first person using different words";

                if ($keyword) {
                    $instruction . " and including the keyword \"" . $keyword . "\"";
                }
                $messages->push([
                    'role' => 'user',
                    'content' => $instruction . ": \n\n" . $paragraph
                ]);
                $response = $this->chatGpt->request($messages->toArray());
            }

            $messages->push($response['choices'][0]['message']);
            $summarizedParagraphs->push($response['choices'][0]['message']['content']);
        });

        $mainSummary = $summarizedParagraphs->join(' ');
        $this->textRequest->update(['summary' => $mainSummary]);
    }

    public function generateSubheaders()
    {
        $keyword = $this->textRequest->keyword;
        $tone = $this->textRequest->tone;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Create 7 subheaders, with maximum of 7 words each, using the keyword $keyword, with a $tone tone, for the following text: \n\n" . $this->textRequest->summary
        ]]);
        $rawSubheaders = $response['choices'][0]['message']['content'];
        $subheaderArray = explode("\n", $rawSubheaders);
        $subheaders = array_filter($subheaderArray, function ($subheader) {
            return is_numeric(substr(trim($subheader), 0, 1));
        });
        $this->textRequest->update(['subheaders' => $subheaders]);
    }

    public function generateParagraphs()
    {
        $mainText = "";
        $keyword = $this->textRequest->keyword;
        $tone = $this->textRequest->tone;
        foreach ($this->textRequest->subheaders as $subheader) {
            $response = $this->chatGpt->request([[
                'role' => 'user',
                'content' => "Taking into account the following summary for context: \n\n" . $this->textRequest->summary . ". " . $mainText .
                    "\n\n Write 3 paragraphs, in the first person, using the keyword $keyword, with a $tone tone, about the following subtopic \n \"" . $subheader . "\""
            ]]);
            $mainText .= "\n" . "<h2>" . preg_replace('/^\d+\./', '', $subheader) . "</h2>" . "\n" . $response['choices'][0]['message']['content'];
        }
        $this->textRequest->update(['final_text' => $mainText]);
    }

    public function generateTitle()
    {
        $keyword = $this->textRequest->keyword;
        $tone = $this->textRequest->tone;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Write a title, with a maximum of 7 words, using the keyword $keyword and with a $tone tone, for the following text: \n\n" . $this->textRequest->final_text
        ]]);
        $title = $response['choices'][0]['message']['content'];
        $this->textRequest->update(['title' => $title]);
    }

    public function generateIntro()
    {
        $tone = $this->textRequest->tone;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Write an intro, with a $tone tone, for the following title: \n\n" . $this->textRequest->title
        ]]);
        $intro = $response['choices'][0]['message']['content'];
        $mainText = $intro . "\n" . $this->textRequest->final_text;
        $this->textRequest->update(['final_text' => $mainText]);
    }

    public function generateConclusion()
    {
        $tone = $this->textRequest->tone;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Write a conclusion, with a $tone tone, highlighting the most important parts of the following text: \n\n" . $this->textRequest->final_text
        ]]);
        $end = $response['choices'][0]['message']['content'];
        $mainText = $this->textRequest->final_text . "\n" . $end;
        $this->textRequest->update(['final_text' => $mainText]);
    }

    public function parseToHtmlTags()
    {
        $paragraphs = explode("\n", $this->textRequest->final_text);
        $ptagsArray = collect([]);
        foreach ($paragraphs as $paragraph) {
            if (strpos($paragraph, "<") === false || strpos($paragraph, ">") === false) {
                $ptagsArray->push("<p>" . trim($paragraph) . "</p>");
            } else {
                $ptagsArray->push($paragraph);
            }
        }
        $finalText = preg_replace('/<[^\/>][^>]*><\/[^>]+>/', '', $ptagsArray->join(''));
        $this->textRequest->update(['final_text' => $finalText]);
    }

    public function generateMetaDescription()
    {
        $tone = $this->textRequest->tone;
        $keyword = $this->textRequest->keyword;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Write a meta description of a maximum of 20 words, with a $tone tone, using the keyword $keyword, for the following text: \n\n" . $this->textRequest->final_text
        ]]);
        $meta = $response['choices'][0]['message']['content'];
        $this->textRequest->update(['meta_description' => $meta]);
    }
}
