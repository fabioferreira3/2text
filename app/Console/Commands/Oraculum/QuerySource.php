<?php

namespace App\Console\Commands\Oraculum;

use Illuminate\Console\Command;

class QuerySource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oraculum:query {collection} {question}';

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

        $script = app()->environment('production') ? '/app/oraculum/query-source.py' : '/var/www/html/oraculum/query-source.py';

        $collection = $this->argument('collection');
        $question = $this->argument('question');

        $output = [];
        exec("$python $script \"$collection\" \"$question\"", $output);

        $this->line(implode(PHP_EOL, $output));
    }
}
