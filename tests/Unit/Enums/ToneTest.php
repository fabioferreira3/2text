<?php

use App\Enums\Tone;

describe('Tone test', function () {
    it('can get label of tones', function () {
        expect(Tone::DEFAULT->label())->toEqual(__('tones.default'));
        expect(Tone::ACADEMIC->label())->toEqual(__('tones.academic'));
        expect(Tone::ADVENTUROUS->label())->toEqual(__('tones.adventurous'));
    });

    it('can get values of tones', function () {
        $values = Tone::getValues();
        expect($values)->toBeArray();
        expect($values)->toContain('default', 'academic', 'adventurous');
    });

    it('can get a label from tone and language', function () {
        expect(Tone::fromLanguage('academic', 'en'))->toEqual(__('tones.academic'));
        expect(Tone::fromLanguage('nonexistent', 'en'))->toEqual(__('tones.default'));
    });
})->group('enums');
