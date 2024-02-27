<?php

use App\Models\Account;
use App\Models\ShortLink;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->be($this->authUser);
});

it('has factory and uuids traits', function () {
    expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(ShortLink::class)))->toBeTrue();
    expect(in_array('Illuminate\Database\Eloquent\Concerns\HasUuids', class_uses(ShortLink::class)))->toBeTrue();
});

it('has guarded attributes', function () {
    $shortLink = new ShortLink();
    expect($shortLink->getGuarded())->toEqual(['id']);
});

it('casts expires_at attribute to datetime', function () {
    $shortLink = new ShortLink();
    expect($shortLink->getCasts())->toHaveKey('expires_at', 'datetime');
});

it('belongs to an account', function () {
    $shortLink = new ShortLink();
    expect($shortLink->account())->toBeInstanceOf(BelongsTo::class);
    expect($shortLink->account()->getRelated())->toBeInstanceOf(Account::class);
});

it('has a valid scope', function () {
    $validShortLink = ShortLink::factory()->create(['expires_at' => Carbon::tomorrow()]);
    $expiredShortLink = ShortLink::factory()->create(['expires_at' => Carbon::yesterday()]);

    $validShortLinks = ShortLink::valid()->get();

    expect($validShortLink->id)->toBeIn($validShortLinks->pluck('id')->toArray());
    expect($validShortLinks)->not->toContain($expiredShortLink);
});

it('sets link and account_id on saving', function () {
    $shortLink = new ShortLink([
        'target_url' => 'https://example.com',
    ]);
    $shortLink->save();

    expect((string) $shortLink->link)->toBeUuid();
    expect($shortLink->account_id)->toEqual($this->authUser->account_id);
});
