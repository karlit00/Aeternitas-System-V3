<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Add overtime_pay column
            if (!Schema::hasColumn('payrolls', 'overtime_pay')) {
                $table->decimal('overtime_pay', 10, 2)->default(0)->after('overtime_rate');
            }
            
            // Add allowances column
            if (!Schema::hasColumn('payrolls', 'allowances')) {
                $table->decimal('allowances', 10, 2)->default(0)->after('bonuses');
            }
            
            // Add night differential columns
            if (!Schema::hasColumn('payrolls', 'night_differential_hours')) {
                $table->decimal('night_differential_hours', 8, 2)->default(0)->after('scheduled_hours');
            }
            
            if (!Schema::hasColumn('payrolls', 'night_differential_rate')) {
                $table->decimal('night_differential_rate', 8, 2)->default(0)->after('night_differential_hours');
            }
            
            if (!Schema::hasColumn('payrolls', 'night_differential_pay')) {
                $table->decimal('night_differential_pay', 10, 2)->default(0)->after('night_differential_rate');
            }
            
            // Add rest day premium
            if (!Schema::hasColumn('payrolls', 'rest_day_premium_pay')) {
                $table->decimal('rest_day_premium_pay', 10, 2)->default(0)->after('night_differential_pay');
            }
            
            // Add paid_by column
            if (!Schema::hasColumn('payrolls', 'paid_by')) {
                $table->string('paid_by', 36)->nullable()->after('paid_at');
            }
            
            // Add payment_reference
            if (!Schema::hasColumn('payrolls', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payslip_file');
            }
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [
                'overtime_pay',
                'allowances',
                'night_differential_hours',
                'night_differential_rate',
                'night_differential_pay',
                'rest_day_premium_pay',
                'paid_by',
                'payment_reference'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};