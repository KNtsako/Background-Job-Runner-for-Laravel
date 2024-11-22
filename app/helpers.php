<?php

use App\Jobs\BackgroundJobRunner;

if (!function_exists('runBackgroundJob')) {
    /**
     * Execute a class and method as a background job.
     *
     * @param string $class
     * @param string $method
     * @param array $parameters
     */
    function runBackgroundJob(string $class, string $method, array $parameters = [])
    {
        $command = PHP_BINARY . " artisan background:run \"$class\" \"$method\" '" . json_encode($parameters) . "' > /dev/null 2>&1 &";

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows-specific background process execution
            pclose(popen("start /B $command", "r"));
        } else {
            // Unix-based systems
            shell_exec($command);
        }
    }
}
