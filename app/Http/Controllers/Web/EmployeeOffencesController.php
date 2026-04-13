<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOffence;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmployeeOffencesController extends Controller
{
    /**
     * Display a listing of employee offences.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get current company
        $currentCompany = \App\Helpers\CompanyHelper::getCurrentCompany();
        
        // Build query
        $query = EmployeeOffence::with('employee');
        
        if ($currentCompany) {
            $query->forCompany($currentCompany->id);
        }
        
        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('department') && $request->department) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }
        
        if ($request->has('severity') && $request->severity) {
            $query->where('severity', $request->severity);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->where('offence_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('offence_date', '<=', $request->end_date);
        }
        
        // Order by most recent
        $query->orderBy('created_at', 'desc');
        
        // Get paginated results with error handling
        try {
            $offences = $query->paginate(15);
        } catch (\Exception $e) {
            $offences = collect([])->paginate(15);
        }
        
        // Get filter options
        $departments = $currentCompany 
            ? Department::forCompany($currentCompany->id)->get() 
            : Department::all();
        
        $positions = $currentCompany 
            ? Position::forCompany($currentCompany->id)->get() 
            : Position::all();
        
        $employees = $currentCompany 
            ? Employee::forCompany($currentCompany->id)->get() 
            : Employee::all();
        
        // Statistics with error handling
        $stats = [
            'total' => 0,
            'pending' => 0,
            'verified' => 0,
            'serious' => 0,
        ];
        
        try {
            $stats = [
                'total' => EmployeeOffence::when($currentCompany, fn($q) => $q->forCompany($currentCompany->id))->count(),
                'pending' => EmployeeOffence::when($currentCompany, fn($q) => $q->forCompany($currentCompany->id))->pending()->count(),
                'verified' => EmployeeOffence::when($currentCompany, fn($q) => $q->forCompany($currentCompany->id))->verified()->count(),
                'serious' => EmployeeOffence::when($currentCompany, fn($q) => $q->forCompany($currentCompany->id))->bySeverity('serious')->count(),
            ];
        } catch (\Exception $e) {
            // Keep default values if query fails
        }
        
        return view('hr.reports.employee-offences', [
            'offences' => $offences,
            'departments' => $departments,
            'positions' => $positions,
            'employees' => $employees,
            'stats' => $stats,
            'currentCompany' => $currentCompany,
        ]);
    }

    /**
     * Store a newly created offence.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'offence_type' => 'required|string|max:255',
            'description' => 'required|string',
            'offence_date' => 'required|date',
            'severity' => 'required|in:minor,major,serious',
            'reported_by' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        EmployeeOffence::create([
            'employee_id' => $request->employee_id,
            'offence_type' => $request->offence_type,
            'description' => $request->description,
            'offence_date' => $request->offence_date,
            'severity' => $request->severity,
            'status' => 'pending',
            'reported_by' => $request->reported_by ?? auth()->user()->name ?? 'System',
        ]);
        
        return redirect()->back()->with('success', 'Employee offence record created successfully.');
    }

    /**
     * Update the specified offence.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'offence_type' => 'required|string|max:255',
            'description' => 'required|string',
            'offence_date' => 'required|date',
            'severity' => 'required|in:minor,major,serious',
            'status' => 'required|in:pending,verified,dismissed',
            'reported_by' => 'nullable|string|max:255',
            'action_taken' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            // If AJAX request, return JSON with errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $offence = EmployeeOffence::findOrFail($id);
        
        $offence->update([
            'employee_id' => $request->employee_id,
            'offence_type' => $request->offence_type,
            'description' => $request->description,
            'offence_date' => $request->offence_date,
            'severity' => $request->severity,
            'status' => $request->status,
            'reported_by' => $request->reported_by ?? $offence->reported_by,
            'action_taken' => $request->action_taken,
            'action_date' => $request->status !== 'pending' ? Carbon::now() : $offence->action_date,
            'notes' => $request->notes,
        ]);
        
        // If AJAX request, return JSON response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Employee offence record updated successfully.',
                'offence' => $offence
            ]);
        }
        
        return redirect()->back()->with('success', 'Employee offence record updated successfully.');
    }

    /**
     * Update the specified offence status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,verified,dismissed',
            'action_taken' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $offence = EmployeeOffence::findOrFail($id);
        $offence->update([
            'status' => $request->status,
            'action_taken' => $request->action_taken,
            'action_date' => $request->status !== 'pending' ? Carbon::now() : null,
            'notes' => $request->notes,
        ]);
        
        return response()->json(['message' => 'Offence status updated successfully.']);
    }

    /**
     * Display the specified offence (for AJAX modal view and detailed view).
     */
    public function show($id)
    {
        $offence = EmployeeOffence::with('employee')->findOrFail($id);
        
        // If request is AJAX, return JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'id' => $offence->id,
                'employee_id' => $offence->employee_id,
                'offence_type' => $offence->offence_type,
                'description' => $offence->description,
                'offence_date' => $offence->offence_date->format('Y-m-d'),
                'severity' => $offence->severity,
                'status' => $offence->status,
                'reported_by' => $offence->reported_by,
                'action_taken' => $offence->action_taken,
                'action_date' => $offence->action_date ? $offence->action_date->format('Y-m-d') : null,
                'notes' => $offence->notes,
                'created_at' => $offence->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $offence->updated_at->format('Y-m-d H:i:s'),
                'severity_badge_class' => $offence->getSeverityBadgeClassAttribute(),
                'status_badge_class' => $offence->getStatusBadgeClassAttribute(),
                'employee' => [
                    'id' => $offence->employee->id,
                    'employee_id' => $offence->employee->employee_id,
                    'full_name' => $offence->employee->full_name,
                    'first_name' => $offence->employee->first_name,
                    'last_name' => $offence->employee->last_name,
                    'email' => $offence->employee->email,
                    'department' => $offence->employee->department ? $offence->employee->department->name : null,
                    'position' => $offence->employee->position ? $offence->employee->position->name : null,
                ],
            ]);
        }
        
        // Otherwise return blade view for detailed page view
        return view('hr.reports.employee-offences-detail', compact('offence'));
    }

    /**
     * Remove the specified offence from storage.
     */
    public function destroy($id)
    {
        try {
            $offence = EmployeeOffence::findOrFail($id);
            $offencial_data = [
                'id' => $offence->id,
                'employee_id' => $offence->employee_id,
                'offence_type' => $offence->offence_type,
            ];
            
            $offence->delete();
            
            // If AJAX request, return JSON
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Offence record deleted successfully.',
                    'offence' => $offencial_data
                ]);
            }
            
            return redirect()->back()->with('success', 'Offence record deleted successfully.');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete offence record: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete offence record.');
        }
    }

    /**
     * Get offences for a specific employee.
     */
    public function getByEmployee($employeeId)
    {
        $offences = EmployeeOffence::where('employee_id', $employeeId)
            ->orderBy('offence_date', 'desc')
            ->get();
        
        return response()->json($offences);
    }
}
