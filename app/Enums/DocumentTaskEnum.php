<?php

namespace App\Enums;

enum DocumentTaskEnum: string
{
    case DOWNLOAD_AUDIO = 'download_audio';
    case PROCESS_AUDIO = 'process_audio';
    case PUBLISH_TRANSCRIPTION = 'publish_transcription';
    case SUMMARIZE_DOC = 'summarize_doc';
    case CREATE_SOCIAL_MEDIA_POST = 'create_social_media_post';
    case CREATE_OUTLINE = 'create_outline';
    case CREATE_TITLE = 'create_title';
    case CREATE_METADESCRIPTION = 'create_meta_description';
    case EXPAND_OUTLINE = 'expand_outline';
    case EXPAND_TEXT = 'expand_text';
    case EXPAND_TEXT_SECTION = 'expand_text_section';
    case REGISTER_CONTENT_HISTORY = 'register_content_history';

    public function getJob()
    {
        return match ($this) {
            self::DOWNLOAD_AUDIO => "App\Jobs\DownloadAudio",
            self::PROCESS_AUDIO => "App\Jobs\ProcessAudio",
            self::PUBLISH_TRANSCRIPTION => "App\Jobs\TextTranscription\PublishTranscription",
            self::SUMMARIZE_DOC => "App\Jobs\SummarizeDocument",
            self::CREATE_SOCIAL_MEDIA_POST => "App\Jobs\SocialMedia\CreatePost",
            self::CREATE_OUTLINE => "App\Jobs\Blog\CreateOutline",
            self::CREATE_TITLE => "App\Jobs\CreateTitle",
            self::CREATE_METADESCRIPTION => "App\Jobs\Blog\CreateMetaDescription",
            self::EXPAND_OUTLINE => "App\Jobs\ExpandOutline",
            self::EXPAND_TEXT => "App\Jobs\ExpandText",
            self::EXPAND_TEXT_SECTION => "App\Jobs\ExpandTextSection",
            self::REGISTER_CONTENT_HISTORY => "App\Jobs\RegisterContentHistory",
        };
    }
}
