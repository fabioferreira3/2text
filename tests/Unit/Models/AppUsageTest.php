<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\AppUsage;
use App\Models\User;

describe('AppUsage Model', function () {
    it('belongs to an account', function () {
        $account = Account::factory()->create();
        $appUsage = AppUsage::factory()->create(['account_id' => $account->id]);

        expect($appUsage->account)->toBeInstanceOf(Account::class);
        expect($appUsage->account->id)->toEqual($account->id);
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $appUsage = AppUsage::factory()->create(['user_id' => $user->id]);

        expect($appUsage->user)->toBeInstanceOf(User::class);
        expect($appUsage->user->id)->toEqual($user->id);
    });
});
