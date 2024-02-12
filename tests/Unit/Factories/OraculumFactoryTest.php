<?php

use App\Factories\OraculumFactory;
use App\Models\User;
use App\Packages\Oraculum\Oraculum;

describe('OraculumFactory factory', function () {
    it('creates an instance of Oraculum', function () {
        $factory = new OraculumFactory();
        $user = User::factory()->create();
        $oraculum = $factory->make($user, 'collection_name');

        expect($oraculum)->toBeInstanceOf(Oraculum::class);
    });
})->group('factories');
