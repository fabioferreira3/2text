<?php

use App\Models\Account;
use App\Models\UnitTransaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('has uuids trait', function () {
    expect(in_array('Illuminate\Database\Eloquent\Concerns\HasUuids', class_uses(UnitTransaction::class)))->toBeTrue();
});

it('has factory trait', function () {
    expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(UnitTransaction::class)))->toBeTrue();
});

it('has guarded attributes', function () {
    $unitTransaction = new UnitTransaction();
    expect($unitTransaction->getGuarded())->toEqual(['id']);
});

it('casts meta attribute to array', function () {
    $unitTransaction = new UnitTransaction();
    expect($unitTransaction->getCasts())->toHaveKey('meta', 'array');
});

it('belongs to an account', function () {
    $unitTransaction = new UnitTransaction();
    expect($unitTransaction->account())->toBeInstanceOf(BelongsTo::class);
    expect($unitTransaction->account()->getRelated())->toBeInstanceOf(Account::class);
});
