<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\ProductUsage;
use App\Models\User;

describe('ProductUsage Model', function () {
    it('belongs to an account', function () {
        $account = Account::factory()->create();
        $productUsage = ProductUsage::factory()->create(['account_id' => $account->id]);

        expect($productUsage->account)->toBeInstanceOf(Account::class);
        expect($productUsage->account->id)->toEqual($account->id);
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $productUsage = ProductUsage::factory()->create(['user_id' => $user->id]);

        expect($productUsage->user)->toBeInstanceOf(User::class);
        expect($productUsage->user->id)->toEqual($user->id);
    });
});
