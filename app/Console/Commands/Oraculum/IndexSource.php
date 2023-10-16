<?php

namespace App\Console\Commands\Oraculum;

use Illuminate\Console\Command;

class IndexSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oraculum:index {source_type} {collection} {source}';

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
        $python = app()->environment('production') ? '/usr/bin/python' : '/usr/local/bin/python';

        $script = app()->environment('production') ? '/app/oraculum/index-source.py' : '/var/www/html/oraculum/index-source.py';

        $sourceType = $this->argument('source_type');
        $collection = $this->argument('collection');
        $source = $this->argument('source');

        $output = [];
        exec("$python $script \"$sourceType\" \"$collection\" \"$source\"", $output);

        $this->line(implode(PHP_EOL, $output));
    }
}
