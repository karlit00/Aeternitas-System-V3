<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeOtherInfo extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'employee_id',
        'address',
        'pov_address',
        'no_street',
        'barangay',
        'town_district',
        'city_province',
        'birthplace',
        'religion',
        'blood_type',
        'citizenship',
        'height',
        'weight',
        'phone',
        'mobile',
        'drivers_license',
        'prc_no',
        'father',
        'mother',
        'spouse',
        'spouse_employed',
        'photo_path',
        
        // Hiring/Onboarding Documents
        'job_application',
        'resume',
        'offer_letter',
        'employment_contract',
        'onboarding_checklist',
        
        // Employment Details
        'job_description',
        'tax_forms',
        'emergency_contact',
        'salary_history',
        
        // Offboarding Documents
        'resignation_letter',
        'exit_interview_records',
        'termination_documentation',
        
        // Confidential Files
        'medical_records',
        'medical_leave_documents',
        'health_insurance_info',
        'background_checks',
        'child_support_garnishment',
        'bank_details',
        'i9_forms',
        'work_eligibility',
    ];

    protected $casts = [
        'spouse_employed' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
