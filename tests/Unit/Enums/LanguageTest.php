<?php

use App\Enums\Language;

describe('Language test', function () {
    it('can get label of languages', function () {
        expect(Language::ENGLISH->label())->toEqual(__('languages.english'));
        expect(Language::PORTUGUESE->label())->toEqual(__('languages.portuguese'));
        expect(Language::SPANISH->label())->toEqual(__('languages.spanish'));
    });

    it('can get system enabled languages', function () {
        $expected = [
            Language::ENGLISH,
            Language::PORTUGUESE,
            Language::SPANISH,
        ];
        expect(Language::systemEnabled())->toEqual($expected);
    });

    it('can get labels of languages', function () {
        $labels = Language::getLabels();
        expect($labels)->toBeArray();
        expect($labels)->toHaveCount(14);
    });

    it('can get key values of languages', function () {
        $keyValues = Language::getKeyValues();
        expect($keyValues)->toBeArray();
        expect($keyValues['en'])->toEqual(__('languages.english'));
    });

    it('checks if a language is voice enabled', function () {
        expect(Language::voiceEnabled())->toContain(Language::ENGLISH, Language::PORTUGUESE, Language::SPANISH);
    });
})->group('enums');
