<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceLog;
use App\Models\AttendanceSetting;
use App\Models\AttendanceException;
use App\Models\EmployeeBreak;
use App\Models\TimeEntry;
use App\Helpers\TimezoneHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TimeInOutController extends Controller
{
    /**
     * Display time in/out page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only employees can access time in/out
        if ($user->role !== 'employee') {
            return redirect()->route('dashboard')->with('error', 'Time In/Out is only available for employees.');
        }
        
        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }
        
        if (!$employee) {
            $message = $user->employee_id 
                ? 'Employee record not found. Please contact administrator to link your account to an employee record.'
                : 'Your account is not linked to an employee record. Please contact administrator.';
            return redirect()->route('dashboard')->with('error', $message);
        }

        // Get today's attendance record
        $todayAttendance = $employee->getTodayAttendance();
        
        // Load time entries if exists
        if ($todayAttendance) {
            $todayAttendance->load('timeEntries', 'breaks');
        }
        
        // Get recent activity (last 5 days)
        $recentActivity = $employee->attendanceRecords()
            ->with('timeEntries')
            ->where('date', '>=', today()->subDays(5))
            ->orderBy('date', 'desc')
            ->get();

        return view('attendance.time-in-out', compact('user', 'todayAttendance', 'recentActivity'));
    }

    /**
     * Handle time in
     */
    public function timeIn(Request $request)
    {
        $user = Auth::user();
        
        // Only employees can time in
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'Time In is only available for employees.'], 403);
        }
        
        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }
        
        if (!$employee) {
            $message = $user->employee_id 
                ? 'Employee record not found. Please contact administrator to link your account to an employee record.'
                : 'Your account is not linked to an employee record. Please contact administrator.';
            return response()->json(['error' => $message], 404);
        }

        $today = today();
        
        // Check if there's an active time entry (already clocked in but not out)
        $existingRecord = $employee->getTodayAttendance();
        if ($existingRecord) {
            $existingRecord->load('timeEntries');
            $activeEntry = $existingRecord->getActiveTimeEntry();
            if ($activeEntry) {
                \Log::info('Time in blocked - active entry exists', [
                    'employee_id' => $employee->id,
                    'active_entry_id' => $activeEntry->id,
                    'active_entry_time_in' => $activeEntry->time_in
                ]);
                return response()->json(['error' => 'You are already clocked in. Please clock out first before clocking in again.'], 400);
            }
        }

        // Check if it's a working day
     //   if (!$this->isWorkingDay($today)) {
    //        return response()->json(['error' => 'Today is not a working day.'], 400);
     //   }

        $currentTime = TimezoneHelper::now();

        try {
            // Create or get today's attendance record
            $attendanceRecord = AttendanceRecord::firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $today,
                ],
                [
                    'status' => 'present',
                ]
            );

            // Create a new time entry
            $timeEntry = TimeEntry::create([
                'attendance_record_id' => $attendanceRecord->id,
                'time_in' => $currentTime,
                'entry_type' => 'regular',
            ]);

            // Update the attendance record's time_in if this is the first entry of the day
            $isFirstEntry = $attendanceRecord->timeEntries()->count() === 1;
            if ($isFirstEntry) {
                $attendanceRecord->update([
                    'time_in' => $currentTime,
                    'status' => 'present',
                ]);
            }

            // Log the action
            $this->logAttendanceAction($attendanceRecord, 'time_in', null, [
                'time_in' => $currentTime->toDateTimeString(),
                'time_entry_id' => $timeEntry->id,
                'entry_number' => $attendanceRecord->timeEntries()->count(),
            ], 'Employee timed in');

            // Calculate if late (only for first entry)
            $isLate = false;
            if ($isFirstEntry) {
                $isLate = $this->checkIfLate($employee, $today, $currentTime);
                if ($isLate) {
                    $attendanceRecord->update(['status' => 'late']);
                }
            }

            // Get all time entries for today
            $attendanceRecord->load('timeEntries');
            $entryCount = $attendanceRecord->timeEntries->count();

            return response()->json([
                'success' => true,
                'message' => $isLate 
                    ? 'Time in recorded (Late arrival)' 
                    : ($isFirstEntry ? 'Time in recorded successfully' : "Time in #{$entryCount} recorded"),
                'time_in' => $currentTime->format('H:i:s'),
                'is_late' => $isLate,
                'is_first_entry' => $isFirstEntry,
                'entry_number' => $entryCount,
                'time_entry' => $timeEntry,
                'attendance_record' => $attendanceRecord->fresh(['timeEntries', 'breaks']),
            ]);
        } catch (\Exception $e) {
            \Log::error('Time in failed', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to record time in: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle time out
     */
    public function timeOut(Request $request)
    {
        $user = Auth::user();
        
        // Only employees can time out
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'Time Out is only available for employees.'], 403);
        }
        
        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }
        
        if (!$employee) {
            $message = $user->employee_id 
                ? 'Employee record not found. Please contact administrator to link your account to an employee record.'
                : 'Your account is not linked to an employee record. Please contact administrator.';
            return response()->json(['error' => $message], 404);
        }

        $today = today();
        
        // Get today's attendance record
        $attendanceRecord = $employee->getTodayAttendance();
        if (!$attendanceRecord) {
            return response()->json(['error' => 'You must time in first before timing out.'], 400);
        }

        // Load time entries and breaks
        $attendanceRecord->load('timeEntries', 'breaks');

        // Find active time entry (clocked in but not out)
        $activeEntry = $attendanceRecord->getActiveTimeEntry();
        if (!$activeEntry) {
            return response()->json(['error' => 'No active time entry found. You must time in first.'], 400);
        }

        $currentTime = TimezoneHelper::now();

        // Update the active time entry with time_out
        $activeEntry->update([
            'time_out' => $currentTime,
            'hours_worked' => $activeEntry->calculateHoursWorked(),
        ]);

        // Refresh time entries
        $attendanceRecord->load('timeEntries', 'breaks');

        // Calculate total hours from all completed entries
        $totalHours = $attendanceRecord->calculateTotalHoursFromEntries();
        $hoursBreakdown = $attendanceRecord->calculateRegularAndOvertimeHours();

        // Check if there are more active entries
        $hasMoreActiveEntries = $attendanceRecord->hasActiveTimeEntry();
        
        // Update attendance record - only set time_out if no more active entries
        $updateData = [
            'total_hours' => $totalHours,
            'regular_hours' => $hoursBreakdown['regular_hours'],
            'overtime_hours' => $hoursBreakdown['overtime_hours'],
            'status' => $this->calculateStatus($totalHours, $attendanceRecord->time_in),
        ];

        // Set the main time_out to the latest time_out if no active entries remain
        if (!$hasMoreActiveEntries) {
            $lastEntry = $attendanceRecord->getLastTimeEntry();
            if ($lastEntry && $lastEntry->time_out) {
                $updateData['time_out'] = $lastEntry->time_out;
            }
        }

        $attendanceRecord->update($updateData);

        // Log the action
        $this->logAttendanceAction($attendanceRecord, 'time_out', null, [
            'time_out' => $currentTime->toDateTimeString(),
            'time_entry_id' => $activeEntry->id,
            'entry_hours' => $activeEntry->hours_worked,
            'total_hours' => $totalHours,
            'regular_hours' => $hoursBreakdown['regular_hours'],
            'overtime_hours' => $hoursBreakdown['overtime_hours'],
        ], 'Employee timed out');

        // Refresh to get latest values
        $attendanceRecord->refresh();
        $attendanceRecord->load('timeEntries', 'breaks');

        $entryCount = $attendanceRecord->timeEntries->count();

        return response()->json([
            'success' => true,
            'message' => $entryCount > 1 
                ? "Time out recorded for entry #{$entryCount}" 
                : 'Time out recorded successfully',
            'time_out' => $currentTime->format('H:i:s'),
            'entry_hours' => $activeEntry->hours_worked,
            'total_hours' => $totalHours,
            'regular_hours' => $hoursBreakdown['regular_hours'],
            'overtime_hours' => $hoursBreakdown['overtime_hours'],
            'entry_count' => $entryCount,
            'has_active_entries' => $hasMoreActiveEntries,
            'time_entry' => $activeEntry,
            'attendance_record' => $attendanceRecord->fresh(['timeEntries', 'breaks']),
        ]);
    }

    /**
     * Handle break start
     */
    public function breakStart(Request $request)
    {
        $user = Auth::user();
        
        // Only employees can start breaks
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'Break actions are only available for employees.'], 403);
        }
        
        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }
        
        if (!$employee) {
            $message = $user->employee_id 
                ? 'Employee record not found. Please contact administrator to link your account to an employee record.'
                : 'Your account is not linked to an employee record. Please contact administrator.';
            return response()->json(['error' => $message], 404);
        }

        $attendanceRecord = $employee->getTodayAttendance();
        if (!$attendanceRecord || !$attendanceRecord->time_in) {
            return response()->json(['error' => 'You must time in first before starting break.'], 400);
        }

        // Load breaks relationship
        $attendanceRecord->load('breaks');

        // Check if there's an active break (one without break_end)
        $activeBreak = $attendanceRecord->breaks()->whereNull('break_end')->first();
        if ($activeBreak) {
            return response()->json(['error' => 'You have an active break. Please end your current break before starting a new one.'], 400);
        }

        // Check break count (maximum 2 breaks per day)
        $breakCount = $attendanceRecord->breaks()->count();
        if ($breakCount >= 2) {
            return response()->json(['error' => 'Maximum of 2 breaks per day allowed.'], 400);
        }

        $currentTime = TimezoneHelper::now();

        // Create new break record
        $break = EmployeeBreak::create([
            'attendance_record_id' => $attendanceRecord->id,
            'break_start' => $currentTime,
        ]);

        // Log the action
        $this->logAttendanceAction($attendanceRecord, 'break_start', null, [
            'break_id' => $break->id,
            'break_start' => $currentTime->toDateTimeString(),
        ], 'Employee started break');

        // Calculate total break time including this new break
        $totalBreakMinutes = $attendanceRecord->getTotalBreakMinutes();
        $isOverBreak = $totalBreakMinutes > 90;

        return response()->json([
            'success' => true,
            'message' => 'Break started',
            'break_start' => $currentTime->format('H:i:s'),
            'break_count' => $breakCount + 1,
            'total_break_minutes' => $totalBreakMinutes,
            'is_over_break' => $isOverBreak,
            'over_break_minutes' => $isOverBreak ? max(0, $totalBreakMinutes - 90) : 0,
        ]);
    }

    /**
     * Handle break end
     */
    public function breakEnd(Request $request)
    {
        $user = Auth::user();
        
        // Only employees can end breaks
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'Break actions are only available for employees.'], 403);
        }
        
        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }
        
        if (!$employee) {
            $message = $user->employee_id 
                ? 'Employee record not found. Please contact administrator to link your account to an employee record.'
                : 'Your account is not linked to an employee record. Please contact administrator.';
            return response()->json(['error' => $message], 404);
        }

        $attendanceRecord = $employee->getTodayAttendance();
        if (!$attendanceRecord) {
            return response()->json(['error' => 'No attendance record found.'], 400);
        }

        // Find active break (one without break_end)
        $activeBreak = $attendanceRecord->breaks()->whereNull('break_end')->first();
        if (!$activeBreak) {
            return response()->json(['error' => 'No active break found. Please start a break first.'], 400);
        }

        $currentTime = TimezoneHelper::now();

        // Update break end
        $activeBreak->update(['break_end' => $currentTime]);

        // Log the action
        $this->logAttendanceAction($attendanceRecord, 'break_end', null, [
            'break_id' => $activeBreak->id,
            'break_end' => $currentTime->toDateTimeString(),
            'break_duration_minutes' => $activeBreak->break_duration_minutes,
        ], 'Employee ended break');

        // Refresh to get updated breaks
        $attendanceRecord->refresh();
        $attendanceRecord->load('breaks');

        // Calculate total break time
        $totalBreakMinutes = $attendanceRecord->getTotalBreakMinutes();
        $isOverBreak = $totalBreakMinutes > 90;

        return response()->json([
            'success' => true,
            'message' => 'Break ended',
            'break_end' => $currentTime->format('H:i:s'),
            'break_duration_minutes' => $activeBreak->break_duration_minutes,
            'total_break_minutes' => $totalBreakMinutes,
            'is_over_break' => $isOverBreak,
            'over_break_minutes' => $isOverBreak ? max(0, $totalBreakMinutes - 90) : 0,
        ]);
    }

    /**
     * Get current attendance status
     */
    public function getStatus()
    {
        $user = Auth::user();
        
        // Only employees can get their attendance status
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'Attendance status is only available for employees.'], 403);
        }
        
        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }
        
        if (!$employee) {
            $message = $user->employee_id 
                ? 'Employee record not found. Please contact administrator to link your account to an employee record.'
                : 'Your account is not linked to an employee record. Please contact administrator.';
            return response()->json(['error' => $message], 404);
        }

        $attendanceRecord = $employee->getTodayAttendance();
        
        if (!$attendanceRecord) {
            return response()->json([
                'status' => 'not_started',
                'message' => 'Ready to clock in',
                'can_time_in' => true,
                'can_time_out' => false,
                'can_break_start' => false,
                'can_break_end' => false,
                'time_entries' => [],
            ]);
        }

        // Load breaks and time entries relationships
        $attendanceRecord->load('breaks', 'timeEntries');
        
        // Find active break (one without break_end)
        $activeBreak = $attendanceRecord->breaks()->whereNull('break_end')->first();
        $breakCount = $attendanceRecord->breaks()->count();
        $totalBreakMinutes = $attendanceRecord->getTotalBreakMinutes();
        $isOverBreak = $totalBreakMinutes > 90;

        // Check for active time entry (clocked in but not out)
        $activeTimeEntry = $attendanceRecord->getActiveTimeEntry();
        $hasActiveEntry = $activeTimeEntry !== null;

        // Multiple time entries support: can time in again if no active entry
        $canTimeIn = !$hasActiveEntry;
        $canTimeOut = $hasActiveEntry;
        $canBreakStart = $hasActiveEntry && !$activeBreak && $breakCount < 2;
        $canBreakEnd = $activeBreak !== null;

        // Get all breaks with formatted data
        $breaks = $attendanceRecord->breaks->map(function ($break) {
            return [
                'id' => $break->id,
                'break_start' => $break->break_start ? $break->break_start->toIso8601String() : null,
                'break_end' => $break->break_end ? $break->break_end->toIso8601String() : null,
                'break_duration_minutes' => $break->break_duration_minutes,
                'is_active' => $break->break_end === null,
            ];
        });

        // Get all time entries with formatted data
        $timeEntries = $attendanceRecord->timeEntries->map(function ($entry) {
            return [
                'id' => $entry->id,
                'time_in' => $entry->time_in ? $entry->time_in->toIso8601String() : null,
                'time_out' => $entry->time_out ? $entry->time_out->toIso8601String() : null,
                'hours_worked' => $entry->hours_worked,
                'duration' => $entry->duration,
                'entry_type' => $entry->entry_type,
                'is_active' => $entry->time_out === null,
            ];
        });

        // For backward compatibility, also include old break_start/break_end from active break
        $breakStart = $activeBreak ? $activeBreak->break_start->toIso8601String() : null;
        $breakEnd = null; // Only set if there's a completed break (for backward compatibility)

        return response()->json([
            'status' => $attendanceRecord->status,
            'time_in' => $attendanceRecord->time_in ? $attendanceRecord->time_in->toIso8601String() : null,
            'time_out' => $attendanceRecord->time_out ? $attendanceRecord->time_out->toIso8601String() : null,
            'break_start' => $breakStart, // Active break start for backward compatibility
            'break_end' => $breakEnd, // For backward compatibility
            'breaks' => $breaks,
            'time_entries' => $timeEntries,
            'active_time_entry' => $activeTimeEntry ? [
                'id' => $activeTimeEntry->id,
                'time_in' => $activeTimeEntry->time_in->toIso8601String(),
            ] : null,
            'entry_count' => $attendanceRecord->timeEntries->count(),
            'active_break' => $activeBreak ? [
                'id' => $activeBreak->id,
                'break_start' => $activeBreak->break_start->toIso8601String(),
            ] : null,
            'break_count' => $breakCount,
            'total_break_minutes' => $totalBreakMinutes,
            'total_break_hours' => round($totalBreakMinutes / 60, 2),
            'is_over_break' => $isOverBreak,
            'over_break_minutes' => $isOverBreak ? max(0, $totalBreakMinutes - 90) : 0,
            'total_hours' => $attendanceRecord->total_hours,
            'can_time_in' => $canTimeIn,
            'can_time_out' => $canTimeOut,
            'can_break_start' => $canBreakStart,
            'can_break_end' => $canBreakEnd,
            'attendance_record' => [
                'time_in' => $attendanceRecord->time_in ? $attendanceRecord->time_in->toIso8601String() : null,
                'time_out' => $attendanceRecord->time_out ? $attendanceRecord->time_out->toIso8601String() : null,
                'break_start' => $breakStart,
                'break_end' => $breakEnd,
                'status' => $attendanceRecord->status,
            ],
        ]);
    }

    /**
     * Check if a date is a working day
     */
    private function isWorkingDay($date)
    {
        // Check if it's a holiday
        if (AttendanceException::isHoliday($date)) {
            return false;
        }

        // Check if it's a special working day (weekend work)
        if (AttendanceException::isSpecialWorkingDay($date)) {
            return true;
        }

        // Check if it's weekend
        if ($date->isWeekend()) {
            return false;
        }

        return true;
    }

    /**
     * Check if employee is late
     */
    private function checkIfLate($employee, $date, $timeIn)
    {
        $schedule = $employee->getWorkScheduleForDate($date);
        if (!$schedule) {
            return false;
        }

        $dayOfWeek = strtolower($date->format('l'));
        $expectedStartTime = $schedule->{$dayOfWeek . '_start'};
        
        if (!$expectedStartTime) {
            return false;
        }

        $gracePeriod = AttendanceSetting::getValue('grace_period_minutes', 15);
        $expectedTime = Carbon::parse($date->format('Y-m-d') . ' ' . $expectedStartTime);
        $actualTime = $timeIn;

        return $actualTime->gt($expectedTime->addMinutes($gracePeriod));
    }

    /**
     * Calculate total hours worked
     */
    private function calculateTotalHours($timeIn, $timeOut, $breakStart = null, $breakEnd = null)
    {
        $totalMinutes = $timeOut->diffInMinutes($timeIn);
        
        // Subtract break time if exists
        if ($breakStart && $breakEnd) {
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $totalMinutes -= $breakMinutes;
        }

        return round($totalMinutes / 60, 2);
    }

    /**
     * Calculate regular and overtime hours
     */
    private function calculateRegularAndOvertimeHours($totalHours)
    {
        $regularHoursLimit = AttendanceSetting::getValue('regular_hours_limit', 8);
        $regularHours = min($totalHours, $regularHoursLimit);
        $overtimeHours = max(0, $totalHours - $regularHoursLimit);

        return [
            'regular_hours' => $regularHours,
            'overtime_hours' => $overtimeHours,
        ];
    }

    /**
     * Calculate attendance status
     */
    private function calculateStatus($totalHours, $timeIn)
    {
        if ($totalHours < 4) {
            return 'half_day';
        }

        // Check if late (this would need the schedule check)
        return 'present';
    }

    /**
     * Log attendance action
     */
    private function logAttendanceAction($attendanceRecord, $action, $oldValues, $newValues, $reason)
    {
        AttendanceLog::create([
            'attendance_record_id' => $attendanceRecord->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'performed_by' => Auth::id(),
            'performed_at' => TimezoneHelper::now(),
            'reason' => $reason,
        ]);
    }
}
