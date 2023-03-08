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
        $python = '/usr/local/bin/python';

        $script = '/var/www/html/file.py';

        $string = $this->argument('string');

        $output = [];
        exec("$python $script \"$string\"", $output);


        return implode(PHP_EOL, $output);
    }
}
