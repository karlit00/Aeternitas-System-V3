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
        Schema::create('employee_offences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('offence_type');
            $table->text('description');
            $table->date('offence_date');
            $table->enum('severity', ['minor', 'major', 'serious'])->default('minor');
            $table->enum('status', ['pending', 'verified', 'dismissed'])->default('pending');
            $table->string('reported_by')->nullable();
            $table->text('action_taken')->nullable();
            $table->date('action_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['employee_id', 'offence_date']);
            $table->index(['offence_type']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_offences');
    }
};
