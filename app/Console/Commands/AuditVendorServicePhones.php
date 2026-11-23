<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditVendorServicePhones extends Command
{
    protected $signature = 'vendor-services:audit-phones';
    protected $description = 'Report invalid or suspicious vendor service phone numbers without changing data';

    public function handle(): int
    {
        $count = 0;
        $this->line('ID | Service | Field | Value | Reason');

        DB::table('vendor_services')
            ->select(['id', 'name', 'phone', 'contact_number', 'whatsapp'])
            ->orderBy('id')
            ->chunkById(500, function ($services) use (&$count) {
                foreach ($services as $service) {
                    foreach ([
                        'phone' => [$service->phone, '/^\+[1-9]\d{7,14}$/'],
                        'contact_number' => [$service->contact_number, '/^\+[1-9]\d{7,14}$/'],
                        'whatsapp' => [$service->whatsapp, '/^[1-9]\d{7,14}$/'],
                    ] as $field => [$value, $format]) {
                        if ($field === 'contact_number' && $value === $service->phone) {
                            continue;
                        }
                        if ($value === null || $value === '') {
                            if ($field === 'phone') {
                                $this->report($service->id, $service->name, $field, '', 'missing', $count);
                            }
                            continue;
                        }

                        if (!preg_match($format, $value)) {
                            $this->report($service->id, $service->name, $field, $value, 'invalid format', $count);
                        } elseif (preg_match('/(\d)\1{4,}/', $value) || preg_match('/(?:012345|123456|234567|345678|456789|987654|876543|765432|654321)/', $value)) {
                            $this->report($service->id, $service->name, $field, $value, 'suspicious pattern; verify manually', $count);
                        }
                    }
                }
            });

        $this->info("Flagged entries: {$count}. No records were changed.");

        return self::SUCCESS;
    }

    private function report(int $id, string $name, string $field, string $value, string $reason, int &$count): void
    {
        $this->line(implode(' | ', [$id, $name, $field, $value, $reason]));
        $count++;
    }
}
