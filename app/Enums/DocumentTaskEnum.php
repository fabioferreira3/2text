<?php

namespace App\Enums;

enum DocumentTaskEnum: string
{
    case BROADCAST_CUSTOM_EVENT = 'broadcast_custom_event';
    case CRAWL_WEBSITE = 'crawl_website';
    case CREATE_SOCIAL_MEDIA_POST = 'create_social_media_post';
    case CREATE_OUTLINE = 'create_outline';
    case CREATE_TITLE = 'create_title';
    case CREATE_METADESCRIPTION = 'create_meta_description';
    case DOWNLOAD_AUDIO = 'download_audio';
    case DOWNLOAD_SUBTITLES = 'download_subtitles';
    case EMBED_SOURCE = 'embed_source';
    case EXPAND_OUTLINE = 'expand_outline';
    case EXPAND_TEXT = 'expand_text';
    case EXPAND_TEXT_SECTION = 'expand_text_section';
    case EXTRACT_AND_EMBED_AUDIO = 'extract_embed_audio';
    case GENERATE_IMAGE = 'generate_image';
    case GENERATE_IMAGE_VARIANTS = 'generate_image_variants';
    case GENERATE_AI_THOUGHTS = 'generate_ai_thoughts';
    case GENERATE_FINISHED_BLOG_POST_NOTIFICATION = 'generate_finished_blog_post_notification';
    case POST_PROCESS_AUDIO = "post_process_audio";
    case PARAPHRASE_DOCUMENT = 'paraphrase_document';
    case PARAPHRASE_TEXT = 'paraphrase_text';
    case PROCESS_SOCIAL_MEDIA_POSTS_CREATION = 'process_social_media_posts_creation';
    case PUBLISH_TEXT_BLOCKS = 'publish_text_blocks';
    case PUBLISH_TEXT_BLOCK = 'publish_text_block';
    case TRANSLATE_TEXT_BLOCK = 'translate_text_block';
    case REGISTER_FINISHED_PROCESS = 'register_finished_process';
    case REMOVE_EMBEDDINGS = 'remove_embeddings';
    case REWRITE_TEXT_BLOCK = 'rewrite_text_block';
    case SUMMARIZE_CONTENT = 'summarize_content';
    case TRANSLATE_TEXT = 'translate_text';
    case TRANSCRIBE_AUDIO = 'transcribe_audio';
    case TRANSCRIBE_AUDIO_WITH_DIARIZATION = 'transcribe_audio_with_diarization';
    case TEXT_TO_AUDIO = 'text_to_audio';

    public function getJob()
    {
        return match ($this) {
            self::BROADCAST_CUSTOM_EVENT => "App\Jobs\BroadcastCustomEvent",
            self::CRAWL_WEBSITE => "App\Jobs\CrawlWebsite",
            self::CREATE_SOCIAL_MEDIA_POST => "App\Jobs\SocialMedia\CreatePost",
            self::CREATE_OUTLINE => "App\Jobs\Blog\CreateOutline",
            self::CREATE_TITLE => "App\Jobs\CreateTitle",
            self::CREATE_METADESCRIPTION => "App\Jobs\Blog\CreateMetaDescription",
            self::DOWNLOAD_AUDIO => "App\Jobs\DownloadAudio",
            self::DOWNLOAD_SUBTITLES => "App\Jobs\DownloadSubtitles",
            self::EXPAND_OUTLINE => "App\Jobs\ExpandOutline",
            self::EXPAND_TEXT => "App\Jobs\ExpandText",
            self::EXPAND_TEXT_SECTION => "App\Jobs\ExpandTextSection",
            self::EXTRACT_AND_EMBED_AUDIO => "App\Jobs\ExtractAndEmbedAudio",
            self::EMBED_SOURCE => "App\Jobs\EmbedSource",
            self::GENERATE_IMAGE => "App\Jobs\GenerateImage",
            self::GENERATE_IMAGE_VARIANTS => "App\Jobs\GenerateImageVariants",
            self::GENERATE_AI_THOUGHTS => "App\Jobs\GenerateAIThoughts",
            self::GENERATE_FINISHED_BLOG_POST_NOTIFICATION => "App\Jobs\Blog\GenerateFinishedNotification",
            self::PARAPHRASE_DOCUMENT => "App\Jobs\Paraphraser\ParaphraseDocument",
            self::PARAPHRASE_TEXT => "App\Jobs\Paraphraser\ParaphraseText",
            self::POST_PROCESS_AUDIO => "App\Jobs\AudioTranscription\PostProcessAudio",
            self::PROCESS_SOCIAL_MEDIA_POSTS_CREATION => "App\Jobs\SocialMedia\ProcessSocialMediaPostsCreation",
            self::TRANSLATE_TEXT_BLOCK => "App\Jobs\Translation\TranslateTextBlock",
            self::PUBLISH_TEXT_BLOCKS => "App\Jobs\Blog\PublishTextBlocks",
            self::PUBLISH_TEXT_BLOCK => "App\Jobs\PublishTextBlock",
            self::REGISTER_FINISHED_PROCESS => "App\Jobs\RegisterFinishedProcess",
            self::REMOVE_EMBEDDINGS => "App\Jobs\RemoveEmbeddings",
            self::REWRITE_TEXT_BLOCK => "App\Jobs\RewriteTextBlock",
            self::SUMMARIZE_CONTENT => "App\Jobs\SummarizeContent",
            self::TEXT_TO_AUDIO => "App\Jobs\TextToAudio\GenerateAudio",
            self::TRANSLATE_TEXT => "App\Jobs\Translation\TranslateText",
            self::TRANSCRIBE_AUDIO => "App\Jobs\TranscribeAudio",
            self::TRANSCRIBE_AUDIO_WITH_DIARIZATION => "App\Jobs\TranscribeAudioWithDiarization",
        };
    }
}
