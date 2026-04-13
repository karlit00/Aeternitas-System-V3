<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Company;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class EmployeeInfoController extends Controller
{
    /**
     * Display the employee info list with documents
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentCompany = $this->getCurrentCompany($user);

        // Get departments for filtering
        $departments = Department::query();
        if ($currentCompany) {
            $departments->forCompany($currentCompany->id);
        }
        $departments = $departments->orderBy('name')->get();

        // Build employee query with document count
        $query = Employee::with(['department', 'position', 'documents'])
            ->withCount('documents')
            ->whereHas('account', function($q) {
                $q->where('is_active', true);
            });

        // Apply company filter
        if ($currentCompany) {
            $query->forCompany($currentCompany->id);
        }

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhereHas('department', function($deptQuery) use ($search) {
                      $deptQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort by name
        $query->orderBy('first_name')->orderBy('last_name');

        // Paginate results
        $employees = $query->paginate(15)->withQueryString();

        return view('employee-info.index', compact('user', 'employees', 'departments', 'currentCompany'));
    }

    /**
     * Get employee documents for modal
     */
    public function getEmployeeDocuments($employeeId)
    {
        $user = Auth::user();
        $currentCompany = $this->getCurrentCompany($user);

        // Get employee with documents, ensuring they belong to current company
        $employee = Employee::with(['department', 'position', 'documents'])
            ->withCount('documents')
            ->whereHas('account', function($q) {
                $q->where('is_active', true);
            });

        if ($currentCompany) {
            $employee->forCompany($currentCompany->id);
        }

        $employee = $employee->find($employeeId);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found or not in current company'
            ], 404);
        }

        $html = view('employee-info.partials.employee-documents-modal', compact('employee'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'employee' => [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'employee_id' => $employee->employee_id,
                'position' => $employee->position,
                'department' => $employee->department->name ?? 'N/A'
            ]
        ]);
    }

    /**
     * Get current company based on user role and session
     */
    private function getCurrentCompany($user)
    {
        // Get current company from session or user
        $currentCompanyId = session('current_company_id');

        // If no company in session, try to get from user's employee record
        if (!$currentCompanyId && $user->employee) {
            $currentCompanyId = $user->employee->company_id;
        }

        // If still no company, get the first company (for admin users)
        if (!$currentCompanyId && in_array($user->role, ['admin', 'hr'])) {
            $firstCompany = Company::first();
            $currentCompanyId = $firstCompany ? $firstCompany->id : null;
        }

        // Store in session for future use
        if ($currentCompanyId) {
            session(['current_company_id' => $currentCompanyId]);
        }

        return $currentCompanyId ? Company::find($currentCompanyId) : null;
    }

    /**
     * Download a specific document
     */
    public function downloadDocument($documentId)
    {
        $user = Auth::user();
        $currentCompany = $this->getCurrentCompany($user);

        $document = Document::with('employee')
            ->whereHas('employee', function($q) use ($currentCompany) {
                if ($currentCompany) {
                    $q->forCompany($currentCompany->id);
                }
                $q->whereHas('account', function($q) {
                    $q->where('is_active', true);
                });
            })
            ->find($documentId);

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found or access denied.');
        }

        // Check if file exists
        $filePath = storage_path('app/public/' . $document->path);
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Document file not found.');
        }

        return response()->download($filePath, $document->name . '.' . pathinfo($document->path, PATHINFO_EXTENSION));
    }

    /**
     * Delete a document
     */
    public function deleteDocument(Request $request, $documentId)
    {
        $user = Auth::user();
        $currentCompany = $this->getCurrentCompany($user);

        $document = Document::with('employee')
            ->whereHas('employee', function($q) use ($currentCompany) {
                if ($currentCompany) {
                    $q->forCompany($currentCompany->id);
                }
                $q->whereHas('account', function($q) {
                    $q->where('is_active', true);
                });
            })
            ->find($documentId);

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found or access denied.'
            ], 404);
        }

        try {
            // Delete file from storage
            $filePath = storage_path('app/public/' . $document->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete database record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document.'
            ], 500);
        }
    }
}