<?php

namespace App\Helpers;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Support\Str;

class DocumentStatusHelper
{
    public static function canView(DocumentType $type, DocumentStatus $status)
    {
        $method = Str::camel($type->value) . 'CanView';
        if (!method_exists(self::class, $method)) {
            return self::defaultCanView($status);
        }
        return self::$method($status);
    }

    public static function blogPostCanView(DocumentStatus $status)
    {
        return !in_array($status, [DocumentStatus::FINISHED, DocumentStatus::DRAFT, DocumentStatus::IN_PROGRESS]);
    }

    public static function socialMediaGroupCanView(DocumentStatus $status)
    {
        return !in_array($status, [
            DocumentStatus::FINISHED, DocumentStatus::DRAFT, DocumentStatus::IN_PROGRESS
        ]);
    }

    public static function summarizerCanView(DocumentStatus $status)
    {
        return !in_array($status, [
            DocumentStatus::FINISHED, DocumentStatus::DRAFT, DocumentStatus::IN_PROGRESS
        ]);
    }

    public static function audioTranscriptionCanView(DocumentStatus $status)
    {
        return in_array($status, [DocumentStatus::FINISHED, DocumentStatus::DRAFT]);
    }

    public static function defaultCanView(DocumentStatus $status)
    {
        return in_array($status, [DocumentStatus::FINISHED, DocumentStatus::DRAFT]);
    }
}
