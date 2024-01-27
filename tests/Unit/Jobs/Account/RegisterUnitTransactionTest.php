<?php

use App\Jobs\Account\RegisterUnitTransaction;
use App\Models\Account;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake(RegisterUnitTransaction::class);
    $this->account = Account::factory()->create();
});

describe('RegisterUnitTransaction job', function () {
    it('registers a unit transaction successfully', function ($amount) {

        $job = new RegisterUnitTransaction($this->account, $amount);
        $job->handle();

        $this->assertDatabaseHas('unit_transactions', [
            'account_id' => $this->account->id,
            'amount' => $amount
        ]);
    })->with([100, 200, -150, -50, 6940, -13543]);

    it('rolls back the unit transaction if an exception occurs', function () {
        $this->expectException(\Exception::class);
        $mockAccount = Mockery::mock(Account::class)->makePartial();
        $mockAccount->shouldReceive('update')->andThrow(new \Exception('Test exception'));
        $mockAccount->units = 100;

        $job = new RegisterUnitTransaction($mockAccount, 50, ['meta_key' => 'meta_value']);
        $job->handle();

        expect($mockAccount->units)->toEqual(100);

        $this->assertDatabaseMissing('unit_transactions', [
            'account_id' => $this->account->id,
            'amount' => 50
        ]);
    });
});
