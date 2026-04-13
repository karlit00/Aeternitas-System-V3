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
        Schema::table('employee_other_infos', function (Blueprint $table) {
            // Hiring/Onboarding Documents
            $table->string('job_application')->nullable()->after('photo_path');
            $table->string('resume')->nullable();
            $table->string('offer_letter')->nullable();
            $table->string('employment_contract')->nullable();
            $table->string('onboarding_checklist')->nullable();

            // Employment Details
            $table->string('job_description')->nullable();
            $table->string('tax_forms')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('salary_history')->nullable();

            // Offboarding Documents
            $table->string('resignation_letter')->nullable();
            $table->string('exit_interview_records')->nullable();
            $table->string('termination_documentation')->nullable();

            // Confidential Files
            $table->string('medical_records')->nullable();
            $table->string('medical_leave_documents')->nullable();
            $table->string('health_insurance_info')->nullable();
            $table->string('background_checks')->nullable();
            $table->string('child_support_garnishment')->nullable();
            $table->string('bank_details')->nullable();
            $table->string('i9_forms')->nullable();
            $table->string('work_eligibility')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_other_infos', function (Blueprint $table) {
            // Hiring/Onboarding Documents
            $table->dropColumn('job_application');
            $table->dropColumn('resume');
            $table->dropColumn('offer_letter');
            $table->dropColumn('employment_contract');
            $table->dropColumn('onboarding_checklist');

            // Employment Details
            $table->dropColumn('job_description');
            $table->dropColumn('tax_forms');
            $table->dropColumn('emergency_contact');
            $table->dropColumn('salary_history');

            // Offboarding Documents
            $table->dropColumn('resignation_letter');
            $table->dropColumn('exit_interview_records');
            $table->dropColumn('termination_documentation');

            // Confidential Files
            $table->dropColumn('medical_records');
            $table->dropColumn('medical_leave_documents');
            $table->dropColumn('health_insurance_info');
            $table->dropColumn('background_checks');
            $table->dropColumn('child_support_garnishment');
            $table->dropColumn('bank_details');
            $table->dropColumn('i9_forms');
            $table->dropColumn('work_eligibility');
        });
    }
};