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
     * Generate payroll for a specific period
     *
     * @param array $periodData Period data from session
     * @param array|null $employeeIds Optional array of employee IDs to process
     * @return array Generated payroll records
     */
    public function generatePayrollForPeriod(array $periodData, ?array $employeeIds = null): array
    {
        try {
            DB::beginTransaction();

            $startDate = Carbon::parse($periodData['start_date']);
            $endDate = Carbon::parse($periodData['end_date']);
            
            // Get employees to process
            $employees = Employee::with('department');
            
            if (!empty($employeeIds)) {
                $employees = $employees->whereIn('id', $employeeIds);
            }
            
            // Apply department filter if specified
            if (!empty($periodData['department_id'])) {
                $employees = $employees->where('department_id', $periodData['department_id']);
            }
            
            $employees = $employees->get();
            
            $generatedPayrolls = [];
            
            foreach ($employees as $employee) {
                $payroll = $this->generateEmployeePayroll($employee, $startDate, $endDate);
                if ($payroll) {
                    $generatedPayrolls[] = $payroll;
                }
            }
            
            DB::commit();
            
            return $generatedPayrolls;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate payroll for a specific employee and period
     *
     * @param Employee $employee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Payroll|null
     */
    public function generateEmployeePayroll(Employee $employee, Carbon $startDate, Carbon $endDate): ?Payroll
    {
        // Check if payroll already exists for this period
        $existingPayroll = Payroll::where('employee_id', $employee->id)
            ->where('pay_period_start', $startDate->format('Y-m-d'))
            ->where('pay_period_end', $endDate->format('Y-m-d'))
            ->first();
            
        if ($existingPayroll) {
            Log::info("Payroll already exists for employee {$employee->employee_id} for period {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
            return null;
        }

        // Calculate attendance data
        $attendanceData = $this->calculateAttendanceData($employee, $startDate, $endDate);
        
        // Calculate payroll components using daily rate
        $basicSalary = $this->calculateBasicSalary($employee, $attendanceData);
        $overtimeData = $this->calculateOvertime($employee, $startDate, $endDate);
        $bonuses = $this->calculateBonuses($employee, $attendanceData);
        $deductions = $this->calculateDeductions($employee, $attendanceData);
        $taxAmount = $this->calculateTax($basicSalary + $overtimeData['overtime_pay'] + $bonuses);
        
        $grossPay = $basicSalary + $overtimeData['overtime_pay'] + $bonuses - $deductions;
        $netPay = $grossPay - $taxAmount;

        // Create payroll record
        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'pay_period_start' => $startDate->format('Y-m-d'),
            'pay_period_end' => $endDate->format('Y-m-d'),
            'basic_salary' => $basicSalary,
            'overtime_hours' => $overtimeData['overtime_hours'],
            'overtime_rate' => $overtimeData['overtime_rate'],
            'bonuses' => $bonuses,
            'deductions' => $deductions,
            'tax_amount' => $taxAmount,
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'status' => 'pending',
        ]);

        Log::info("Generated payroll for employee {$employee->employee_id}: Basic: {$basicSalary}, Overtime: {$overtimeData['overtime_pay']}, Net: {$netPay}");
        
        return $payroll;
    }

    /**
     * Calculate attendance data for an employee in a period
     *
     * @param Employee $employee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function calculateAttendanceData(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        $attendanceRecords = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $workingDays = 0;
        $totalHours = 0;
        $overtimeHours = 0;

        foreach ($attendanceRecords as $record) {
            if ($record->status === 'present' || $record->status === 'late') {
                $workingDays++;
                $totalHours += $record->total_hours ?? 0;
                
                // Calculate overtime (hours beyond 8 per day)
                if (($record->total_hours ?? 0) > 8) {
                    $overtimeHours += ($record->total_hours - 8);
                }
            }
        }

        return [
            'working_days' => $workingDays,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'attendance_records' => $attendanceRecords
        ];
    }

    /**
     * Calculate basic salary using daily rate × working days
     *
     * @param Employee $employee
     * @param array $attendanceData
     * @return float
     */
    private function calculateBasicSalary(Employee $employee, array $attendanceData): float
    {
        $dailyRate = $employee->daily_rate;
        $workingDays = $attendanceData['working_days'];
        
        return round($dailyRate * $workingDays, 2);
    }

    /**
     * Calculate overtime pay
     *
     * @param Employee $employee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function calculateOvertime(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        $attendanceData = $this->calculateAttendanceData($employee, $startDate, $endDate);
        $overtimeHours = $attendanceData['overtime_hours'];
        $overtimeRate = $employee->overtime_rate;
        $overtimePay = $overtimeHours * $overtimeRate;

        return [
            'overtime_hours' => $overtimeHours,
            'overtime_rate' => $overtimeRate,
            'overtime_pay' => round($overtimePay, 2)
        ];
    }

    /**
     * Calculate bonuses (performance, attendance, etc.)
     *
     * @param Employee $employee
     * @param array $attendanceData
     * @return float
     */
    private function calculateBonuses(Employee $employee, array $attendanceData): float
    {
        $bonuses = 0;
        
        // Perfect attendance bonus (example)
        $totalDays = $attendanceData['attendance_records']->count();
        $workingDays = $attendanceData['working_days'];
        
        if ($totalDays > 0 && $workingDays == $totalDays) {
            $bonuses += 500; // Perfect attendance bonus
        }
        
        return $bonuses;
    }

    /**
     * Calculate deductions (late, absent, etc.)
     *
     * @param Employee $employee
     * @param array $attendanceData
     * @return float
     */
    private function calculateDeductions(Employee $employee, array $attendanceData): float
    {
        $deductions = 0;
        
        // Late deductions
        $lateCount = $attendanceData['attendance_records']->where('status', 'late')->count();
        $deductions += $lateCount * 50; // ₱50 per late
        
        // Absent deductions
        $absentCount = $attendanceData['attendance_records']->where('status', 'absent')->count();
        $deductions += $absentCount * $employee->daily_rate; // Full day deduction for absent
        
        return $deductions;
    }

    /**
     * Calculate tax amount
     *
     * @param float $grossPay
     * @return float
     */
    private function calculateTax(float $grossPay): float
    {
        // Simple tax calculation (you can implement more complex tax brackets)
        if ($grossPay <= 25000) {
            return 0; // No tax for income below ₱25,000
        } elseif ($grossPay <= 50000) {
            return ($grossPay - 25000) * 0.15; // 15% for ₱25,001 - ₱50,000
        } else {
            return 3750 + (($grossPay - 50000) * 0.25); // 25% for above ₱50,000
        }
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