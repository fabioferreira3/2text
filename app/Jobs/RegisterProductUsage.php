<?php

namespace App\Jobs;

use App\Helpers\SupportHelper;
use App\Jobs\Traits\JobEndings;
use App\Models\Account;
use App\Models\ProductUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

class RegisterProductUsage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    protected Account $account;
    protected array $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Account $account, array $params = [])
    {
        $this->account = $account;
        $this->params = $params;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $cost = 0;
            if ($this->params['model'] ?? false) {
                $cost = SupportHelper::calculateModelCosts($this->params['model'], [
                    'prompt' => $this->params['prompt'] ?? 0,
                    'completion' => $this->params['completion'] ?? 0,
                    'audio_length' => $this->params['length'] ?? 0,
                    'total' => $this->params['total'] ?? 0,
                ]);
            }

            $this->account->productUsage()->save(
                new ProductUsage([
                    'model' => $this->params['model'] ?? null,
                    'prompt_token_usage' => $this->params['prompt'] ?? 0,
                    'completion_token_usage' => $this->params['completion'] ?? 0,
                    'total_token_usage' => $this->params['total'] ?? 0,
                    'cost' => $cost,
                    'meta' => $this->params['meta'] ?? []
                ])
            );
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to register finished process: ' . $e->getMessage());
        }
    }
}
