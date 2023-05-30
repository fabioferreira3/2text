<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl {url}';

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
        $script = app()->environment('production') ? '/app/crawler.py' : '/var/www/html/crawler.py';
        $url = $this->argument('url');

        $output = [];
        exec("$python $script \"$url\"", $output);

        $this->line(implode(PHP_EOL, $output));
    }
}
