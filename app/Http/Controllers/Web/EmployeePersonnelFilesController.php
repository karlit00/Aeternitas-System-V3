<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOtherInfo;
use App\Models\PreviousEmployment;
use App\Models\EmployeeBreak;
use App\Models\AttendanceRecord;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use App\Models\AttendanceException;
use App\Models\HrContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmployeePersonnelFilesController extends Controller
{
    /**
     * Display the main Employee Personnel Files dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get employees based on user role and company
        $employees = Employee::with(['position', 'department'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                return $query->where('company_id', $user->employee->company_id ?? null);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('hr.employee-personnel-files.index', compact('employees'));
    }

    /**
     * Display Hiring/Onboarding documents for an employee
     */
    public function showHiring($employeeId)
    {
        $employee = Employee::with(['position', 'department', 'employeeOtherInfo'])
            ->findOrFail($employeeId);
        
        // Check if user can access this employee's records
        $this->authorizeAccess($employee);

        // Get hiring documents and related data
        $employeeOtherInfo = $employee->employeeOtherInfo;
        $hiringData = [
            'job_application' => $employeeOtherInfo ? $employeeOtherInfo->job_application : null,
            'resume' => $employeeOtherInfo ? $employeeOtherInfo->resume : null,
            'offer_letter' => $employeeOtherInfo ? $employeeOtherInfo->offer_letter : null,
            'employment_contract' => $employeeOtherInfo ? $employeeOtherInfo->employment_contract : null,
            'onboarding_checklist' => $employeeOtherInfo ? $employeeOtherInfo->onboarding_checklist : null,
        ];

        return view('hr.employee-personnel-files.hiring', compact('employee', 'hiringData'));
    }

    /**
     * Display Employment Details for an employee
     */
    public function showEmployment($employeeId)
    {
        $employee = Employee::with(['position', 'department', 'employeeOtherInfo'])
            ->findOrFail($employeeId);
        
        // Check if user can access this employee's records
        $this->authorizeAccess($employee);

        // Get employment details
        $employeeOtherInfo = $employee->employeeOtherInfo;
        $employmentData = [
            'job_description' => $employeeOtherInfo ? $employeeOtherInfo->job_description : null,
            'tax_forms' => $employeeOtherInfo ? $employeeOtherInfo->tax_forms : null,
            'emergency_contact' => $employeeOtherInfo ? $employeeOtherInfo->emergency_contact : null,
            'salary_history' => $employeeOtherInfo ? $employeeOtherInfo->salary_history : null,
            'previous_employments' => $employee->previousEmployments ?? collect(),
        ];

        return view('hr.employee-personnel-files.employment', compact('employee', 'employmentData'));
    }

    /**
     * Display Performance & Development records for an employee
     */
    public function showPerformance($employeeId)
    {
        $employee = Employee::with(['position', 'department'])
            ->findOrFail($employeeId);
        
        // Check if user can access this employee's records
        $this->authorizeAccess($employee);

        // Get performance data
        $performanceData = [
            'performance_evaluations' => HrContact::where('employee_id', $employeeId)
                ->where('category', 'performance_evaluation')
                ->orderBy('created_at', 'desc')
                ->get(),
            'disciplinary_actions' => HrContact::where('employee_id', $employeeId)
                ->where('category', 'disciplinary_action')
                ->orderBy('created_at', 'desc')
                ->get(),
            'feedback_records' => HrContact::where('employee_id', $employeeId)
                ->where('category', 'feedback')
                ->orderBy('created_at', 'desc')
                ->get(),
            'training_records' => HrContact::where('employee_id', $employeeId)
                ->where('category', 'training')
                ->orderBy('created_at', 'desc')
                ->get(),
            'overtime_requests' => OvertimeRequest::where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->get(),
            'leave_requests' => LeaveRequest::where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->get(),
        ];

        return view('hr.employee-personnel-files.performance', compact('employee', 'performanceData'));
    }

    /**
     * Display Offboarding documents for an employee
     */
    public function showOffboarding($employeeId)
    {
        $employee = Employee::with(['position', 'department', 'employeeOtherInfo'])
            ->findOrFail($employeeId);
        
        // Check if user can access this employee's records
        $this->authorizeAccess($employee);

        // Get offboarding data
        $employeeOtherInfo = $employee->employeeOtherInfo;
        $offboardingData = [
            'resignation_letter' => $employeeOtherInfo ? $employeeOtherInfo->resignation_letter : null,
            'exit_interview_records' => $employeeOtherInfo ? $employeeOtherInfo->exit_interview_records : null,
            'termination_documentation' => $employeeOtherInfo ? $employeeOtherInfo->termination_documentation : null,
            'final_payroll_records' => $employee->payrolls()->where('status', 'paid')->orderBy('pay_period_end', 'desc')->get(),
        ];

        return view('hr.employee-personnel-files.offboarding', compact('employee', 'offboardingData'));
    }

    /**
     * Display Confidential files for an employee
     */
    public function showConfidential($employeeId)
    {
        $employee = Employee::with(['position', 'department', 'employeeOtherInfo'])
            ->findOrFail($employeeId);
        
        // Check if user can access this employee's records
        $this->authorizeAccess($employee);

        // Get confidential data
        $employeeOtherInfo = $employee->employeeOtherInfo;
        $confidentialData = [
            'medical_records' => $employeeOtherInfo ? $employeeOtherInfo->medical_records : null,
            'medical_leave_documents' => $employeeOtherInfo ? $employeeOtherInfo->medical_leave_documents : null,
            'health_insurance_info' => $employeeOtherInfo ? $employeeOtherInfo->health_insurance_info : null,
            'background_checks' => $employeeOtherInfo ? $employeeOtherInfo->background_checks : null,
            'child_support_garnishment' => $employeeOtherInfo ? $employeeOtherInfo->child_support_garnishment : null,
            'bank_details' => $employeeOtherInfo ? $employeeOtherInfo->bank_details : null,
            'i9_forms' => $employeeOtherInfo ? $employeeOtherInfo->i9_forms : null,
            'work_eligibility' => $employeeOtherInfo ? $employeeOtherInfo->work_eligibility : null,
        ];

        return view('hr.employee-personnel-files.confidential', compact('employee', 'confidentialData'));
    }

    /**
     * Authorize access to employee records based on user role and company
     */
    private function authorizeAccess(Employee $employee)
    {
        $user = Auth::user();
        
        // Admin can access all records
        if ($user->role === 'admin') {
            return true;
        }
        
        // HR and Manager can only access their company's employees
        if ($user->role === 'hr' || $user->role === 'manager') {
            if ($user->employee && $user->employee->company_id === $employee->company_id) {
                return true;
            }
        }
        
        // Deny access
        abort(403, 'Unauthorized access to employee records');
    }

    /**
     * Download a file from employee personnel files
     */
    public function downloadFile($employeeId, $category, $filename)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorizeAccess($employee);

        $filePath = "employee_files/{$employeeId}/{$category}/{$filename}";
        
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::download($filePath);
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    /**
     * Upload a file to employee personnel files
     */
    public function uploadFile(Request $request, $employeeId, $category)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorizeAccess($employee);

        $request->validate([
            'file' => 'required|file|max:2048|mimes:pdf,doc,docx', // Max 2MB, PDF/DOC only
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = "employee_files/{$employeeId}/{$category}/{$filename}";

        try {
            // Store file in filesystem using public disk
            $storedPath = Storage::disk('public')->putFileAs(
                "employee_files/{$employeeId}/{$category}",
                $file,
                $filename
            );
            
            // Use the stored path returned by putFileAs
            $filePath = $storedPath;

            // Update database with file path
            $employeeOtherInfo = $employee->employeeOtherInfo ?? new \App\Models\EmployeeOtherInfo();
            $employeeOtherInfo->employee_id = $employeeId;

            // Map category to database field
            $fieldMapping = [
                'hiring' => [
                    'job_application' => 'job_application',
                    'resume' => 'resume', 
                    'offer_letter' => 'offer_letter',
                    'employment_contract' => 'employment_contract',
                    'onboarding_checklist' => 'onboarding_checklist',
                ],
                'employment' => [
                    'job_description' => 'job_description',
                    'tax_forms' => 'tax_forms',
                    'emergency_contact' => 'emergency_contact',
                    'salary_history' => 'salary_history',
                ],
                'offboarding' => [
                    'resignation_letter' => 'resignation_letter',
                    'exit_interview_records' => 'exit_interview_records',
                    'termination_documentation' => 'termination_documentation',
                ],
                'confidential' => [
                    'medical_records' => 'medical_records',
                    'medical_leave_documents' => 'medical_leave_documents',
                    'health_insurance_info' => 'health_insurance_info',
                    'background_checks' => 'background_checks',
                    'child_support_garnishment' => 'child_support_garnishment',
                    'bank_details' => 'bank_details',
                    'i9_forms' => 'i9_forms',
                    'work_eligibility' => 'work_eligibility',
                ],
            ];

            // Determine the field name based on category and document type
            $documentType = $request->input('document_type');
            $fieldName = $fieldMapping[$category][$documentType] ?? null;

            if ($fieldName) {
                $employeeOtherInfo->$fieldName = $filePath;
                $employeeOtherInfo->save();
            }

            // Log the file upload
            Log::info("File uploaded to personnel files: {$employee->full_name} - {$category} - {$filename}");

            return redirect()->back()->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            Log::error("File upload failed: {$employee->full_name} - {$category} - {$filename}", [
                'error' => $e->getMessage(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType()
            ]);
            
            return redirect()->back()->with('error', 'File upload failed. Please try again with a smaller file (max 2MB).');
        }
    }

    /**
     * View a file from employee personnel files
     */
    public function viewFile($employeeId, $category, $filename)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorizeAccess($employee);

        // Get the employee other info to find the full path
        $employeeOtherInfo = $employee->employeeOtherInfo;
        if (!$employeeOtherInfo) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Find the field that contains this filename
        $fullPath = null;
        $fields = [
            'job_application', 'resume', 'offer_letter', 'employment_contract', 
            'onboarding_checklist', 'job_description', 'tax_forms', 'emergency_contact',
            'salary_history', 'resignation_letter', 'exit_interview_records', 
            'termination_documentation', 'medical_records', 'medical_leave_documents',
            'health_insurance_info', 'background_checks', 'child_support_garnishment',
            'bank_details', 'i9_forms', 'work_eligibility'
        ];

        foreach ($fields as $field) {
            if ($employeeOtherInfo->$field && basename($employeeOtherInfo->$field) === $filename) {
                $fullPath = $employeeOtherInfo->$field;
                break;
            }
        }

        // If we found the full path, use it
        if ($fullPath && Storage::disk('public')->exists($fullPath)) {
            try {
                return Storage::response($fullPath);
            } catch (\Exception $e) {
                // If Storage::response fails, try to serve the file directly
                $filePath = storage_path('app/public/' . $fullPath);
                if (file_exists($filePath)) {
                    return response()->file($filePath, [
                        'Content-Type' => mime_content_type($filePath),
                        'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"'
                    ]);
                }
            }
        }

        // Fallback to trying the constructed path
        $constructedPath = "employee_files/{$employeeId}/{$category}/{$filename}";
        if (Storage::disk('public')->exists($constructedPath)) {
            try {
                return Storage::response($constructedPath);
            } catch (\Exception $e) {
                // If Storage::response fails, try to serve the file directly
                $filePath = storage_path('app/public/' . $constructedPath);
                if (file_exists($filePath)) {
                    return response()->file($filePath, [
                        'Content-Type' => mime_content_type($filePath),
                        'Content-Disposition' => 'inline; filename="' . basename($constructedPath) . '"'
                    ]);
                }
            }
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    /**
     * Delete a file from employee personnel files
     */
    public function deleteFile($employeeId, $category, $filename)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorizeAccess($employee);

        $filePath = "employee_files/{$employeeId}/{$category}/{$filename}";
        
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            
            // Remove file path from database
            $employeeOtherInfo = $employee->employeeOtherInfo;
            if ($employeeOtherInfo) {
                // Map category to database field
                $fieldMapping = [
                    'hiring' => [
                        'job_application' => 'job_application',
                        'resume' => 'resume', 
                        'offer_letter' => 'offer_letter',
                        'employment_contract' => 'employment_contract',
                        'onboarding_checklist' => 'onboarding_checklist',
                    ],
                    'employment' => [
                        'job_description' => 'job_description',
                        'tax_forms' => 'tax_forms',
                        'emergency_contact' => 'emergency_contact',
                        'salary_history' => 'salary_history',
                    ],
                    'offboarding' => [
                        'resignation_letter' => 'resignation_letter',
                        'exit_interview_records' => 'exit_interview_records',
                        'termination_documentation' => 'termination_documentation',
                    ],
                    'confidential' => [
                        'medical_records' => 'medical_records',
                        'medical_leave_documents' => 'medical_leave_documents',
                        'health_insurance_info' => 'health_insurance_info',
                        'background_checks' => 'background_checks',
                        'child_support_garnishment' => 'child_support_garnishment',
                        'bank_details' => 'bank_details',
                        'i9_forms' => 'i9_forms',
                        'work_eligibility' => 'work_eligibility',
                    ],
                ];

                // Find which field contains this file path and clear it
                foreach ($fieldMapping[$category] as $fieldKey => $fieldName) {
                    if ($employeeOtherInfo->$fieldName === $filePath) {
                        $employeeOtherInfo->$fieldName = null;
                        break;
                    }
                }
                $employeeOtherInfo->save();
            }
            
            Log::info("File deleted from personnel files: {$employee->full_name} - {$category} - {$filename}");
            return redirect()->back()->with('success', 'File deleted successfully.');
        }

        return redirect()->back()->with('error', 'File not found.');
    }
}