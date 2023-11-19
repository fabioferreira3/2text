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

    public function generic($prompt)
    {
        return Lang::get('prompt.generic_prompt', ['prompt' => $prompt], $this->language);
    }

    public function modifyText($customPrompt, $text)
    {
        return Lang::get('prompt.modify_text', [
            'customPrompt' => $customPrompt,
            'text' => $text
        ], $this->language);
    }

    public function summarize($text)
    {
        return Lang::get('prompt.summarize_text', ['text' => $text], $this->language);
    }

    public function simplify($text)
    {
        return Lang::get('prompt.simplify_text', ['text' => $text], $this->language);
    }

    public function paraphrase($text, $tone = 'normal')
    {
        return Lang::get('prompt.paraphrase_text', ['text' => $text, 'tone' => $tone], $this->language);
    }

    public function directOutput()
    {
        return Lang::get('prompt.direct_output', [], $this->language);
    }

    public function translate($text, $targetLanguage)
    {
        return Lang::get('prompt.translate_text', [
            'text' => $text,
            'target_language' => $targetLanguage
        ], $this->language);
    }

    public function writeFirstPass($outline, array $params)
    {
        $toneInstructions = ($params['tone'] ?? false) ? $this->getToneInstructions($params['tone']) : '';
        $prompt = Lang::get(
            'prompt.blog_first_pass',
            [
                'first_pass' => Lang::get(
                    'prompt.first_pass',
                    [
                        'tone_instructions' => $toneInstructions,
                        'outline' => $outline
                    ],
                    $this->language
                ),
            ],
            $this->language
        );
        if ($params['style'] ?? false) {
            $prompt .= Lang::get('prompt.style_instructions', ['style' => $params['style']], $this->language);
        }
        return $prompt;
    }

    public function writeEmbeddedFirstPass($outline, array $params)
    {
        $toneInstructions = ($params['tone'] ?? false) ? $this->getToneInstructions($params['tone']) : '';
        $prompt = Lang::get(
            'prompt.blog_embedded_first_pass',
            [
                'first_pass' => Lang::get(
                    'prompt.first_pass',
                    [
                        'tone_instructions' => $toneInstructions,
                        'outline' => $outline
                    ],
                    $this->language
                ),
            ],
            $this->language
        );

        if ($params['style'] ?? false) {
            $prompt .= Lang::get('prompt.style_instructions', ['style' => $params['style']], $this->language);
        }
        return $prompt;
    }

    public function writeTitle($context, array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        $prompt = Lang::get('prompt.write_title', ['context' => $context, 'tone' => $tone], $this->language);
        if ($params['keyword'] ?? false) {
            $prompt .= Lang::get('prompt.keyword_instructions', ['keyword' => $params['keyword']], $this->language);
        }

        return $prompt;
    }

    public function writeEmbeddedTitle(array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        $prompt = Lang::get('prompt.write_embedded_title', ['tone' => $tone], $this->language);
        if ($params['keyword'] ?? false) {
            $prompt .= Lang::get('prompt.keyword_instructions', ['keyword' => $params['keyword']], $this->language);
        }

        return $prompt;
    }

    public function writeOutline($context, array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        return Lang::get('prompt.write_outline', [
            'outline_base' => __('prompt.outline_base', [
                'tone' => $tone,
                'maxsubtopics' => $params['maxsubtopics'],
                'style' => $params['style'],
                'keyword' => $params['keyword'],
            ]),
            'context' => $context,
        ], $this->language);
    }

    public function writeEmbeddedOutline(array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        return Lang::get('prompt.write_embbeded_outline', [
            'outline_base' => __('prompt.outline_base', [
                'tone' => $tone,
                'maxsubtopics' => $params['maxsubtopics'],
                'style' => $params['style'],
                'keyword' => $params['keyword'],
            ]),
            'context' => $params['context'],
        ], $this->language);
    }

    public function givenFollowingText($text)
    {
        return Lang::get('prompt.given_following_text', ['text' => $text], $this->language);
    }

    public function andGivenFollowingContext($text)
    {
        return Lang::get('prompt.given_following_context', ['context' => preg_replace('/\s+/', ' ', $text)], $this->language);
    }

    public function expandEmbeddedOn($text, array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        $prompt = Lang::get('prompt.expand_embedded_text', [
            'expand_text' => Lang::get('prompt.expand_text', [
                'tone' => $tone,
                'context' => $text,
                'style' => $params['style'] ?? 'default',
                'keyword' => $params['keyword']
            ], $this->language)
        ], $this->language);

        return $prompt;
    }

    public function expandOn($text, array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        return Lang::get('prompt.expand_text', [
            'tone' => $tone,
            'context' => $text,
            'style' => $params['style'] ?? 'default',
            'keyword' => $params['keyword']
        ], $this->language);
    }

    public function generateThoughts(array $params)
    {
        return Lang::get('prompt.generate_thoughts', [
            'context' => $params['context'],
            'owner' => $params['owner'],
            'tone' => $params['tone'],
            'style' => $params['style'],
            'sentences_count' => $params['sentences_count']
        ]);
    }

    public function generateFinishedNotification(array $params)
    {
        return Lang::get('prompt.generate_finished_notification', [
            'jobName' => $params['jobName'],
            'context' => $params['context'],
            'owner' => $params['owner'],
            'document_link' => $params['document_link']
        ]);
    }

    public function writeEmbeddedSummary(array $params)
    {
        return Lang::get(
            'prompt.write_embedded_summary',
            ['maxWords' => $params['max_words_count']],
            $this->language
        );
    }

    public function writeSummary(array $params)
    {
        return Lang::get(
            'prompt.write_summary',
            ['text' => $params['content'], 'maxWords' => $params['max_words_count']],
            $this->language
        );
    }

    public function writeMetaDescription($text, array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        $prompt = Lang::get('prompt.write_meta_description', [], $this->language);
        if ($params['keyword'] ?? false) {
            $prompt .= Lang::get('prompt.keyword_instructions', ['keyword' => $params['keyword']], $this->language);
        }
        $prompt .= Lang::get('prompt.tone_instructions', ['tone' => $tone], $this->language);
        $prompt .= Lang::get('prompt.meta_description_context_instructions', ['context' => $text], $this->language);

        return $prompt;
    }

    public function writeEmbeddedSocialMediaPost(array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        $prompt = Lang::get('social_media_prompt.write_social_media_post_embedded', [
            'platform' => $params['platform']
        ], $this->language);

        if ($params['target_word_count'] ?? false) {
            $prompt .= Lang::get('social_media_prompt.target_word_count', ['target' => $params['platform'] === 'twitter' ? 35 : $params['target_word_count']], $this->language);
        }

        if ($params['keyword'] ?? false) {
            $prompt .= Lang::get('social_media_prompt.keyword_instructions', ['keyword' => $params['keyword']], $this->language);
        }
        if ($params['style'] ?? false) {
            $prompt .= Lang::get('social_media_prompt.style_instructions', ['style' => $params['style']], $this->language);
        }
        $prompt .= Lang::get('social_media_prompt.tone_instructions', ['tone' => $tone], $this->language);
        if ($params['more_instructions']) {
            $prompt .= Lang::get('social_media_prompt.more_instructions', ['instructions' => $params['more_instructions']], $this->language);
        }

        return $prompt;
    }

    public function writeSocialMediaPost($context, array $params)
    {
        $tone = Tone::fromLanguage($params['tone'] ?? 'casual', $this->language);
        $prompt = Lang::get('social_media_prompt.write_social_media_post', [
            'platform' => $params['platform']
        ], $this->language);

        if ($params['target_word_count'] ?? false) {
            $prompt .= Lang::get('social_media_prompt.target_word_count', ['target' => $params['platform'] === 'twitter' ? 35 : $params['target_word_count']], $this->language);
        }

        if ($params['keyword'] ?? false) {
            $prompt .= Lang::get('social_media_prompt.keyword_instructions', ['keyword' => $params['keyword']], $this->language);
        }
        if ($params['style'] ?? false) {
            $prompt .= Lang::get('social_media_prompt.style_instructions', ['style' => $params['style']], $this->language);
        }
        $prompt .= Lang::get('social_media_prompt.tone_instructions', ['tone' => $tone], $this->language);
        $prompt .= Lang::get('social_media_prompt.context_instructions', ['context' => $context], $this->language);
        if ($params['more_instructions']) {
            $prompt .= Lang::get('social_media_prompt.more_instructions', ['instructions' => $params['more_instructions']], $this->language);
        }
        return $prompt;
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    public function getToneInstructions($value)
    {
        $tone = Tone::tryFrom($value);
        $prompt = 'prompt.' . $tone->value . '_tone';
        return Lang::get($prompt, [], $this->language);
    }
}
