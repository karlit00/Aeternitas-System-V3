<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollGenerationService
{
    /**
     * Generate payroll preview from comprehensive attendance data (without saving to database)
     *
     * @param array $periodData Period data from session
     * @param array $comprehensiveData Comprehensive attendance data from period management
     * @param array|null $employeeIds Optional array of employee IDs to process
     * @return array Preview payroll data
     */
    public function generatePayrollPreview(array $periodData, array $comprehensiveData, ?array $employeeIds = null): array
    {
        $generatedPayrolls = [];
        
        // Group comprehensive data by employee
        $groupedData = collect($comprehensiveData)->groupBy('employee_id');
        
        foreach ($groupedData as $employeeId => $employeeRecords) {
            $employee = Employee::find($employeeId);
            if ($employee) {
                $payrollData = $this->calculatePayrollPreviewFromRecords($employee, $employeeRecords, $periodData);
                if ($payrollData) {
                    $generatedPayrolls[] = $payrollData;
                }
            }
        }
        
        return $generatedPayrolls;
    }

    /**
     * Generate payroll for a specific period using comprehensive attendance data
     *
     * @param array $periodData Period data from session
     * @param array $comprehensiveData Comprehensive attendance data from period management
     * @param array|null $employeeIds Optional array of employee IDs to process
     * @return array Generated payroll records
     */
    public function generatePayrollFromComprehensiveData(array $periodData, array $comprehensiveData, ?array $employeeIds = null): array
    {
        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($periodData['start_date']);
            $endDate = Carbon::parse($periodData['end_date']);
            
            // Group comprehensive data by employee
            $groupedData = collect($comprehensiveData)->groupBy('employee_id');
            
            // Filter by employee IDs if specified
            if (!empty($employeeIds)) {
                $groupedData = $groupedData->filter(function ($records, $employeeId) use ($employeeIds) {
                    return in_array($employeeId, $employeeIds);
                });
            }
            
            $generatedPayrolls = [];
            
            foreach ($groupedData as $employeeId => $employeeRecords) {
                $employee = Employee::find($employeeId);
                if ($employee) {
                    $payroll = $this->calculatePayrollFromRecords($employee, $employeeRecords, $periodData);
                    if ($payroll) {
                        $generatedPayrolls[] = $payroll;
                    }
                }
            }
            
            DB::commit();
            
            return $generatedPayrolls;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll generation from comprehensive data failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate payroll preview from comprehensive records (without saving to database)
     *
     * @param Employee $employee
     * @param \Illuminate\Support\Collection $employeeRecords
     * @param array $periodData
     * @return array|null
     */
    private function calculatePayrollPreviewFromRecords(Employee $employee, $employeeRecords, array $periodData): ?array
    {
        $startDate = Carbon::parse($periodData['start_date']);
        $endDate = Carbon::parse($periodData['end_date']);
        
        // Calculate payroll components
        $basicSalaryData = $this->calculateBasicSalaryFromRecords($employee, $employeeRecords);
        $overtimeData = $this->calculateOvertimeFromRecords($employee, $employeeRecords);
        $nightDifferentialData = $this->calculateNightDifferentialFromRecords($employee, $employeeRecords);
        $holidayData = $this->calculateHolidayPayFromRecords($employee, $employeeRecords);
        $bonuses = $this->calculateBonusesFromRecords($employee, $employeeRecords);
        $deductions = $this->calculateDeductionsFromRecords($employee, $employeeRecords);
        
        // Calculate totals
        // Gross pay should NOT include deductions - deductions are subtracted from gross pay to get net pay
        // FIXED: Basic salary now uses full scheduled hours, late deductions applied separately
        $grossPay = $basicSalaryData['amount'] + $holidayData['holiday_premium'] + $holidayData['special_holiday_premium'] + $overtimeData['overtime_pay'] + $nightDifferentialData['night_differential_pay'] + $bonuses;
        $taxAmount = $this->calculateTax($grossPay);
        $netPay = $grossPay - $deductions - $taxAmount;

        // Calculate late minutes details for preview
        $lateMinutesDetails = $this->calculateLateMinutesDetails($employee, $employeeRecords);
        
        // Return preview data array (not saved to database)
        return [
            'employee_id' => $employee->id,
            'pay_period_start' => $startDate->format('Y-m-d'),
            'pay_period_end' => $endDate->format('Y-m-d'),
            'basic_salary' => $basicSalaryData['amount'],
            'basic_salary_details' => $basicSalaryData,
            'holiday_basic_pay' => $holidayData['holiday_basic_pay'],
            'holiday_premium' => $holidayData['holiday_premium'],
            'special_holiday_premium' => $holidayData['special_holiday_premium'],
            'regular_holiday_days' => $holidayData['regular_holiday_days'],
            'special_holiday_days' => $holidayData['special_holiday_days'],
            'overtime_hours' => $overtimeData['overtime_hours'],
            'overtime_rate' => $overtimeData['overtime_rate'],
            'night_differential_hours' => $nightDifferentialData['night_differential_hours'],
            'night_differential_rate' => $nightDifferentialData['night_differential_rate'],
            'night_differential_pay' => $nightDifferentialData['night_differential_pay'],
            'bonuses' => $bonuses,
            'deductions' => $deductions,
            'deductions_details' => $lateMinutesDetails,
            'tax_amount' => $taxAmount,
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'status' => 'preview',
        ];
    }

    /**
     * Calculate payroll from comprehensive attendance records
     *
     * @param Employee $employee
     * @param \Illuminate\Support\Collection $employeeRecords
     * @param array $periodData
     * @return Payroll|null
     */
    private function calculatePayrollFromRecords(Employee $employee, $employeeRecords, array $periodData): ?Payroll
    {
        $startDate = Carbon::parse($periodData['start_date']);
        $endDate = Carbon::parse($periodData['end_date']);
        
        // Check if payroll already exists for this period
        $existingPayroll = Payroll::where('employee_id', $employee->id)
            ->where('pay_period_start', $startDate->format('Y-m-d'))
            ->where('pay_period_end', $endDate->format('Y-m-d'))
            ->first();
            
        if ($existingPayroll) {
            Log::info("Payroll already exists for employee {$employee->employee_id} for period {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
            return null;
        }

        // Calculate payroll components
        $basicSalaryData = $this->calculateBasicSalaryFromRecords($employee, $employeeRecords);
        $overtimeData = $this->calculateOvertimeFromRecords($employee, $employeeRecords);
        $nightDifferentialData = $this->calculateNightDifferentialFromRecords($employee, $employeeRecords);
        $holidayData = $this->calculateHolidayPayFromRecords($employee, $employeeRecords);
        $bonuses = $this->calculateBonusesFromRecords($employee, $employeeRecords);
        $deductions = $this->calculateDeductionsFromRecords($employee, $employeeRecords);
        
        // Calculate totals
        // Gross pay should NOT include deductions - deductions are subtracted from gross pay to get net pay
        // FIXED: Basic salary now uses full scheduled hours, late deductions applied separately
        $grossPay = $basicSalaryData['amount'] + $holidayData['holiday_premium'] + $holidayData['special_holiday_premium'] + $overtimeData['overtime_pay'] + $nightDifferentialData['night_differential_pay'] + $bonuses;
        $taxAmount = $this->calculateTax($grossPay);
        $netPay = $grossPay - $deductions - $taxAmount;

        // Create payroll record
        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'pay_period_start' => $startDate->format('Y-m-d'),
            'pay_period_end' => $endDate->format('Y-m-d'),
            'basic_salary' => $basicSalaryData['amount'],
            'holiday_basic_pay' => $holidayData['holiday_basic_pay'],
            'holiday_premium' => $holidayData['holiday_premium'],
            'special_holiday_premium' => $holidayData['special_holiday_premium'],
            'regular_holiday_days' => $holidayData['regular_holiday_days'],
            'special_holiday_days' => $holidayData['special_holiday_days'],
            'overtime_hours' => $overtimeData['overtime_hours'],
            'overtime_rate' => $overtimeData['overtime_rate'],
            'scheduled_hours' => $basicSalaryData['total_scheduled_hours'],
            'bonuses' => $bonuses,
            'deductions' => $deductions,
            'tax_amount' => $taxAmount,
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'status' => 'pending',
        ]);

        Log::info("Generated payroll for employee {$employee->employee_id}: Basic: {$basicSalaryData['amount']}, Holiday Basic: {$holidayData['holiday_basic_pay']}, Holiday Premium: {$holidayData['holiday_premium']}, Overtime: {$overtimeData['overtime_pay']}, Net: {$netPay}");
        
        return $payroll;
    }

    /**
     * Calculate basic salary from comprehensive records based on actual scheduled hours worked
     */
    private function calculateBasicSalaryFromRecords(Employee $employee, $employeeRecords): array
    {
        $totalScheduledHours = 0;
        $scheduledHoursDetails = [];
        
        // Calculate total scheduled hours for records where employee actually worked
        // Include all work (regular days + holidays) for basic salary calculation
        $workingRecords = $employeeRecords->filter(function($record) {
            // Exclude if schedule status is 'Leave' - even if they have time_in, they shouldn't be paid
            if ($record['schedule_status'] === 'Leave') {
                return false;
            }
            
            // Include if it's a working day with present attendance
            if ($record['schedule_status'] === 'Working' && $record['attendance_status'] === 'Present') {
                return true;
            }
            
            // Include holidays with actual scheduled hours worked
            if (($record['schedule_status'] === 'Regular Holiday' || $record['schedule_status'] === 'Special Holiday') && 
                $this->parseFormattedHours($record['scheduled_hours']) > 0) {
                return true;
            }
            
            // Also include if there are actual scheduled hours worked (for other statuses)
            $scheduledHours = $this->parseFormattedHours($record['scheduled_hours']);
            return $scheduledHours > 0;
        });
        
        foreach ($workingRecords as $record) {
            // FIXED: Use full scheduled hours (8 hours for full day) instead of actual worked hours
            // This prevents double deduction - late time will be handled by late deductions
            $fullScheduledHours = 8; // Default to 8 hours for full working day
            
            // For holidays, use the actual scheduled hours if available
            if (($record['schedule_status'] === 'Regular Holiday' || $record['schedule_status'] === 'Special Holiday')) {
                $holidayHours = $this->parseFormattedHours($record['scheduled_hours']);
                if ($holidayHours > 0) {
                    $fullScheduledHours = $holidayHours;
                }
            }
            
            $totalScheduledHours += $fullScheduledHours;
            
            // Store details for display
            $scheduledHoursDetails[] = [
                'date' => $record['date_formatted'],
                'hours' => $fullScheduledHours . ' hrs', // Show full scheduled hours
                'decimal_hours' => $fullScheduledHours
            ];
        }
        
        // Calculate basic salary based on daily rate and FULL scheduled hours
        // Formula: Basic Salary = Daily Rate × (Full Scheduled Hours / 8)
        // Late deductions will be applied separately to avoid double penalty
        $dailyRate = $employee->daily_rate;
        $basicSalary = $dailyRate * ($totalScheduledHours / 8);
        
        // Calculate hourly rate for reference
        $hourlyRate = $dailyRate / 8;
        
        return [
            'amount' => round($basicSalary, 2),
            'total_scheduled_hours' => $totalScheduledHours,
            'scheduled_hours_details' => $scheduledHoursDetails,
            'daily_rate' => $dailyRate,
            'hourly_rate' => $hourlyRate
        ];
    }

    /**
     * Calculate overtime from comprehensive records
     */
    private function calculateOvertimeFromRecords(Employee $employee, $employeeRecords): array
    {
        $totalOvertimeHours = $employeeRecords->sum('overtime');
        $overtimePay = $totalOvertimeHours * $employee->overtime_rate;

        return [
            'overtime_hours' => $totalOvertimeHours,
            'overtime_rate' => $employee->overtime_rate,
            'overtime_pay' => round($overtimePay, 2)
        ];
    }

    /**
     * Calculate night differential from comprehensive records
     * Formula: hourly rate × 0.1 × night hours
     */
    private function calculateNightDifferentialFromRecords(Employee $employee, $employeeRecords): array
    {
        $nightShiftHours = $employeeRecords->sum('night_differential_hours');
        $hourlyRate = $employee->daily_rate / 8; // Daily rate / 8 hours
        $nightDifferentialRate = $hourlyRate * 0.1; // 10% of hourly rate
        $nightDifferentialPay = $nightShiftHours * $nightDifferentialRate;

        return [
            'night_differential_hours' => $nightShiftHours,
            'night_differential_rate' => round($nightDifferentialRate, 2),
            'night_differential_pay' => round($nightDifferentialPay, 2)
        ];
    }

    /**
     * Calculate holiday pay from comprehensive records based on actual hours worked
     */
    private function calculateHolidayPayFromRecords(Employee $employee, $employeeRecords): array
    {
        $regularHolidayDays = 0;
        $specialHolidayDays = 0;
        $regularHolidayHours = 0;
        $specialHolidayHours = 0;
        
        // Calculate actual hours worked on holidays
        foreach ($employeeRecords as $record) {
            if ($record['schedule_status'] === 'Regular Holiday') {
                $regularHolidayDays++;
                $regularHolidayHours += $this->parseFormattedHours($record['scheduled_hours']);
            } elseif ($record['schedule_status'] === 'Special Holiday') {
                $specialHolidayDays++;
                $specialHolidayHours += $this->parseFormattedHours($record['scheduled_hours']);
            }
        }
        
        // Calculate hourly rate
        $hourlyRate = $employee->daily_rate / 8; // Daily rate / 8 hours
        
        // Calculate holiday basic pay based on actual hours worked
        $regularHolidayBasicPay = $regularHolidayHours * $hourlyRate;
        $specialHolidayBasicPay = $specialHolidayHours * $hourlyRate;
        
        // Calculate holiday premium based on actual hours worked
        // Regular holiday: 100% premium on hours worked (double pay)
        $regularHolidayPremium = $regularHolidayHours * $hourlyRate; // 100% premium = double pay
        
        // Special holiday: 30% premium on hours worked
        $specialHolidayPremium = $specialHolidayHours * $hourlyRate * 0.3; // 30% premium
        
        // Total holiday basic pay
        $holidayBasicPay = $regularHolidayBasicPay + $specialHolidayBasicPay;
        
        // Total holiday pay = basic pay + premium
        $totalHolidayPay = $holidayBasicPay + $regularHolidayPremium + $specialHolidayPremium;
        
        return [
            'regular_holiday_days' => $regularHolidayDays,
            'special_holiday_days' => $specialHolidayDays,
            'holiday_basic_pay' => round($holidayBasicPay, 2),
            'holiday_premium' => round($regularHolidayPremium, 2),
            'special_holiday_premium' => round($specialHolidayPremium, 2),
            'holiday_pay' => round($totalHolidayPay, 2)
        ];
    }

    /**
     * Calculate bonuses from comprehensive records
     */
    private function calculateBonusesFromRecords(Employee $employee, $employeeRecords): float
    {
        $bonuses = 0;
        
        // Perfect attendance bonus
        $totalWorkingDays = $employeeRecords->where('schedule_status', 'Working')->count();
        $presentDays = $employeeRecords->where('attendance_status', 'Present')->count();
        
        if ($totalWorkingDays > 0 && $presentDays == $totalWorkingDays) {
            $bonuses += 500; // Perfect attendance bonus
        }
        
        // Performance bonus (example: based on overtime hours)
        $totalOvertimeHours = $employeeRecords->sum('overtime');
        if ($totalOvertimeHours > 20) {
            $bonuses += 300; // Overtime performance bonus
        }
        
        return $bonuses;
    }

    /**
     * Calculate deductions from comprehensive records
     */
    private function calculateDeductionsFromRecords(Employee $employee, $employeeRecords): float
    {
        $deductions = 0;
        
        // Late deductions - based on basic salary per minute
        $totalLateMinutes = $employeeRecords->sum('late_minutes');
        if ($totalLateMinutes > 0) {
            // Calculate rate per minute: Daily rate / (8 hours * 60 minutes)
            $minutesPerDay = 8 * 60; // 480 minutes per day
            $ratePerMinute = $employee->daily_rate / $minutesPerDay;
            $deductions += $totalLateMinutes * $ratePerMinute;
        }
        
        // Error deductions (incomplete time records)
        $errorDays = $employeeRecords->where('attendance_status', 'Error')->count();
        $deductions += $errorDays * $employee->daily_rate; // Full day deduction for error
        
        return $deductions;
    }

    /**
     * Calculate late minutes details for preview display
     */
    private function calculateLateMinutesDetails(Employee $employee, $employeeRecords): array
    {
        $totalLateMinutes = $employeeRecords->sum('late_minutes');
        $lateDays = $employeeRecords->where('late_minutes', '>', 0);
        
        $lateDetails = [];
        $totalLateDeduction = 0;
        
        if ($totalLateMinutes > 0) {
            // Calculate rate per minute: Daily rate / (8 hours * 60 minutes)
            $minutesPerDay = 8 * 60; // 480 minutes per day
            $ratePerMinute = $employee->daily_rate / $minutesPerDay;
            
            foreach ($lateDays as $record) {
                if ($record['late_minutes'] > 0) {
                    $lateDeduction = $record['late_minutes'] * $ratePerMinute;
                    $totalLateDeduction += $lateDeduction;
                    
                    $lateDetails[] = [
                        'date' => $record['date_formatted'],
                        'late_minutes' => $record['late_minutes'],
                        'deduction_amount' => round($lateDeduction, 2),
                        'rate_per_minute' => round($ratePerMinute, 4)
                    ];
                }
            }
        }
        
        return [
            'total_late_minutes' => $totalLateMinutes,
            'total_late_deduction' => round($totalLateDeduction, 2),
            'late_days_count' => count($lateDetails),
            'late_details' => $lateDetails,
            'rate_per_minute' => $totalLateMinutes > 0 ? round($employee->daily_rate / (8 * 60), 4) : 0
        ];
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
        if ($formattedHours === '—' || $formattedHours === 'Regular Holiday' || $formattedHours === 'Special Holiday' || $formattedHours === 'Leave' || $formattedHours === 'Day Off') {
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
     * Calculate tax amount using TaxCalculationService
     *
     * @param float $grossPay
     * @return float
     */
    private function calculateTax(float $grossPay): float
    {
        $taxService = app(\App\Services\TaxCalculationService::class);
        return $taxService->calculateTax($grossPay);
    }

    /**
     * Get payroll summary for a period
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getPayrollSummary(Carbon $startDate, Carbon $endDate): array
    {
        $payrolls = Payroll::where('pay_period_start', $startDate->format('Y-m-d'))
            ->where('pay_period_end', $endDate->format('Y-m-d'))
            ->get();

        return [
            'total_employees' => $payrolls->count(),
            'total_gross_pay' => $payrolls->sum('gross_pay'),
            'total_overtime_hours' => $payrolls->sum('overtime_hours'),
            'total_deductions' => $payrolls->sum('deductions'),
            'total_net_pay' => $payrolls->sum('net_pay'),
        ];
    }
}
