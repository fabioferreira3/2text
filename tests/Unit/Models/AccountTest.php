<?php

namespace Tests\Unit\Models;

use App\Jobs\Account\RegisterUnitTransaction;
use App\Models\Account;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake([RegisterUnitTransaction::class]);
    $this->account = Account::factory()->create();
});

describe('Account model', function () {
    it('it triggers register unit job when adding units', function () {
        $this->account->addUnits(100);

        Bus::assertDispatched(RegisterUnitTransaction::class, function ($job) {
            return $job->amount == 100;
        });
    });

    it('it triggers register unit job when subtracting units', function () {
        $this->account->subUnits(-150);

        Bus::assertDispatched(RegisterUnitTransaction::class, function ($job) {
            return $job->amount == -150;
        });
    });

    it('it throws an error when adding wrong unit values', function () {
        $this->expectException(\Exception::class);
        $this->account->addUnits(-200);
    });

    it('it throws an error when subtracting wrong unit values', function () {
        $this->expectException(\Exception::class);
        $this->account->subUnits(200);
    });
});
