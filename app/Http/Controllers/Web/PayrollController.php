<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('employee.department');

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('month')) {
            $query->whereMonth('pay_period_start', $request->month);
        }

        if ($request->has('year')) {
            $query->whereYear('pay_period_start', $request->year);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('pay_period_start', 'desc')->paginate(15);
        $employees = Employee::all();

        // Calculate summary statistics
        $allPayrolls = $query->get();
        $summary = [
            'total_employees' => Employee::count(),
            'gross_pay' => $allPayrolls->sum('gross_pay'),
            'total_deductions' => $allPayrolls->sum('deductions'),
            'net_pay' => $allPayrolls->sum('net_pay'),
            'pending_count' => $allPayrolls->where('status', 'pending')->count(),
            'approved_count' => $allPayrolls->where('status', 'approved')->count(),
            'paid_count' => $allPayrolls->where('status', 'paid')->count(),
        ];

        return view('payroll.index', compact('payrolls', 'employees', 'summary'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        Payroll::create($request->all());

        return redirect()->route('payrolls.index')
            ->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee.department');
        return view('payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::all();
        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ]);

        $payroll->update($request->all());

        return redirect()->route('payrolls.index')
            ->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payrolls.index')
            ->with('success', 'Payroll deleted successfully.');
    }

    public function process(Payroll $payroll)
    {
        // Calculate net pay
        $grossPay = $payroll->basic_salary + 
                   ($payroll->overtime_hours * $payroll->overtime_rate) + 
                   $payroll->bonuses;

        $netPay = $grossPay - $payroll->deductions - $payroll->tax_amount;

        $payroll->update([
            'gross_pay' => $grossPay,
            'net_pay' => $netPay,
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return redirect()->route('payrolls.show', $payroll)
            ->with('success', 'Payroll processed successfully.');
    }

    public function summary()
    {
        $summary = DB::table('payrolls')
            ->select([
                DB::raw('COUNT(*) as total_payrolls'),
                DB::raw('SUM(gross_pay) as total_gross_pay'),
                DB::raw('SUM(net_pay) as total_net_pay'),
                DB::raw('AVG(gross_pay) as average_gross_pay'),
                DB::raw('AVG(net_pay) as average_net_pay'),
            ])
            ->where('status', 'processed')
            ->first();

        $monthly_data = DB::table('payrolls')
            ->select([
                DB::raw('YEAR(pay_period_start) as year'),
                DB::raw('MONTH(pay_period_start) as month'),
                DB::raw('SUM(gross_pay) as total_gross_pay'),
                DB::raw('SUM(net_pay) as total_net_pay'),
                DB::raw('COUNT(*) as payroll_count'),
            ])
            ->where('status', 'processed')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('payrolls.summary', compact('summary', 'monthly_data'));
    }

    public function monthlyReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $report = DB::table('payrolls')
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->select([
                'departments.name as department_name',
                DB::raw('COUNT(payrolls.id) as employee_count'),
                DB::raw('SUM(payrolls.gross_pay) as total_gross_pay'),
                DB::raw('SUM(payrolls.net_pay) as total_net_pay'),
            ])
            ->whereYear('payrolls.pay_period_start', $request->year)
            ->whereMonth('payrolls.pay_period_start', $request->month)
            ->where('payrolls.status', 'processed')
            ->groupBy('departments.id', 'departments.name')
            ->get();

        return view('payrolls.monthly-report', compact('report', 'request'));
    }
}
