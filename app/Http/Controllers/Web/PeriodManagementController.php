<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\AttendanceRecord;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeriodManagementController extends Controller
{
    /**
     * Display a listing of periods
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get periods from session or create default ones
        $periods = session('periods', []);
        
        // Get current filter state
        $filters = $this->getFilterState($request);
        
        return view('attendance.period-management.index', compact('periods', 'user', 'filters'));
    }

    /**
     * Show the form for creating a new period
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Get departments and employees for filtering
        $departments = \App\Models\Department::orderBy('name')->get();
        $employees = \App\Models\Employee::with('department')->orderBy('first_name')->get();
        
        // Get current filter state for back navigation
        $currentFilters = $this->getFilterState($request);
        
        return view('attendance.period-management.create', compact('user', 'currentFilters', 'departments', 'employees'));
    }

    /**
     * Store a newly created period
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get existing periods from session
        $periods = session('periods', []);
        
        // Create new period
        $period = [
            'id' => uniqid(),
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'department_id' => $request->department_id,
            'employee_ids' => $request->employee_ids ?? [],
            'created_by' => Auth::user()->full_name,
            'created_at' => now()->toDateTimeString(),
        ];
        
        // Add to periods array
        $periods[] = $period;
        
        // Store in session
        session(['periods' => $periods]);
        
        return redirect()->route('attendance.period-management.index')
            ->with('success', 'Period created successfully.');
    }

    /**
     * Display the specified period with attendance analysis
     */
    public function show(Request $request, $periodId)
    {
        $user = Auth::user();
        
        // Get periods from session
        $periods = session('periods', []);
        
        // Find the specific period
        $period = collect($periods)->firstWhere('id', $periodId);
        
        if (!$period) {
            return redirect()->route('attendance.period-management.index')
                ->with('error', 'Period not found.');
        }
        
        // Convert to Carbon dates
        $startDate = Carbon::parse($period['start_date']);
        $endDate = Carbon::parse($period['end_date']);
        
        // Get current filter state for back navigation
        $currentFilters = $this->getFilterState($request);
        
        // Get all employees or filtered employees
        $employees = Employee::with('department');
        
        // Apply department filter if specified
        if (!empty($period['department_id'])) {
            $employees = $employees->where('department_id', $period['department_id']);
        }
        
        // Apply specific employee filter if specified
        if (!empty($period['employee_ids']) && is_array($period['employee_ids'])) {
            $employees = $employees->whereIn('id', $period['employee_ids']);
        }
        
        $employees = $employees->get();
        
        // Get comprehensive attendance data for the period
        $comprehensiveData = $this->getComprehensiveAttendanceData($startDate, $endDate, $employees);
        
        // Calculate summary statistics
        $summaryData = $this->calculateSummaryDataFromComprehensive($comprehensiveData);
        
        return view('attendance.period-management.show', compact(
            'period', 
            'user', 
            'currentFilters', 
            'comprehensiveData',
            'summaryData',
            'employees'
        ));
    }

    /**
     * Remove the specified period
     */
    public function destroy(Request $request, $periodId = null)
    {
        // Handle both URL parameter and form submission
        $targetPeriodId = $periodId ?: $request->input('period_id');
        
        if (!$targetPeriodId) {
            return redirect()->route('attendance.period-management.index')
                ->with('error', 'Period ID is required.');
        }
        
        // Get periods from session
        $periods = session('periods', []);
        
        // Find the period to get its name for the success message
        $periodToDelete = collect($periods)->firstWhere('id', $targetPeriodId);
        
        if (!$periodToDelete) {
            return redirect()->route('attendance.period-management.index')
                ->with('error', 'Period not found.');
        }
        
        // Remove the period
        $periods = collect($periods)->reject(function ($period) use ($targetPeriodId) {
            return $period['id'] === $targetPeriodId;
        })->values()->toArray();
        
        // Store updated periods in session
        session(['periods' => $periods]);
        
        return redirect()->route('attendance.period-management.index')
            ->with('success', "Period '{$periodToDelete['name']}' deleted successfully.");
    }

    /**
     * Get comprehensive attendance data for all employees in the period
     * 
     * This method creates a detailed attendance report by:
     * 1. Iterating through each employee and each date in the period
     * 2. Fetching attendance records and employee schedules for each day
     * 3. Calculating worked hours, overtime, and attendance status
     * 4. Formatting all data for display in the comprehensive table
     * 
     * @param Carbon $startDate Start date of the period
     * @param Carbon $endDate End date of the period
     * @param Collection $employees Collection of employees to analyze
     * @return array Comprehensive attendance data array
     */
    private function getComprehensiveAttendanceData($startDate, $endDate, $employees)
    {
        $comprehensiveData = [];
        
        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Get attendance record for this date
                $attendanceRecord = AttendanceRecord::where('employee_id', $employee->id)
                    ->where('date', $dateStr)
                    ->first();
                
                // Get schedule for this date
                $schedule = EmployeeSchedule::where('employee_id', $employee->id)
                    ->where('date', $dateStr)
                    ->first();
                
                // Determine schedule status
                $scheduleStatus = $this->getScheduleStatus($schedule);
                
                // Determine attendance status
                $attendanceStatus = $this->getAttendanceStatus($attendanceRecord, $schedule);
                
                // Calculate worked hours and overtime
                $workedHours = $this->calculateWorkedHours($attendanceRecord, $schedule);
                $overtime = $this->calculateOvertime($attendanceRecord, $schedule);
                
                // Format times
                $scheduleInOut = $this->formatScheduleTime($schedule);
                $actualInOut = $this->formatActualTime($attendanceRecord);
                $workingHours = $this->calculateWorkingHours($schedule);
                
                $comprehensiveData[] = [
                    'employee_id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'employee_name' => $employee->full_name,
                    'date' => $dateStr,
                    'date_formatted' => $currentDate->format('M j, Y'),
                    'schedule_in_out' => $scheduleInOut,
                    'working_hours' => $workingHours,
                    'actual_in_out' => $actualInOut,
                    'worked_hours' => $workedHours,
                    'overtime' => $overtime,
                    'schedule_status' => $scheduleStatus,
                    'attendance_status' => $attendanceStatus,
                    'combined_status' => $scheduleStatus . ' - ' . $attendanceStatus,
                ];
                
                $currentDate->addDay();
            }
        }
        
        return $comprehensiveData;
    }
    
    /**
     * Get schedule status
     */
    private function getScheduleStatus($schedule)
    {
        if (!$schedule) {
            return 'Day Off'; // Mark as Day Off if no schedule
        }
        
        switch ($schedule->status) {
            case 'Working':
                return 'Working';
            case 'Day Off':
            case 'Rest Day':
                return 'Rest Day';
            case 'Holiday':
                return 'Holiday';
            case 'Leave':
                return 'Leave';
            default:
                return 'Day Off'; // Default to Day Off for unknown status
        }
    }
    
    /**
     * Determine attendance status based on time in/out and schedule
     * 
     * This method implements a comprehensive attendance logic system:
     * 
     * Rule 1: No Time In and No Time Out → Absent
     * Rule 2: Has only Time In or Time Out → Error  
     * Rule 3: Time In is later than Schedule Out → Absent (compares time parts only)
     * Rule 4: Both times completely outside scheduled shift → Absent
     * Rule 5: Has both Time In and Time Out (valid range) → Present
     * 
     * Key Fix: Uses time-only comparison (H:i:s) instead of full datetime
     * to prevent incorrect "Absent" results when schedule dates differ
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return string Attendance status: 'Present', 'Absent', 'Error'
     */
    private function getAttendanceStatus($attendanceRecord, $schedule = null)
    {
        if (!$attendanceRecord) {
            return 'Absent';
        }
        
        $hasTimeIn = !empty($attendanceRecord->time_in);
        $hasTimeOut = !empty($attendanceRecord->time_out);
        
        // Rule 1: No Time In and No Time Out → Absent
        if (!$hasTimeIn && !$hasTimeOut) {
            return 'Absent';
        }
        
        // Rule 2: Has only Time In or Time Out → Error
        if (($hasTimeIn && !$hasTimeOut) || (!$hasTimeIn && $hasTimeOut)) {
            return 'Error';
        }
        
        // Rule 3: Time In is later than Schedule Out → Absent (compare only time parts)
        if ($hasTimeIn && $schedule && $schedule->time_out) {
            $timeIn = Carbon::parse($attendanceRecord->time_in);
            $scheduleOut = Carbon::parse($schedule->time_out);
            
            // Compare only time parts to avoid date comparison issues
            if ($timeIn->format('H:i:s') > $scheduleOut->format('H:i:s')) {
                return 'Absent';
            }
        }
        
        // Rule 4: Both times are completely outside the scheduled shift → Absent (compare only time parts)
        if ($hasTimeIn && $hasTimeOut && $schedule && $schedule->time_in && $schedule->time_out) {
            $timeIn = Carbon::parse($attendanceRecord->time_in);
            $timeOut = Carbon::parse($attendanceRecord->time_out);
            $scheduleIn = Carbon::parse($schedule->time_in);
            $scheduleOut = Carbon::parse($schedule->time_out);
            
            // Compare only time parts to avoid date comparison issues
            $timeInTime = $timeIn->format('H:i:s');
            $timeOutTime = $timeOut->format('H:i:s');
            $scheduleInTime = $scheduleIn->format('H:i:s');
            $scheduleOutTime = $scheduleOut->format('H:i:s');
            
            // Only mark as Absent if both times are completely outside working range
            // (not just overtime - overtime is valid)
            if (
                ($timeInTime < $scheduleInTime && $timeOutTime < $scheduleInTime) ||
                ($timeInTime > $scheduleOutTime && $timeOutTime > $scheduleOutTime)
            ) {
                return 'Absent';
            }
        }
        
        // Rule 5: Has both Time In and Time Out (valid range) → Present
        if ($hasTimeIn && $hasTimeOut) {
            return 'Present';
        }
        
        return 'Absent';
    }
    
    /**
     * Calculate overtime based on worked hours vs scheduled working hours
     * 
     * This method implements a simplified overtime calculation:
     * 1. Calculates actual worked hours (time_out - time_in - 1 hour lunch break)
     * 2. Calculates scheduled working hours (schedule_out - schedule_in - 1 hour lunch break)
     * 3. Returns the difference as overtime if worked hours > scheduled hours
     * 
     * Key Features:
     * - Automatically subtracts 1 hour for lunch break from both calculations
     * - Uses diffInMinutes/60 to avoid negative values
     * - Returns 0 if no overtime (worked hours <= scheduled hours)
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return float Overtime hours (rounded to 2 decimal places)
     */
    private function calculateOvertime($attendanceRecord, $schedule)
    {
        if (
            !$attendanceRecord || !$schedule ||
            !$attendanceRecord->time_in || !$attendanceRecord->time_out ||
            !$schedule->time_in || !$schedule->time_out
        ) {
            return 0;
        }

        // Calculate worked hours (subtract 1 hour for lunch break)
        $timeIn = Carbon::parse($attendanceRecord->time_in);
        $timeOut = Carbon::parse($attendanceRecord->time_out);
        $totalHours = round($timeIn->diffInMinutes($timeOut) / 60, 2);
        $workedHours = max(0, $totalHours - 1); // Subtract 1 hour for lunch break

        // Calculate scheduled working hours (subtract 1 hour for lunch break)
        $scheduleIn = Carbon::parse($schedule->time_in);
        $scheduleOut = Carbon::parse($schedule->time_out);
        $totalScheduleHours = round($scheduleIn->diffInMinutes($scheduleOut) / 60, 2);
        $workingHours = max(0, $totalScheduleHours - 1); // Subtract 1 hour for lunch break

        // Calculate overtime as difference between worked and scheduled hours
        $overtime = 0;
        if ($workedHours > $workingHours) {
            $overtime = round($workedHours - $workingHours, 2);
        }

        return $overtime;
    }
    
    /**
     * Format schedule time
     */
    private function formatScheduleTime($schedule)
    {
        if (!$schedule || !$schedule->time_in || !$schedule->time_out) {
            return '—';
        }
        
        $timeIn = Carbon::parse($schedule->time_in)->format('H:i');
        $timeOut = Carbon::parse($schedule->time_out)->format('H:i');
        
        return "{$timeIn}–{$timeOut}";
    }
    
    /**
     * Format actual time
     */
    private function formatActualTime($attendanceRecord)
    {
        if (!$attendanceRecord) {
            return '—';
        }
        
        $timeIn = $attendanceRecord->time_in ? Carbon::parse($attendanceRecord->time_in)->format('H:i') : '—';
        $timeOut = $attendanceRecord->time_out ? Carbon::parse($attendanceRecord->time_out)->format('H:i') : '—';
        
        return "{$timeIn}–{$timeOut}";
    }
    
    /**
     * Calculate working hours from schedule (subtract 1 hour for lunch break)
     */
    private function calculateWorkingHours($schedule)
    {
        if (!$schedule || !$schedule->time_in || !$schedule->time_out) {
            return '—';
        }
        
        $timeIn = Carbon::parse($schedule->time_in);
        $timeOut = Carbon::parse($schedule->time_out);
        
        $totalHours = round($timeIn->diffInMinutes($timeOut) / 60, 2);
        $workingHours = max(0, $totalHours - 1); // Subtract 1 hour for lunch break
        
        return $workingHours . ' hrs';
    }
    
    /**
     * Calculate worked hours from actual attendance with lunch break deduction
     * 
     * This method calculates the actual hours worked by an employee:
     * 1. Validates that both time_in and time_out exist
     * 2. Checks if time_in is later than schedule_out (invalid attendance)
     * 3. Checks if both times are completely outside scheduled range
     * 4. Calculates total duration and subtracts 1 hour for lunch break
     * 5. Ensures non-negative result using max(0, ...)
     * 
     * Key Features:
     * - Uses time-only comparison to avoid datetime issues
     * - Automatically deducts 1 hour for lunch break
     * - Returns "0 hrs" for invalid attendance
     * - Returns "—" for missing time data
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return string Worked hours formatted as "X.XX hrs" or "0 hrs" or "—"
     */
    private function calculateWorkedHours($attendanceRecord, $schedule = null)
    {
        if (!$attendanceRecord || !$attendanceRecord->time_in || !$attendanceRecord->time_out) {
            return '—';
        }
        
        $timeIn = Carbon::parse($attendanceRecord->time_in);
        $timeOut = Carbon::parse($attendanceRecord->time_out);
        
        // Do not calculate worked hours if time_in > schedule_out (compare only time parts)
        if ($schedule && $schedule->time_out) {
            $scheduleOut = Carbon::parse($schedule->time_out);
            
            // Compare only time parts to avoid date comparison issues
            if ($timeIn->format('H:i:s') > $scheduleOut->format('H:i:s')) {
                return '0 hrs'; // Invalid attendance - no worked hours
            }
        }
        
        // Only return 0 hrs if both times are completely outside schedule range (compare only time parts)
        if ($schedule && $schedule->time_in && $schedule->time_out) {
            $scheduleIn = Carbon::parse($schedule->time_in);
            $scheduleOut = Carbon::parse($schedule->time_out);
            
            // Compare only time parts to avoid date comparison issues
            $timeInTime = $timeIn->format('H:i:s');
            $timeOutTime = $timeOut->format('H:i:s');
            $scheduleInTime = $scheduleIn->format('H:i:s');
            $scheduleOutTime = $scheduleOut->format('H:i:s');
            
            // If both actual times are completely outside working range
            if (
                ($timeInTime < $scheduleInTime && $timeOutTime < $scheduleInTime) ||
                ($timeInTime > $scheduleOutTime && $timeOutTime > $scheduleOutTime)
            ) {
                return '0 hrs';
            }
        }
        
        // Calculate worked hours using diffInMinutes to avoid negative values
        // Subtract 1 hour for lunch break
        $totalHours = round($timeIn->diffInMinutes($timeOut) / 60, 2);
        $workedHours = max(0, $totalHours - 1); // Ensure non-negative result
        
        return $workedHours . ' hrs';
    }
    
    /**
     * Calculate summary data from comprehensive data
     */
    private function calculateSummaryDataFromComprehensive($comprehensiveData)
    {
        $summary = [
            'total_employees' => collect($comprehensiveData)->pluck('employee_id')->unique()->count(),
            'total_records' => count($comprehensiveData),
            'present_days' => collect($comprehensiveData)->where('attendance_status', 'Present')->count(),
            'absent_days' => collect($comprehensiveData)->where('attendance_status', 'Absent')->count(),
            'error_days' => collect($comprehensiveData)->where('attendance_status', 'Error')->count(),
            'total_overtime_hours' => collect($comprehensiveData)->sum('overtime'),
            'working_days' => collect($comprehensiveData)->where('schedule_status', 'Working')->count(),
            'holiday_days' => collect($comprehensiveData)->where('schedule_status', 'Holiday')->count(),
            'rest_days' => collect($comprehensiveData)->where('schedule_status', 'Rest Day')->count(),
        ];
        
        return $summary;
    }





    /**
     * Get current filter state from request
     */
    private function getFilterState(Request $request)
    {
        return array_filter([
            'search' => $request->get('search'),

            'department_id' => $request->get('department_id'),
        ]);
    }
}


