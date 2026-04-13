<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_offences', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['employee_id']);
            
            // Change the column type to char(36) to match UUID
            $table->char('employee_id', 36)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_offences', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['employee_id']);
            
            // Revert to the previous type (if it was integer)
            $table->unsignedBigInteger('employee_id')->change();
            
            // Re-add the foreign key constraint
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }
};