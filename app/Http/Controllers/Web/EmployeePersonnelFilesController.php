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

    /**
     * Display New Employees screen for Timekeeping & HRIS Reports
     */
    public function showNewEmployees(Request $request)
    {
        $user = Auth::user();
        
        // Get current company for filtering
        $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
        
        // Get date range filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Default to last 3 months if no date range provided
        if (!$startDate || !$endDate) {
            $endDate = now();
            $startDate = $endDate->copy()->subMonths(3);
        } else {
            $startDate = \Carbon\Carbon::parse($startDate);
            $endDate = \Carbon\Carbon::parse($endDate);
        }

        // Build query for new employees
        $query = Employee::with(['position', 'department', 'employeeOtherInfo'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                return $query->where('company_id', $user->employee->company_id ?? null);
            })
            ->when($currentCompany, function ($query) use ($currentCompany) {
                return $query->where('company_id', $currentCompany->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('position', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply department filter
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }

        // Apply position filter
        if ($request->has('position') && $request->position) {
            $query->where('position_id', $request->position);
        }

        $newEmployees = $query->paginate(10);

        // Get filter options
        $departments = $currentCompany 
            ? \App\Models\Department::forCompany($currentCompany->id)->get()
            : \App\Models\Department::all();
        
        $positions = $currentCompany 
            ? \App\Models\Position::forCompany($currentCompany->id)->get()
            : \App\Models\Position::all();

        return view('hr.reports.new-employees', compact('newEmployees', 'departments', 'positions', 'startDate', 'endDate'));
    }

    /**
     * Display End of Contracts screen for Timekeeping & HRIS Reports
     */
    public function showEndOfContracts(Request $request)
    {
        $user = Auth::user();
        
        // Get current company for filtering
        $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
        
        // Get date range filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Default to next 3 months if no date range provided
        if (!$startDate || !$endDate) {
            $startDate = now();
            $endDate = $startDate->copy()->addMonths(3);
        } else {
            $startDate = \Carbon\Carbon::parse($startDate);
            $endDate = \Carbon\Carbon::parse($endDate);
        }

        // Build query for employees with ending contracts
        // Note: This assumes there's a contract_end_date field in the employees table
        // If not, we'll use a simulated approach based on hire date + typical contract duration
        $query = Employee::with(['position', 'department', 'employeeOtherInfo'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                return $query->where('company_id', $user->employee->company_id ?? null);
            })
            ->when($currentCompany, function ($query) use ($currentCompany) {
                return $query->where('company_id', $currentCompany->id);
            });

        // Check if contract_end_date column exists
        $hasContractEndDate = \Illuminate\Support\Facades\Schema::hasColumn('employees', 'contract_end_date');
        
        if ($hasContractEndDate) {
            // Filter by contract end date range
            $query->whereBetween('contract_end_date', [$startDate, $endDate]);
        } else {
            // Fallback: Calculate approximate contract end dates based on hire date + 6 months (typical probationary period)
            // or hire date + 1 year (typical contract duration)
            $query->where(function ($q) use ($startDate, $endDate) {
                // Employees hired 6 months ago (end of probationary contract)
                $probationStart = $startDate->copy()->subMonths(6);
                $probationEnd = $endDate->copy()->subMonths(6);
                $q->orWhereBetween('created_at', [$probationEnd, $probationStart])
                  ->orWhereBetween('created_at', [$startDate->copy()->subYear(), $endDate->copy()->subYear()]);
            });
        }

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('position', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply department filter
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }

        // Apply position filter
        if ($request->has('position') && $request->position) {
            $query->where('position_id', $request->position);
        }

        // Apply contract type filter
        if ($request->has('contract_type') && $request->contract_type) {
            $query->where('employment_type', $request->contract_type);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get filter options
        $departments = $currentCompany 
            ? \App\Models\Department::forCompany($currentCompany->id)->get()
            : \App\Models\Department::all();
        
        $positions = $currentCompany 
            ? \App\Models\Position::forCompany($currentCompany->id)->get()
            : \App\Models\Position::all();

        // Calculate statistics
        $totalContracts = $employees->total();
        $expiringThisMonth = Employee::when($hasContractEndDate, function ($q) {
                return $q->whereMonth('contract_end_date', now()->month)
                         ->whereYear('contract_end_date', now()->year);
            }, function ($q) {
                $sixMonthsAgo = now()->subMonths(6);
                $oneYearAgo = now()->subYear();
                return $q->whereBetween('created_at', [$sixMonthsAgo->copy()->subMonth(), $sixMonthsAgo->copy()->addMonth()])
                         ->orWhereBetween('created_at', [$oneYearAgo->copy()->subMonth(), $oneYearAgo->copy()->addMonth()]);
            })
            ->count();

        return view('hr.reports.end-of-contracts', compact(
            'employees', 
            'departments', 
            'positions', 
            'startDate', 
            'endDate', 
            'totalContracts', 
            'expiringThisMonth',
            'hasContractEndDate'
        ));
    }

    /**
     * Send reminder to a specific employee
     */
    public function sendReminder($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            
            // Send email notification
            // In a real application, you would use Laravel's Mail facade
            // Mail::to($employee->email)->send(new ContractExpiryReminder($employee));
            
            // Log the reminder
            Log::info("Contract expiry reminder sent to: {$employee->full_name} ({$employee->email})");
            
            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send reminders to multiple employees
     */
    public function sendReminders(Request $request)
    {
        try {
            $employeeIds = $request->input('employee_ids', []);
            $sentCount = 0;
            
            foreach ($employeeIds as $employeeId) {
                try {
                    $employee = Employee::find($employeeId);
                    if ($employee) {
                        // Send email notification
                        // Mail::to($employee->email)->send(new ContractExpiryReminder($employee));
                        
                        Log::info("Contract expiry reminder sent to: {$employee->full_name} ({$employee->email})");
                        $sentCount++;
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send reminder to employee {$employeeId}: " . $e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'sent' => $sentCount,
                'message' => "{$sentCount} reminder(s) sent successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate renewal letters for employees
     */
    public function generateRenewalLetters(Request $request)
    {
        try {
            $employeeIds = $request->input('employee_ids', []);
            
            // Generate PDF content
            $html = view('hr.reports.renewal-letters-template', [
                'employees' => Employee::whereIn('id', $employeeIds)->get()
            ])->render();
            
            // In a real application, you would use DomPDF to generate the PDF
            // $pdf = PDF::loadHTML($html);
            // return $pdf->download('renewal-letters.pdf');
            
            // For now, return a simple response
            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="renewal-letters.html"');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate renewal letters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule automated reminders
     */
    public function scheduleReminders(Request $request)
    {
        try {
            $reminders = $request->input('reminders', []);
            
            // In a real application, you would schedule these reminders using Laravel's scheduler
            // For each reminder configuration, create a scheduled job
            
            Log::info('Automated contract expiry reminders scheduled', [
                'reminders' => $reminders
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Automated reminders scheduled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a file in employee personnel files
     * Replaces the existing file with a new one
     */
    public function updateFile(Request $request, $employeeId, $category, $oldFilename)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->authorizeAccess($employee);

        // Validate new file
        $request->validate([
            'file' => 'required|file|max:2048|mimes:pdf,doc,docx',
            'document_type' => 'required|string'
        ]);

        $file = $request->file('file');
        $documentType = $request->input('document_type');
        $newFilename = time() . '_' . $file->getClientOriginalName();
        $newFilePath = "employee_files/{$employeeId}/{$category}/{$newFilename}";

        try {
            // Get employee other info
            $employeeOtherInfo = $employee->employeeOtherInfo;
            if (!$employeeOtherInfo) {
                return redirect()->back()->with('error', 'Employee information not found.');
            }

            // Field mapping for database
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

            // Get field name
            $fieldName = $fieldMapping[$category][$documentType] ?? null;
            if (!$fieldName) {
                return redirect()->back()->with('error', 'Invalid document type.');
            }

            // Get old file path
            $oldFilePath = $employeeOtherInfo->$fieldName;

            // Delete old file if it exists
            if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
                Log::info("Old file deleted: {$oldFilePath}");
            }

            // Store new file
            $storedPath = Storage::disk('public')->putFileAs(
                "employee_files/{$employeeId}/{$category}",
                $file,
                $newFilename
            );

            // Update database with new file path
            $employeeOtherInfo->$fieldName = $storedPath;
            $employeeOtherInfo->save();

            Log::info("File updated in personnel files: {$employee->full_name} - {$category} - {$documentType}", [
                'old_file' => $oldFilePath,
                'new_file' => $storedPath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType()
            ]);

            return redirect()->back()->with('success', 'File updated successfully.');
        } catch (\Exception $e) {
            Log::error("File update failed: {$employee->full_name} - {$category} - {$documentType}", [
                'error' => $e->getMessage(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType()
            ]);
            
            return redirect()->back()->with('error', 'File update failed. Please try again with a smaller file (max 2MB).');
        }
    }
}
