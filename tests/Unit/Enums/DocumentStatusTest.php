<?php

use App\Enums\DocumentStatus;

describe('DocumentStatus test', function () {
    it('has correct values for DocumentStatus enum', function () {
        expect(DocumentStatus::ABORTED->value)->toBe('aborted')
            ->and(DocumentStatus::DRAFT->value)->toBe('draft')
            ->and(DocumentStatus::FAILED->value)->toBe('failed')
            ->and(DocumentStatus::FINISHED->value)->toBe('finished')
            ->and(DocumentStatus::IN_PROGRESS->value)->toBe('in_progress')
            ->and(DocumentStatus::ON_HOLD->value)->toBe('on_hold');
    });

    it('labels are correctly translated for DocumentStatus enum', function () {
        expect(DocumentStatus::ABORTED->label())->toBe(__('common.document.aborted'))
            ->and(DocumentStatus::DRAFT->label())->toBe(__('common.document.draft'))
            ->and(DocumentStatus::FAILED->label())->toBe(__('common.document.failed'))
            ->and(DocumentStatus::FINISHED->label())->toBe(__('common.document.finished'))
            ->and(DocumentStatus::IN_PROGRESS->label())->toBe(__('common.document.in_progress'))
            ->and(DocumentStatus::ON_HOLD->label())->toBe(__('common.document.on_hold'));
    });

    it('getKeyValues method returns all enum key-value pairs with correct translations', function () {
        $expectedKeyValues = [
            'aborted' => __('common.document.aborted'),
            'draft' => __('common.document.draft'),
            'failed' => __('common.document.failed'),
            'finished' => __('common.document.finished'),
            'in_progress' => __('common.document.in_progress'),
            'on_hold' => __('common.document.on_hold'),
            'ready' => __('common.document.ready')
        ];
        expect(DocumentStatus::getKeyValues())->toBe($expectedKeyValues);
    });
})->group('enums');
