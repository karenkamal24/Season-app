<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestLogging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test logging to test.log file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing logging...');
        
        $testData = [
            'message' => 'This is a test log entry',
            'timestamp' => now()->toDateTimeString(),
            'queue_connection' => config('queue.default'),
            'log_channel' => config('logging.default'),
        ];

        // Log to default channel
        Log::info('🧪 TEST: Logging to default channel', $testData);
        
        // Log to test.log channel
        Log::channel('test')->info('🧪 TEST: Logging to test.log channel', $testData);
        
        $this->info('✅ Log entries written!');
        $this->info('Check: storage/logs/test.log');
        $this->info('Check: storage/logs/laravel.log');
        
        return Command::SUCCESS;
    }
}

