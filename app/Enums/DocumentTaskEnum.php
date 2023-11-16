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
    case EMBED_SOURCE = 'embed_source';
    case EXPAND_OUTLINE = 'expand_outline';
    case EXPAND_TEXT = 'expand_text';
    case EXPAND_TEXT_SECTION = 'expand_text_section';
    case EXTRACT_AND_EMBED_AUDIO = 'extract_embed_audio';
    case GENERATE_IMAGE = 'generate_image';
    case GENERATE_IMAGE_VARIANTS = 'generate_image_variants';
    case GENERATE_AI_THOUGHTS = 'generate_ai_thoughts';
    case GENERATE_FINISHED_NOTIFICATION = 'generate_finished_notification';
    case POST_PROCESS_AUDIO = "post_process_audio";
    case PARAPHRASE_DOCUMENT = 'paraphrase_document';
    case PARAPHRASE_TEXT = 'paraphrase_text';
    case PROCESS_AUDIO = 'process_audio';
    case PROCESS_SOCIAL_MEDIA_POSTS_CREATION = 'process_social_media_posts_creation';
    case PUBLISH_TRANSCRIPTION = 'publish_transcription';
    case PUBLISH_TEXT_BLOCKS = 'publish_text_blocks';
    case PREPARE_TEXT_TRANSLATION = 'prepare_text_translation';
    case REGISTER_FINISHED_PROCESS = 'register_finished_process';
    case REGISTER_CONTENT_HISTORY = 'register_content_history';
    case REMOVE_EMBEDDINGS = 'remove_embeddings';
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
            self::EXTRACT_AND_EMBED_AUDIO => "App\Jobs\ExtractAndEmbedAudio",
            self::EMBED_SOURCE => "App\Jobs\EmbedSource",
            self::GENERATE_IMAGE => "App\Jobs\GenerateImage",
            self::GENERATE_IMAGE_VARIANTS => "App\Jobs\GenerateImageVariants",
            self::GENERATE_AI_THOUGHTS => "App\Jobs\GenerateAIThoughts",
            self::GENERATE_FINISHED_NOTIFICATION => "App\Jobs\GenerateFinishedNotification",
            self::PARAPHRASE_DOCUMENT => "App\Jobs\Paraphraser\ParaphraseDocument",
            self::PARAPHRASE_TEXT => "App\Jobs\Paraphraser\ParaphraseText",
            self::POST_PROCESS_AUDIO => "App\Jobs\AudioTranscription\PostProcessAudio",
            self::PROCESS_AUDIO => "App\Jobs\ProcessAudio",
            self::PROCESS_SOCIAL_MEDIA_POSTS_CREATION => "App\Jobs\SocialMedia\ProcessSocialMediaPostsCreation",
            self::PREPARE_TEXT_TRANSLATION => "App\Jobs\Translation\PrepareTextTranslation",
            self::PUBLISH_TRANSCRIPTION => "App\Jobs\AudioTranscription\PublishTranscription",
            self::PUBLISH_TEXT_BLOCKS => "App\Jobs\Blog\PublishTextBlocks",
            self::REGISTER_FINISHED_PROCESS => "App\Jobs\RegisterFinishedProcess",
            self::REGISTER_CONTENT_HISTORY => "App\Jobs\RegisterContentHistory",
            self::REMOVE_EMBEDDINGS => "App\Jobs\RemoveEmbeddings",
            self::REWRITE_TEXT_BLOCK => "App\Jobs\RewriteTextBlock",
            self::SUMMARIZE_DOC => "App\Jobs\SummarizeDocument",
            self::TEXT_TO_SPEECH => "App\Jobs\TextToSpeech\GenerateAudio",
            self::TRANSLATE_TEXT => "App\Jobs\Translation\TranslateText",
        };
    }
}
