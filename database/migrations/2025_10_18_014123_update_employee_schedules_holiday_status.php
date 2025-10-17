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
        // First, add the new enum values to the existing enum
        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM('Working', 'Day Off', 'Leave', 'Holiday', 'Regular Holiday', 'Special Holiday', 'Overtime') DEFAULT 'Working'");

        // Then, update existing 'Holiday' records to 'Regular Holiday'
        DB::table('employee_schedules')
            ->where('status', 'Holiday')
            ->update(['status' => 'Regular Holiday']);

        // Finally, remove the old 'Holiday' value from the enum
        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM('Working', 'Day Off', 'Leave', 'Regular Holiday', 'Special Holiday', 'Overtime') DEFAULT 'Working'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update 'Special Holiday' and 'Regular Holiday' back to 'Holiday'
        DB::table('employee_schedules')
            ->whereIn('status', ['Special Holiday', 'Regular Holiday'])
            ->update(['status' => 'Holiday']);

        // Then restore the original enum
        DB::statement("ALTER TABLE employee_schedules MODIFY COLUMN status ENUM('Working', 'Day Off', 'Leave', 'Holiday', 'Overtime') DEFAULT 'Working'");
    }
};
