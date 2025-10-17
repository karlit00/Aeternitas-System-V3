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
                $scheduledHours = $this->calculateScheduledHours($attendanceRecord, $schedule);
                $morningOvertime = $this->calculateMorningOvertime($attendanceRecord, $schedule);
                $eveningOvertime = $this->calculateEveningOvertime($attendanceRecord, $schedule);
                $overtime = $this->calculateOvertime($attendanceRecord, $schedule);
                $lateMinutes = $this->calculateLateMinutes($attendanceRecord, $schedule);
                
                // Format times
                $scheduleInOut = $this->formatScheduleTime($schedule);
                $actualInOut = $this->formatActualTime($attendanceRecord);
                $workingHours = $this->calculateWorkingHours($schedule);
                
                // Create combined status - if both are Day Off, just show Day Off
                $combinedStatus = $scheduleStatus;
                if ($scheduleStatus !== 'Day Off' || $attendanceStatus !== 'Day Off') {
                    $combinedStatus = $scheduleStatus . ' - ' . $attendanceStatus;
                }
                
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
                    'scheduled_hours' => $scheduledHours,
                    'morning_overtime' => $morningOvertime,
                    'evening_overtime' => $eveningOvertime,
                    'overtime' => $overtime,
                    'late_minutes' => $lateMinutes,
                    'schedule_status' => $scheduleStatus,
                    'attendance_status' => $attendanceStatus,
                    'combined_status' => $combinedStatus,
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
                return 'Day Off';
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
     * Rule 0: If schedule is Day Off → Day Off (regardless of attendance)
     * Rule 1: No Time In and No Time Out → Absentada
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
     * @return string Attendance status: 'Present', 'Absent', 'Error', 'Day Off'
     */
    private function getAttendanceStatus($attendanceRecord, $schedule = null)
    {
        // Rule 0: If schedule is Day Off → Day Off (regardless of attendance)
        if ($schedule && ($schedule->status === 'Day Off' || $schedule->status === 'Rest Day')) {
            return 'Day Off';
        }
        
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
     * Calculate morning overtime (time worked before scheduled start time)
     * 
     * This method calculates overtime for time worked before the scheduled start time:
     * 1. Compares actual time_in with scheduled time_in
     * 2. Returns positive hours if employee clocked in early
     * 3. Returns 0 if employee clocked in on time or late
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return float Morning overtime hours (rounded to 2 decimal places)
     */
    private function calculateMorningOvertime($attendanceRecord, $schedule)
    {
        if (
            !$attendanceRecord ||
            !$attendanceRecord->time_in || !$attendanceRecord->time_out
        ) {
            return 0;
        }

        // Get effective schedule (actual or default)
        $effectiveSchedule = $this->getEffectiveSchedule($schedule, $attendanceRecord);
        
        // If no effective schedule (Holiday/Leave), no overtime calculation
        if (!$effectiveSchedule) {
            return 0;
        }

        $timeIn = Carbon::parse($attendanceRecord->time_in);
        $scheduleIn = Carbon::parse($effectiveSchedule['time_in']);
        
        // Compare only time parts to avoid date comparison issues
        $timeInTime = $timeIn->format('H:i:s');
        $scheduleInTime = $scheduleIn->format('H:i:s');
        
        // If employee clocked in before schedule start time, calculate morning overtime
        if ($timeInTime < $scheduleInTime) {
            $scheduleStartMinutes = $scheduleIn->hour * 60 + $scheduleIn->minute;
            $actualStartMinutes = $timeIn->hour * 60 + $timeIn->minute;
            $morningOvertimeMinutes = $scheduleStartMinutes - $actualStartMinutes;
            return round($morningOvertimeMinutes / 60, 2);
        }
        
        return 0; // No morning overtime
    }

    /**
     * Calculate evening overtime (time worked after scheduled end time)
     * 
     * This method calculates overtime for time worked after the scheduled end time:
     * 1. Compares actual time_out with scheduled time_out
     * 2. Returns positive hours if employee clocked out late
     * 3. Returns 0 if employee clocked out on time or early
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return float Evening overtime hours (rounded to 2 decimal places)
     */
    private function calculateEveningOvertime($attendanceRecord, $schedule)
    {
        if (
            !$attendanceRecord ||
            !$attendanceRecord->time_in || !$attendanceRecord->time_out
        ) {
            return 0;
        }

        // Get effective schedule (actual or default)
        $effectiveSchedule = $this->getEffectiveSchedule($schedule, $attendanceRecord);
        
        // If no effective schedule (Holiday/Leave), no overtime calculation
        if (!$effectiveSchedule) {
            return 0;
        }

        $timeOut = Carbon::parse($attendanceRecord->time_out);
        $scheduleOut = Carbon::parse($effectiveSchedule['time_out']);
        
        // Compare only time parts to avoid date comparison issues
        $timeOutTime = $timeOut->format('H:i:s');
        $scheduleOutTime = $scheduleOut->format('H:i:s');
        
        // If employee clocked out after schedule end time, calculate evening overtime
        if ($timeOutTime > $scheduleOutTime) {
            $scheduleEndMinutes = $scheduleOut->hour * 60 + $scheduleOut->minute;
            $actualEndMinutes = $timeOut->hour * 60 + $timeOut->minute;
            $eveningOvertimeMinutes = $actualEndMinutes - $scheduleEndMinutes;
            return round($eveningOvertimeMinutes / 60, 2);
        }
        
        return 0; // No evening overtime
    }

    /**
     * Calculate total overtime (morning + evening overtime)
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return float Total overtime hours (rounded to 2 decimal places)
     */
    private function calculateOvertime($attendanceRecord, $schedule)
    {
        $morningOvertime = $this->calculateMorningOvertime($attendanceRecord, $schedule);
        $eveningOvertime = $this->calculateEveningOvertime($attendanceRecord, $schedule);
        
        return round($morningOvertime + $eveningOvertime, 2);
    }
    
    /**
     * Format decimal hours to readable format (e.g., 8.5 → "8 hrs 30 mins")
     * 
     * @param float $decimalHours
     * @return string Formatted hours and minutes
     */
    private function formatHoursToReadable($decimalHours)
    {
        if ($decimalHours <= 0) {
            return '0 hrs';
        }
        
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        
        // Handle minute rounding that might exceed 59
        if ($minutes >= 60) {
            $hours += 1;
            $minutes = 0;
        }
        
        $result = '';
        
        if ($hours > 0) {
            $result .= $hours . ' hr' . ($hours > 1 ? 's' : '');
        }
        
        if ($minutes > 0) {
            if ($hours > 0) {
                $result .= ' ';
            }
            $result .= $minutes . ' min' . ($minutes > 1 ? 's' : '');
        }
        
        return $result ?: '0 hrs';
    }
    
    /**
     * Calculate late minutes when employee clocks in after scheduled start time
     * 
     * This method calculates how many minutes an employee was late in arriving:
     * 1. Compares actual time_in with scheduled time_in (actual or default)
     * 2. Returns 0 if employee clocked in on time or early
     * 3. Returns positive minutes if employee clocked in late
     * 4. Returns 0 for Holiday/Leave status (no late calculation)
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return int Late minutes (0 if not late)
     */
    private function calculateLateMinutes($attendanceRecord, $schedule)
    {
        if (
            !$attendanceRecord ||
            !$attendanceRecord->time_in
        ) {
            return 0;
        }

        // Get effective schedule (actual or default)
        $effectiveSchedule = $this->getEffectiveSchedule($schedule, $attendanceRecord);
        
        // If no effective schedule (Holiday/Leave), no late calculation
        if (!$effectiveSchedule) {
            return 0;
        }

        $timeIn = Carbon::parse($attendanceRecord->time_in);
        $scheduleIn = Carbon::parse($effectiveSchedule['time_in']);
        
        // Compare only time parts to avoid date comparison issues
        $timeInTime = $timeIn->format('H:i:s');
        $scheduleInTime = $scheduleIn->format('H:i:s');
        
        // If employee clocked in after schedule start time, calculate late minutes
        if ($timeInTime > $scheduleInTime) {
            // Calculate minutes between schedule start and actual start time
            $scheduleStartMinutes = $scheduleIn->hour * 60 + $scheduleIn->minute;
            $actualStartMinutes = $timeIn->hour * 60 + $timeIn->minute;
            $lateMinutes = $actualStartMinutes - $scheduleStartMinutes;
            return $lateMinutes;
        }
        
        return 0; // Not late
    }
    
    /**
     * Get effective schedule (actual schedule or default schedule)
     * 
     * This method checks the schedule status first:
     * - If schedule exists and is "Working" with complete time data, use it
     * - If schedule status is "Holiday", "Leave", or "Day Off", don't use default schedule
     * - If no schedule (null) or incomplete schedule for "Working" status, use default
     * 
     * SPECIAL CASE: For holidays, if employee has attendance (time_in/time_out), 
     * use default schedule for calculations (holiday work)
     * 
     * @param EmployeeSchedule|null $schedule
     * @param AttendanceRecord|null $attendanceRecord Optional attendance record for holiday work detection
     * @return array|null Array with 'time_in' and 'time_out' keys, or null if no schedule should be applied
     */
    private function getEffectiveSchedule($schedule, $attendanceRecord = null)
    {
        // If no schedule at all (null), use default schedule
        if (!$schedule) {
            return [
                'time_in' => '09:00:00',
                'time_out' => '18:00:00'
            ];
        }
        
        // Get schedule status for existing schedule
        $scheduleStatus = $this->getScheduleStatus($schedule);
        
        // SPECIAL CASE: For holidays, if employee worked (has attendance), use default schedule
        if ($scheduleStatus === 'Holiday' && $attendanceRecord && 
            $attendanceRecord->time_in && $attendanceRecord->time_out) {
            return [
                'time_in' => '09:00:00',
                'time_out' => '18:00:00'
            ];
        }
        
        // If schedule status is Holiday, Leave, or Day Off, don't apply any schedule
        if ($scheduleStatus === 'Holiday' || $scheduleStatus === 'Leave' || $scheduleStatus === 'Day Off') {
            return null;
        }
        
        // If schedule exists and has time_in and time_out, use it
        if ($schedule->time_in && $schedule->time_out) {
            return [
                'time_in' => $schedule->time_in,
                'time_out' => $schedule->time_out
            ];
        }
        
        // Default schedule: 9:00 AM to 6:00 PM (for Working status with missing times)
        return [
            'time_in' => '09:00:00',
            'time_out' => '18:00:00'
        ];
    }
    
    /**
     * Format schedule time
     */
    private function formatScheduleTime($schedule)
    {
        $scheduleStatus = $this->getScheduleStatus($schedule);
        
        // For holidays, show default schedule with holiday indicator
        if ($scheduleStatus === 'Holiday') {
            return '09:00–18:00'; // Default schedule for holidays
        }
        
        $effectiveSchedule = $this->getEffectiveSchedule($schedule);
        
        // If no effective schedule (Leave/Day Off), return appropriate display
        if (!$effectiveSchedule) {
            return $scheduleStatus; // Return "Leave" or "Day Off"
        }
        
        $timeIn = Carbon::parse($effectiveSchedule['time_in'])->format('H:i');
        $timeOut = Carbon::parse($effectiveSchedule['time_out'])->format('H:i');
        
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
        $scheduleStatus = $this->getScheduleStatus($schedule);
        
        // For holidays, show default working hours
        if ($scheduleStatus === 'Holiday') {
            return '8 hrs'; // Default working hours for holidays
        }
        
            $effectiveSchedule = $this->getEffectiveSchedule($schedule, null);
        
        // If no effective schedule (Leave/Day Off), return appropriate display
        if (!$effectiveSchedule) {
            return $scheduleStatus; // Return "Leave" or "Day Off"
        }
        
        $timeIn = Carbon::parse($effectiveSchedule['time_in']);
        $timeOut = Carbon::parse($effectiveSchedule['time_out']);
        
        $totalHours = round($timeIn->diffInMinutes($timeOut) / 60, 2);
        $workingHours = max(0, $totalHours - 1); // Subtract 1 hour for lunch break
        
        return $this->formatHoursToReadable($workingHours);
    }
    
    /**
     * Calculate worked hours from actual attendance with lunch break deduction
     * 
     * This method calculates the actual hours worked by an employee:
     * 1. Validates that both time_in and time_out exist
     * 2. Checks if time_in is later than schedule_out (invalid attendance)
     * 3. Checks if both times are completely outside scheduled range
     * 4. INCLUDES early time-in minutes in total worked hours
     * 5. Calculates total duration and subtracts 1 hour for lunch break
     * 6. Ensures non-negative result using max(0, ...)
     * 
     * Key Features:
     * - Uses time-only comparison to avoid datetime issues
     * - Automatically deducts 1 hour for lunch break
     * - INCLUDES early time-in minutes in worked hours calculation
     * - Uses effective schedule (actual or default 9:00-18:00)
     * - Returns "—" for Holiday/Leave status (no worked hours calculation)
     * - Returns "0 hrs" for invalid attendance
     * - Returns "—" for missing time data
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return string Worked hours formatted as "X hrs Y mins" or "0 hrs" or "—"
     */
    private function calculateWorkedHours($attendanceRecord, $schedule = null)
    {
        if (!$attendanceRecord || !$attendanceRecord->time_in || !$attendanceRecord->time_out) {
            return '—';
        }
        
        // Get effective schedule (actual or default)
        $effectiveSchedule = $this->getEffectiveSchedule($schedule, $attendanceRecord);
        
        // If no effective schedule (Holiday/Leave), return appropriate display
        if (!$effectiveSchedule) {
            $scheduleStatus = $this->getScheduleStatus($schedule);
            return $scheduleStatus; // Return "Holiday" or "Leave"
        }
        
        $timeIn = Carbon::parse($attendanceRecord->time_in);
        $timeOut = Carbon::parse($attendanceRecord->time_out);
        
        $scheduleIn = Carbon::parse($effectiveSchedule['time_in']);
        $scheduleOut = Carbon::parse($effectiveSchedule['time_out']);
        
        // Do not calculate worked hours if time_in > schedule_out (compare only time parts)
            if ($timeIn->format('H:i:s') > $scheduleOut->format('H:i:s')) {
                return '0 hrs'; // Invalid attendance - no worked hours
        }
        
        // Only return 0 hrs if both times are completely outside schedule range (compare only time parts)
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
        
        // NEW RULE: Include early time-in minutes in total worked hours
        // Calculate total worked hours using actual time_in and actual time_out
        // Subtract 1 hour for lunch break
        $totalHours = round($timeIn->diffInMinutes($timeOut) / 60, 2);
        $workedHours = max(0, $totalHours - 1); // Ensure non-negative result
        
        return $this->formatHoursToReadable($workedHours);
    }
    
    /**
     * Calculate scheduled hours (hours worked within the scheduled timeframe)
     * 
     * This method calculates the hours worked by an employee within their scheduled
     * timeframe (between schedule_in and schedule_out), excluding overtime:
     * 1. Validates that both time_in and time_out exist
     * 2. Gets effective schedule (actual or default)
     * 3. Calculates overlap between actual work time and scheduled time
     * 4. Returns hours worked within schedule, excluding overtime periods
     * 
     * Key Features:
     * - Uses time-only comparison to avoid datetime issues
     * - Calculates overlap between actual and scheduled timeframes
     * - Excludes overtime periods (before schedule_in or after schedule_out)
     * - Uses effective schedule (actual or default 9:00-18:00)
     * - Returns "—" for Holiday/Leave status (no scheduled hours calculation)
     * - Returns "0 hrs" for invalid attendance or no overlap
     * - Returns "—" for missing time data
     * 
     * @param AttendanceRecord|null $attendanceRecord Employee's attendance record
     * @param EmployeeSchedule|null $schedule Employee's schedule for the day
     * @return string Scheduled hours formatted as "X hrs Y mins" or "0 hrs" or "—"
     */
    private function calculateScheduledHours($attendanceRecord, $schedule = null)
    {
        if (!$attendanceRecord || !$attendanceRecord->time_in || !$attendanceRecord->time_out) {
            return '—';
        }
        
        // Get effective schedule (actual or default)
        $effectiveSchedule = $this->getEffectiveSchedule($schedule, $attendanceRecord);
        
        // If no effective schedule (Holiday/Leave), return appropriate display
        if (!$effectiveSchedule) {
            $scheduleStatus = $this->getScheduleStatus($schedule);
            return $scheduleStatus; // Return "Holiday" or "Leave"
        }
        
        $timeIn = Carbon::parse($attendanceRecord->time_in);
        $timeOut = Carbon::parse($attendanceRecord->time_out);
        
        $scheduleIn = Carbon::parse($effectiveSchedule['time_in']);
        $scheduleOut = Carbon::parse($effectiveSchedule['time_out']);
        
        // Calculate the overlap between actual work time and scheduled time
        $actualStartMinutes = $timeIn->hour * 60 + $timeIn->minute;
        $actualEndMinutes = $timeOut->hour * 60 + $timeOut->minute;
        $scheduleStartMinutes = $scheduleIn->hour * 60 + $scheduleIn->minute;
        $scheduleEndMinutes = $scheduleOut->hour * 60 + $scheduleOut->minute;
        
        // Find the overlap period
        $overlapStartMinutes = max($actualStartMinutes, $scheduleStartMinutes);
        $overlapEndMinutes = min($actualEndMinutes, $scheduleEndMinutes);
        
        // If no overlap, return 0 hrs
        if ($overlapStartMinutes >= $overlapEndMinutes) {
            return '0 hrs';
        }
        
        // Calculate scheduled hours (overlap duration)
        $scheduledMinutes = $overlapEndMinutes - $overlapStartMinutes;
        $scheduledHours = round($scheduledMinutes / 60, 2);
        
        // Subtract 1 hour for lunch break (same as worked hours)
        $scheduledHours = max(0, $scheduledHours - 1);
        
        return $this->formatHoursToReadable($scheduledHours);
    }
    
    /**
     * Convert formatted hours string back to decimal hours for calculations
     * 
     * @param string $formattedHours Formatted hours like "8 hrs 30 mins", "8 hrs", "30 mins", "—", "Holiday"
     * @return float Decimal hours
     */
    private function parseFormattedHours($formattedHours)
    {
        // Handle special cases
        if ($formattedHours === '—' || $formattedHours === 'Holiday' || $formattedHours === 'Leave' || $formattedHours === 'Day Off') {
            return 0;
        }
        
        // Parse "X hrs Y mins" format
        if (preg_match('/(\d+)\s*hrs?\s*(\d+)\s*mins?/', $formattedHours, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];
            return $hours + ($minutes / 60);
        }
        
        // Parse "X hrs" format (no minutes)
        if (preg_match('/(\d+)\s*hrs?/', $formattedHours, $matches)) {
            return (int)$matches[1];
        }
        
        // Parse "X mins" format (no hours)
        if (preg_match('/(\d+)\s*mins?/', $formattedHours, $matches)) {
            return (int)$matches[1] / 60;
        }
        
        return 0;
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
            'day_off_days' => collect($comprehensiveData)->where('attendance_status', 'Day Off')->count(),
            'total_worked_hours' => collect($comprehensiveData)->map(function($record) {
                return $this->parseFormattedHours($record['worked_hours']);
            })->sum(),
            'total_scheduled_hours' => collect($comprehensiveData)->map(function($record) {
                return $this->parseFormattedHours($record['scheduled_hours']);
            })->sum(),
            'total_morning_overtime_hours' => collect($comprehensiveData)->sum('morning_overtime'),
            'total_evening_overtime_hours' => collect($comprehensiveData)->sum('evening_overtime'),
            'total_overtime_hours' => collect($comprehensiveData)->sum('overtime'),
            'total_late_minutes' => collect($comprehensiveData)->sum('late_minutes'),
            'late_instances' => collect($comprehensiveData)->where('late_minutes', '>', 0)->count(),
            'working_days' => collect($comprehensiveData)->where('schedule_status', 'Working')->count(),
            'holiday_days' => collect($comprehensiveData)->where('schedule_status', 'Holiday')->count(),
            'rest_days' => collect($comprehensiveData)->where('schedule_status', 'Day Off')->count(),
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


