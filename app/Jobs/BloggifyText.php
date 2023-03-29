<?php

namespace App\Jobs;

use App\Helpers\TextRequestHelper;
use App\Models\TextRequest;
use App\Packages\ChatGPT\ChatGPT;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        $this->chatGpt = new ChatGPT();
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
            $this->increaseProgressBy(15);
        }

        if (!$this->textRequest->outline) {
            $this->generateOutline();
            $this->increaseProgressBy(15);
        }

        if (!$this->textRequest->final_text) {
            $this->expandText();
            $this->increaseProgressBy(50);
        }

        if (!$this->textRequest->meta_description) {
            $this->generateMetaDescription();
            $this->increaseProgressBy(10);
        }

        if (!$this->textRequest->title) {
            $this->generateTitle();
            $this->increaseProgressBy(10);
        }
    }

    public function increaseProgressBy(int $amount)
    {
        $this->textRequest->update(['progress' => $this->textRequest->progress + $amount]);
    }

    public function generateTitle()
    {
        $keyword = $this->textRequest->keyword;
        $tone = $this->textRequest->tone;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Write a title, with a maximum of 7 words, using the keyword $keyword and with a $tone tone, for the following text: \n\n" . $this->textRequest->final_text
        ]]);
        $this->textRequest->update(['title' => $response]);
    }

    public function generateSummary()
    {
        $sentences = collect(preg_split("/(?<=[.?!])\s+(?=([^\d\w]*[A-Z][^.?!]+))/", $this->textRequest->original_text, -1, PREG_SPLIT_NO_EMPTY));
        $paragraphs = collect([]);

        $sentences->chunk(12)->each(function ($chunk) use ($paragraphs) {
            $paragraphs->push($chunk);
        });

        $paragraphs = $paragraphs->map(function ($paragraph) {
            return $paragraph->join(' ');
        });

        $rewrittenParagraphs = collect([]);
        $messages = collect([]);

        // Paragraphs generation
        $paragraphs->each(function ($paragraph) use (&$messages, &$rewrittenParagraphs) {
            $allContent = $messages->map(function ($message) {
                return $message['content'];
            })->join("");
            $tokenCount = $this->chatGpt->countTokens($allContent);
            $assistantContent = $messages->filter(function ($message) {
                return $message['role'] === 'assistant';
            })->map(function ($message) {
                return $message['content'];
            })->join("");

            if ($tokenCount > 6000) {
                $messages = collect([]);
                $rewrittenParagraphs = collect([]);
                $response = $this->chatGpt->request([[
                    'role' => 'user',
                    'content' => "Summarize the following text using maximum of 2000 words: \n\n" . $assistantContent
                ]]);
            } else {
                $instruction = "Rewrite the following text using similar words";

                $messages->push([
                    'role' => 'user',
                    'content' => $instruction . ": \n\n" . $paragraph
                ]);
                $response = $this->chatGpt->request($messages->toArray());
            }

            $messages->push([
                'role' => 'assistant',
                'content' => $response
            ]);
            $rewrittenParagraphs->push($response);
        });
        $allRewrittenParagraphs = $rewrittenParagraphs->join(' ');

        $mainSummary = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Summarize the following text using a maximum of 1500 words:\n\n\n" . $allRewrittenParagraphs
        ]]);

        $this->textRequest->update(['summary' => $mainSummary]);
    }

    public function generateOutline()
    {
        $response = $this->chatGpt->request([
            [
                'role' => 'user',
                'content' =>   "Create an indept and comprehensive blog post outline, with maximum of two levels, with a " . $this->textRequest->tone . " tone, that will be " . $this->textRequest->target_word_count . " words long, using roman numerals indicating main topics and alphabet letters to indicate subtopics, for example: \n\n I. Main Topic \n A. Subtopic 1 \n B. Subtopic 2 \n C. Subtopic 3 \n\n The outline should be based on the following text: \n\n" . $this->textRequest->summary
            ]
        ]);
        Log::debug('Outline: ' . $response);
        $this->textRequest->update(['outline' => $response, 'raw_structure' => TextRequestHelper::parseOutlineToRawStructure($response)]);
    }

    // public function generateFirstExpansion()
    // {
    //     $response = $this->chatGpt->request([
    //         [
    //             'role' => 'user',
    //             'content' =>  "Write a blog post, with a " . $this->textRequest->tone . " tone, using the keyword '" . $this->textRequest->keyword . "', using the following structure as example: \n\n" .  "I. Topic \n   <p>Paragraph 1</p><p>Paragraph 2</p>\n\n\nAnd using this exact outline: \n\n" . $this->textRequest->outline . "\n\n\nThis is a summary to also be used as context: \n\n" . $this->textRequest->summary
    //         ]
    //     ]);

    //     Log::debug('First expansion:' . $response);
    //     $this->textRequest->update(['raw_structure' => $this->parseExpandedTextToRawStructure($response)]);
    // }

    public function expandText()
    {
        $rawStructure = $this->textRequest->raw_structure;

        foreach ($this->textRequest->raw_structure as $key => $section) {
            $response = $this->chatGpt->request([
                [
                    'role' => 'user',
                    'content' =>  "Given the following text: \n\n" . $this->textRequest->normalized_structure . "\n\n\nUsing a " . $this->textRequest->tone . " tone, and using <p> tags, expand more on: \n\n" . "<h2>" . $section['subheader'] . "</h2>" . collect($section['content'])->implode('.')
                ]

            ]);
            Log::debug($response);
            $rawStructure[$key]['content'] = TextRequestHelper::parseHtmlTagsToRawStructure($response);
            $this->textRequest->update(['raw_structure' => $rawStructure]);
            $this->textRequest->refresh();
        }

        $this->textRequest->update(['final_text' => $this->textRequest->normalized_structure]);
    }

    public function generateMetaDescription()
    {
        $tone = $this->textRequest->tone;
        $keyword = $this->textRequest->keyword;
        $response = $this->chatGpt->request([[
            'role' => 'user',
            'content' => "Write a meta description of a maximum of 20 words, with a $tone tone, using the keyword $keyword, for the following text: \n\n" . $this->textRequest->final_text
        ]]);
        $meta = $response;
        $this->textRequest->update(['meta_description' => $meta]);
    }
}
