<?php

namespace App\Jobs;

use App\Jobs\Traits\JobEndings;
use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Exception;

class RegisterUnitsConsumption implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, JobEndings;

    public $account;
    public $type;
    public array $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Account $account, $type, array $meta = [])
    {
        $this->account = $account;
        $this->type = $type;
        $this->meta = $meta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $handler = 'handle' . Str::studly($this->type);
            $unitsUsage = $this->$handler();
            $this->account->subUnits($unitsUsage, $this->meta);
            $this->jobSucceded();
        } catch (Exception $e) {
            $this->jobFailed('Failed to register units consumption: ' . $e->getMessage());
        }
    }

    protected function handleWordsGeneration()
    {
        return number_format($this->meta['word_count'] / 480, 2) * (-1);
    }

    protected function handleImageGeneration()
    {
        return $this->meta['img_count'] * (-1);
    }
}
