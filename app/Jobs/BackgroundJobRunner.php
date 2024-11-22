<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class BackgroundJobRunner
{
    /**
     * Run the specified class and method with parameters.
     *
     * @param string $class
     * @param string $method
     * @param array $parameters
     * @param int $retries
     * @param int $delay
     * @return void
     */
    public static function run(string $class, string $method, array $parameters = [], int $retries = 3, int $delay = 5)
    {
        $attempt = 0;

        while ($attempt < $retries) {
            try {
                $attempt++;
                
                // Validate class and method
                if (!class_exists($class)) {
                    throw new \Exception("Class $class does not exist.");
                }

                if (!method_exists($class, $method)) {
                    throw new \Exception("Method $method does not exist in class $class.");
                }

                // Instantiate the class and execute the method
                $instance = app($class);
                call_user_func_array([$instance, $method], $parameters);

                // Log success
                Log::info("Background job executed successfully", [
                    'class' => $class,
                    'method' => $method,
                    'parameters' => $parameters,
                ]);

                return; // Exit loop on success
            } catch (\Exception $e) {
                // Log errors
                Log::channel('background_jobs_errors')->error("Background job execution failed on attempt $attempt", [
                    'class' => $class,
                    'method' => $method,
                    'parameters' => $parameters,
                    'error' => $e->getMessage(),
                ]);

                if ($attempt >= $retries) {
                    throw $e; // Exit if retries exhausted
                }
                sleep($delay); // Wait before retrying
            }
        }
    }
}
