<?php

use App\Enums\AIModel;
use App\Helpers\SupportHelper;
use App\Models\Account;
use App\Models\ShortLink;
use Talendor\StabilityAI\Enums\StabilityAIEngine;

describe('SupportHelper helper', function () {
    it('calculates model costs for GPT-3 Turbo', function () {
        $cost1 = SupportHelper::calculateModelCosts(
            AIModel::GPT_3_TURBO->value,
            ['prompt' => 1000, 'completion' => 2000]
        );
        expect($cost1)->toBe('0.0035000');

        $cost2 = SupportHelper::calculateModelCosts(
            AIModel::GPT_3_TURBO->value,
            ['prompt' => 2345, 'completion' => 3456]
        );
        expect($cost2)->toBe('0.0063565');

        $cost3 = SupportHelper::calculateModelCosts(
            AIModel::GPT_3_TURBO->value,
            ['prompt' => 4567, 'completion' => 5678]
        );
        expect($cost3)->toBe('0.0108005');

        $cost4 = SupportHelper::calculateModelCosts(
            AIModel::GPT_3_TURBO->value,
            ['prompt' => 6789, 'completion' => 7890]
        );
        expect($cost4)->toBe('0.0152295');
    });

    it('calculates model costs for GPT-4', function () {
        $model = AIModel::GPT_4->value;
        $cost1 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 1000, 'completion' => 2000]
        );
        expect($cost1)->toBe('0.1500000');

        $cost2 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 2345, 'completion' => 3456]
        );
        expect($cost2)->toBe('0.2777100');

        $cost3 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 4567, 'completion' => 5678]
        );
        expect($cost3)->toBe('0.4776900');

        $cost4 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 6789, 'completion' => 7890]
        );
        expect($cost4)->toBe('0.6770700');
    });

    it('calculates model costs for GPT-4-32', function () {
        $model = AIModel::GPT_4_32->value;
        $cost1 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 1000, 'completion' => 2000]
        );
        expect($cost1)->toBe('0.3000000');

        $cost2 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 2345, 'completion' => 3456]
        );
        expect($cost2)->toBe('0.5554200');

        $cost3 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 4567, 'completion' => 5678]
        );
        expect($cost3)->toBe('0.9553800');

        $cost4 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 6789, 'completion' => 7890]
        );
        expect($cost4)->toBe('1.3541400');
    });

    it('calculates model costs for GPT-4 Turbo', function ($model) {
        $cost1 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 1000, 'completion' => 2000]
        );
        expect($cost1)->toBe('0.0700000');

        $cost2 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 2345, 'completion' => 3456]
        );
        expect($cost2)->toBe('0.1271300');

        $cost3 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 4567, 'completion' => 5678]
        );
        expect($cost3)->toBe('0.2160100');

        $cost4 = SupportHelper::calculateModelCosts(
            $model,
            ['prompt' => 6789, 'completion' => 7890]
        );
        expect($cost4)->toBe('0.3045900');
    })->with([AIModel::GPT_4_TURBO->value, AIModel::GPT_4_1106->value, AIModel::GPT_4_VISION->value]);

    it('calculates model costs for Whisper and Assembly', function ($model) {
        $cost1 = SupportHelper::calculateModelCosts($model, ['audio_length' => 3]);
        expect($cost1)->toBe('0.0183000');

        $cost2 = SupportHelper::calculateModelCosts($model, ['audio_length' => 5]);
        expect($cost2)->toBe('0.0305000');

        $cost3 = SupportHelper::calculateModelCosts($model, ['audio_length' => 11]);
        expect($cost3)->toBe('0.0671000');

        $cost4 = SupportHelper::calculateModelCosts($model, ['audio_length' => 139]);
        expect($cost4)->toBe('0.8479000');
    })->with([AIModel::ASSEMBLY_AI->value, AIModel::WHISPER->value]);

    it('calculates model costs for Stability AI', function () {
        $model = StabilityAIEngine::SD_XL_V_1->value;
        $cost = SupportHelper::calculateModelCosts($model, []);
        expect($cost)->toBe('0.0800000');
    });

    it('calculates model costs for Dall-E 3', function () {
        $model = AIModel::DALL_E_3->value;
        $cost = SupportHelper::calculateModelCosts($model, []);
        expect($cost)->toBe('0.0400000');
    });

    it('calculates model costs for ElevenLabs', function () {
        $model = AIModel::ELEVEN_LABS->value;
        $cost1 = SupportHelper::calculateModelCosts($model, ['char_count' => 12345]);
        expect($cost1)->toBe('1.3579500');

        $cost2 = SupportHelper::calculateModelCosts($model, ['char_count' => 23456]);
        expect($cost2)->toBe('2.5801600');

        $cost3 = SupportHelper::calculateModelCosts($model, ['char_count' => 34567]);
        expect($cost3)->toBe('3.8023700');

        $cost4 = SupportHelper::calculateModelCosts($model, ['char_count' => 456789]);
        expect($cost4)->toBe('50.2467900');
    });

    it('formats cents to dollars', function () {
        $formatted = SupportHelper::formatCentsToDollars(12345);
        expect($formatted)->toBe('123.45');
    });

    it('subtracts percentage from number', function () {
        $result = SupportHelper::subPercent(100, 10);
        expect($result)->toBe(90);
    });

    it('shortens link creates a valid short link', function () {
        $account = Account::factory()->create();
        $shortLink = SupportHelper::shortenLink('http://example.com', ['account_id' => $account->id]);
        expect($shortLink)->toContain("http://localhost/link/");
    });

    it('gets timezones returns an array of timezones', function () {
        $timezones = SupportHelper::getTimezones();
        expect($timezones)->toBeArray();
        expect($timezones)->toHaveCount(count(\DateTimeZone::listIdentifiers()));
    });
})->group('helpers');
