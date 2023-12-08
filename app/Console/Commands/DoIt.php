<?php

namespace App\Console\Commands;

use App\Models\DocumentContentBlock;
use Illuminate\Console\Command;

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
        $contentBlocks = DocumentContentBlock::all();

        $contentBlocks->each(function ($contentBlock) {
            if (!$contentBlock->versions->count()) {
                $contentBlock->versions()->create([
                    'content' => $contentBlock->content,
                    'version' => 1,
                    'active' => true
                ]);
            }
        });
    }
}
