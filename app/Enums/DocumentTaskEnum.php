<?php

namespace App\Enums;

enum DocumentTaskEnum: string
{
    case DOWNLOAD_AUDIO = 'download_audio';
    case PROCESS_AUDIO = 'process_audio';

    public function getJob()
    {
        return match ($this) {
            self::DOWNLOAD_AUDIO => "App\Jobs\DownloadAudio",
            self::PROCESS_AUDIO => "App\Jobs\ProcessAudio",
        };
    }
}
