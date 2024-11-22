<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BackgroundJobRunner;

class RunBackgroundJob extends Command
{
    protected $signature = 'background:run {class} {method} {parameters?}';

    protected $description = 'Run a PHP class method as a background job';

    public function handle()
    {
        $class = $this->argument('class');
        $method = $this->argument('method');
        $parameters = json_decode($this->argument('parameters') ?? '[]', true);

        try {
            BackgroundJobRunner::run($class, $method, $parameters);
            $this->info('Job executed successfully.');
        } catch (\Exception $e) {
            $this->error('Job execution failed: ' . $e->getMessage());
        }
    }
}
