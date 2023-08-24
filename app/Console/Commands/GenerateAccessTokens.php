<?php

namespace App\Console\Commands;

use App\Models\AccessToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateAccessTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'access-tokens:generate {--count=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate access tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = $this->option('count');

        for ($i = 0; $i < $count; $i++) {
            do {
                $name = Str::random(10);
            } while (AccessToken::where('name', $name)->exists());

            AccessToken::create(['name' => $name]);

            $this->info("Access token '{$name}' has been generated.");
        }

        return Command::SUCCESS;
    }
}
