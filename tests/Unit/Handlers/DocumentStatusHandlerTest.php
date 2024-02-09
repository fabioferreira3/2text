<?php

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Handlers\DocumentStatusHandler;

describe('DocumentStatusHandler handler', function () {
    it('allows viewing based on document type and status for BlogPost', function ($falsyStatus) {
        expect(DocumentStatusHandler::canView(DocumentType::BLOG_POST, DocumentStatus::FINISHED))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::BLOG_POST, DocumentStatus::DRAFT))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::BLOG_POST, DocumentStatus::IN_PROGRESS))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::BLOG_POST, $falsyStatus))->toBeFalse();
    })->with([
        DocumentStatus::ABORTED,
        DocumentStatus::FAILED,
        DocumentStatus::ON_HOLD
    ]);

    it('allows viewing based on document type and status for SocialMediaGroup', function ($falsyStatus) {
        expect(DocumentStatusHandler::canView(DocumentType::SOCIAL_MEDIA_GROUP, DocumentStatus::FINISHED))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::SOCIAL_MEDIA_GROUP, DocumentStatus::DRAFT))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::SOCIAL_MEDIA_GROUP, DocumentStatus::IN_PROGRESS))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::SOCIAL_MEDIA_GROUP, $falsyStatus))->toBeFalse();
    })->with([
        DocumentStatus::ABORTED,
        DocumentStatus::FAILED,
        DocumentStatus::ON_HOLD
    ]);

    it('allows viewing based on document type and status for Summarizer', function ($falsyStatus) {
        expect(DocumentStatusHandler::canView(DocumentType::SUMMARIZER, DocumentStatus::FINISHED))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::SUMMARIZER, DocumentStatus::DRAFT))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::SUMMARIZER, DocumentStatus::IN_PROGRESS))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::SUMMARIZER, $falsyStatus))->toBeFalse();
    })->with([
        DocumentStatus::ABORTED,
        DocumentStatus::FAILED,
        DocumentStatus::ON_HOLD
    ]);

    it('allows viewing based on document type and status for AudioTranscription', function ($falsyStatus) {
        expect(DocumentStatusHandler::canView(DocumentType::AUDIO_TRANSCRIPTION, DocumentStatus::FINISHED))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::AUDIO_TRANSCRIPTION, DocumentStatus::DRAFT))->toBeTrue();
        expect(DocumentStatusHandler::canView(DocumentType::AUDIO_TRANSCRIPTION, DocumentStatus::IN_PROGRESS))->toBeFalse();
        expect(DocumentStatusHandler::canView(DocumentType::AUDIO_TRANSCRIPTION, $falsyStatus))->toBeFalse();
    })->with([
        DocumentStatus::ABORTED,
        DocumentStatus::FAILED,
        DocumentStatus::ON_HOLD
    ]);

    it('uses default viewing permissions for unknown document types', function ($unknownType) {
        expect(DocumentStatusHandler::canView($unknownType, DocumentStatus::FINISHED))->toBeTrue();
        expect(DocumentStatusHandler::canView($unknownType, DocumentStatus::DRAFT))->toBeTrue();
        expect(DocumentStatusHandler::canView($unknownType, DocumentStatus::IN_PROGRESS))->toBeFalse();
        expect(DocumentStatusHandler::canView($unknownType, DocumentStatus::ABORTED))->toBeFalse();
        expect(DocumentStatusHandler::canView($unknownType, DocumentStatus::FAILED))->toBeFalse();
        expect(DocumentStatusHandler::canView($unknownType, DocumentStatus::ON_HOLD))->toBeFalse();
    })->with([
        DocumentType::GENERIC,
        DocumentType::INQUIRY,
        DocumentType::PARAPHRASED_TEXT,
        DocumentType::SOCIAL_MEDIA_POST,
        DocumentType::TEXT_TO_SPEECH,
    ]);
})->group('handlers');
