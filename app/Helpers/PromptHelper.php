<?php

namespace App\Helpers;

use App\Enums\Tone;
use Illuminate\Support\Facades\Lang;

class PromptHelper
{
    protected string $language;

    public function __construct($language = 'en')
    {
        $this->language = $language;
    }

    public function summarize($text)
    {
        return Lang::get('prompt.summarize_text', ['text' => $text], $this->language);
    }

    public function simplify($text)
    {
        return Lang::get('prompt.simplify_text', ['text' => $text], $this->language);
    }

    public function writeFirstPass($outline, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        return Lang::get('prompt.blog_first_pass', ['tone' => $tone, 'outline' => $outline], $this->language);
    }

    public function writeTitle($context, $tone = 'casual', $keyword = null)
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        $prompt = Lang::get('prompt.write_title', ['context' => $context, 'tone' => $tone], $this->language);
        if ($keyword) {
            $prompt .= Lang::get('prompt.keyword_instructions', ['keyword' => $keyword], $this->language);
        }

        return $prompt;
    }

    public function writeOutline($context, $maxSubtopics, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        return Lang::get('prompt.write_outline', ['tone' => $tone, 'maxsubtopics' => $maxSubtopics, 'context' => $context]);
    }


    public function givenFollowingText($text)
    {
        return Lang::get('prompt.given_following_text', ['text' => $text], $this->language);
    }

    public function andGivenFollowingContext($text)
    {
        return Lang::get('prompt.given_following_context', ['context' => preg_replace('/\s+/', ' ', $text)], $this->language);
    }

    public function expandOn($text, $tone = 'casual')
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        return Lang::get('prompt.expand_text', ['tone' => $tone, 'context' => $text], $this->language);
    }

    public function writeMetaDescription($text, $tone = 'casual', $keyword = null)
    {
        $tone = Tone::fromLanguage($tone, $this->language);
        $prompt = Lang::get('prompt.write_meta_description', [], $this->language);
        if ($keyword) {
            $prompt .= Lang::get('prompt.keyword_instructions', ['keyword' => $keyword], $this->language);
        }
        $prompt .= Lang::get('prompt.meta_description_context_instructions', ['context' => $text], $this->language);

        return $prompt;
    }

    public function writeSocialMediaPost(array $params)
    {
        $tone = Tone::fromLanguage($params['tone'], $this->language);
        $basePrompt = Lang::get('prompt.write_social_media_post', ['platform' => $params['platform']], $this->language);
        if ($params['keyword'] ?? false) {
            $basePrompt .= Lang::get('prompt.keyword_instructions', ['keyword' => $params['keyword']], $this->language);
        }
        $basePrompt .= Lang::get('prompt.post_tone_instructions', ['tone' => $tone], $this->language);
        $basePrompt .= Lang::get('prompt.post_context_instructions', ['context' => $params['context']], $this->language);
        if ($params['more_instructions']) {
            $basePrompt .= Lang::get('prompt.more_instructions', ['instructions' => $params['more_instructions']], $this->language);
        }
        return $basePrompt;
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }
}
