<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'processed_by')) {
                // Drop existing foreign key if it exists
                try {
                    $table->dropForeign(['processed_by']);
                } catch (\Exception $e) {
                    // Ignore if not exists
                }
                $table->foreign('processed_by')
                    ->references('id')
                    ->on('accounts')
                    ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'processed_by')) {
                try {
                    $table->dropForeign(['processed_by']);
                } catch (\Exception $e) {
                    // Ignore if not exists
                }
            }
        });
    }
};
