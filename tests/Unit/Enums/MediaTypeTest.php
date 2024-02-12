<?php

use App\Enums\MediaType;

describe('MediaType test', function () {
    it('can get label of media types', function () {
        expect(MediaType::IMAGE->label())->toEqual(__('common.image'));
        expect(MediaType::AUDIO->label())->toEqual(__('common.audio'));
    });

    it('can get key values of media types', function () {
        $expected = [
            'image' => __('common.image'),
            'audio' => __('common.audio'),
        ];
        expect(MediaType::getKeyValues())->toEqual($expected);
    });
})->group('enums');
