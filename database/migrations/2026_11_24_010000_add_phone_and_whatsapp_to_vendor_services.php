<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_services', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 15)->nullable();
        });

        DB::table('vendor_services')->whereNotNull('contact_number')->orderBy('id')
            ->chunkById(500, function ($services) {
                foreach ($services as $service) {
                    if (preg_match('/^\+[1-9]\d{7,14}$/', $service->contact_number)) {
                        DB::table('vendor_services')->where('id', $service->id)
                            ->update(['phone' => $service->contact_number]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('vendor_services', function (Blueprint $table) {
            $table->dropColumn(['phone', 'whatsapp']);
        });
    }
};
