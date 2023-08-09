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
    protected $signature = 'crawl {url} {--html}';

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
        $html = $this->option('html');
        $htmlFlag = $html ? '--html' : '';

        $output = [];
        $timeoutInSeconds = 10;
        exec("timeout $timeoutInSeconds $python $script \"$url\" $htmlFlag", $output, $return_var);

        if ($return_var == 124) {
            return 5;
        }

        $this->line(implode(PHP_EOL, $output));
    }
}
