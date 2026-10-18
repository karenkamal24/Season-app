<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiService;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class WarmEventsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:warm-cache {--country= : Specific country code (e.g., EGY, SAU, UAE)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-fetch and warm upcoming events cache using Gemini AI for top countries';

    /**
     * Execute the console command.
     */
    public function handle(GeminiService $geminiService)
    {
        $countryOption = $this->option('country');

        $countryMap = [
            'EGY' => 'Egypt',
            'SAU' => 'Saudi Arabia',
            'UAE' => 'United Arab Emirates',
            'KWT' => 'Kuwait',
            'QAT' => 'Qatar',
            'BHR' => 'Bahrain',
            'OMN' => 'Oman',
            'JOR' => 'Jordan',
        ];

        if ($countryOption) {
            $code = strtoupper(trim($countryOption));
            $countries = isset($countryMap[$code]) ? [$code => $countryMap[$code]] : [$code => $code];
        } else {
            $countries = $countryMap;
        }

        $languages = ['ar', 'en'];

        $this->info('Starting events cache warming...');

        foreach ($countries as $code => $countryName) {
            foreach ($languages as $lang) {
                $this->line("Fetching events for <comment>{$countryName}</comment> [{$lang}]...");
                
                try {
                    $result = $geminiService->searchUpcomingEvents($countryName, $lang, true);
                    $count = isset($result['events']) ? count($result['events']) : 0;
                    $this->info("✓ Cached {$count} events for {$countryName} ({$lang})");
                } catch (\Exception $e) {
                    $this->error("✗ Failed for {$countryName} ({$lang}): " . $e->getMessage());
                    Log::error("Failed warming events cache for {$countryName} ({$lang})", [
                        'error' => $e->getMessage(),
                    ]);
                }

                // Small pause to avoid hitting rate limits
                usleep(500000); // 0.5s
            }
        }

        $this->info('Events cache warming completed!');
        return Command::SUCCESS;
    }
}
