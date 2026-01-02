<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // For MySQL/MariaDB, we need to alter the enum column
            DB::statement("ALTER TABLE bags MODIFY COLUMN trip_type ENUM('عمل', 'سياحة', 'عائلية', 'علاج', 'الجيم', 'أخرى') DEFAULT 'سياحة'");
        } else {
            // For other databases (like SQLite, PostgreSQL), change to string
            Schema::table('bags', function (Blueprint $table) {
                $table->string('trip_type')->default('سياحة')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Revert to original enum values
            DB::statement("ALTER TABLE bags MODIFY COLUMN trip_type ENUM('عمل', 'سياحة', 'عائلية', 'علاج') DEFAULT 'سياحة'");
        } else {
            // For other databases, change back to enum-like constraint would need application-level validation
            Schema::table('bags', function (Blueprint $table) {
                $table->string('trip_type')->default('سياحة')->change();
            });
        }
    }
};

