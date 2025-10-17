<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DeleteUnverifiedUsers extends Command
{
    protected $signature = 'users:delete-unverified';
    protected $description = 'Delete users who did not verify email within 10 minutes.';

    public function handle()
    {
        $deleted = User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subMinutes(10))
            ->delete();

        $this->info("Deleted {$deleted} unverified users.");
    }
}
