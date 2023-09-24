<?php

namespace App\Enums;

enum DocumentTaskEnum: string
{
    case CRAWL_WEBSITE = 'crawl_website';
    case CREATE_SOCIAL_MEDIA_POST = 'create_social_media_post';
    case CREATE_OUTLINE = 'create_outline';
    case CREATE_TITLE = 'create_title';
    case CREATE_METADESCRIPTION = 'create_meta_description';
    case DOWNLOAD_AUDIO = 'download_audio';
    case EXPAND_OUTLINE = 'expand_outline';
    case EXPAND_TEXT = 'expand_text';
    case EXPAND_TEXT_SECTION = 'expand_text_section';
    case GENERATE_IMAGE = 'generate_image';
    case GENERATE_IMAGE_VARIANTS = 'generate_image_variants';
    case PARAPHRASE_DOCUMENT = 'paraphrase_document';
    case PARAPHRASE_TEXT = 'paraphrase_text';
    case PROCESS_AUDIO = 'process_audio';
    case PUBLISH_TRANSCRIPTION = 'publish_transcription';
    case PREPARE_TEXT_TRANSLATION = 'prepare_text_translation';
    case REGISTER_FINISHED_PROCESS = 'register_finished_process';
    case REGISTER_CONTENT_HISTORY = 'register_content_history';
    case REWRITE_TEXT_BLOCK = 'rewrite_text_block';
    case SUMMARIZE_DOC = 'summarize_doc';
    case TRANSLATE_TEXT = 'translate_text';
    case TEXT_TO_SPEECH = 'text_to_speech';

    public function getJob()
    {
        return match ($this) {
            self::CRAWL_WEBSITE => "App\Jobs\CrawlWebsite",
            self::CREATE_SOCIAL_MEDIA_POST => "App\Jobs\SocialMedia\CreatePost",
            self::CREATE_OUTLINE => "App\Jobs\Blog\CreateOutline",
            self::CREATE_TITLE => "App\Jobs\CreateTitle",
            self::CREATE_METADESCRIPTION => "App\Jobs\Blog\CreateMetaDescription",
            self::DOWNLOAD_AUDIO => "App\Jobs\DownloadAudio",
            self::EXPAND_OUTLINE => "App\Jobs\ExpandOutline",
            self::EXPAND_TEXT => "App\Jobs\ExpandText",
            self::EXPAND_TEXT_SECTION => "App\Jobs\ExpandTextSection",
            self::GENERATE_IMAGE => "App\Jobs\GenerateImage",
            self::GENERATE_IMAGE_VARIANTS => "App\Jobs\GenerateImageVariants",
            self::PARAPHRASE_DOCUMENT => "App\Jobs\Paraphraser\ParaphraseDocument",
            self::PARAPHRASE_TEXT => "App\Jobs\Paraphraser\ParaphraseText",
            self::PROCESS_AUDIO => "App\Jobs\ProcessAudio",
            self::PREPARE_TEXT_TRANSLATION => "App\Jobs\Translation\PrepareTextTranslation",
            self::PUBLISH_TRANSCRIPTION => "App\Jobs\TextTranscription\PublishTranscription",
            self::REGISTER_FINISHED_PROCESS => "App\Jobs\RegisterFinishedProcess",
            self::REGISTER_CONTENT_HISTORY => "App\Jobs\RegisterContentHistory",
            self::REWRITE_TEXT_BLOCK => "App\Jobs\RewriteTextBlock",
            self::SUMMARIZE_DOC => "App\Jobs\SummarizeDocument",
            self::TEXT_TO_SPEECH => "App\Jobs\TextToSpeech\ConvertTextToAudio",
            self::TRANSLATE_TEXT => "App\Jobs\Translation\TranslateText",
        };
    }
}
