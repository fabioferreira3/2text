<?php

namespace App\Jobs\Account;

use App\Models\Account;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RegisterUnitTransaction
{
    use Dispatchable, SerializesModels;

    public Account $account;
    public $amount;
    public array $meta;

    public function __construct(Account $account, $amount, array $meta = [])
    {
        $this->account = $account;
        $this->amount = $amount;
        $this->meta = $meta;
    }

    public function handle()
    {
        DB::beginTransaction();
        try {
            $this->account->unitTransactions()->create([
                'amount' => $this->amount,
                'meta' => $this->meta
            ]);

            $this->account->update([
                'units' => $this->account->units + $this->amount
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
