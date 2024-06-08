<?php

namespace App\Console\Commands;

use App\Domain\Agents\Enums\Agent;
use App\Domain\Agents\Factories\AgentFactory;
use App\Domain\Agents\Jobs\PollRun;
use App\Domain\Agents\Repositories\AgentRepository;
use App\Domain\Agents\TheParaphraser;
use App\Domain\Thread\Enum\MessageRole;
use App\Domain\Thread\Enum\RunStatus;
use App\Domain\Thread\Thread;
use App\Domain\Thread\ThreadRun;
use App\Packages\OpenAI\Assistant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * @codeCoverageIgnore
 */
class DoIt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = 'After battling back from a near-fatal accident in 2023 that left him
        hospitalized with more than 30 broken bones, Jeremy Renner is back for Season 3 of
        this popular crime thriller overseen by Taylor Sheridan, one of six series he
        currently has on air (seven, if mini-series "Lawmen: Bass Reeves" returns). This
        season, Mike McLusky — the main power broker in a family that liaises between local
        police, politicians, and prison system — must contend with the Russian mob, a drug
        war, and a familiar face from his incarcerated past.';

        $repo = new AgentRepository();
        $thread = $repo->createThread($content);

        (new TheParaphraser())->run($thread);
    }
}
