<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Tokenize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count:token {string}';

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

        $script = app()->environment('production') ? '/app/token-counter.py' : '/var/www/html/token-counter.py';

        $string = $this->argument('string');

        $output = [];
        exec("$python $script \"$string\"", $output);

        $this->line(implode(PHP_EOL, $output));
    }
}
