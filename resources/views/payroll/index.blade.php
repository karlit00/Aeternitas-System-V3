@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'payroll.index'])

@section('title', 'Payroll Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payroll Management</h1>
            <p class="mt-1 text-sm text-gray-600">Manage employee salaries, deductions, and payments</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export Payroll
            </button>
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Generate Payroll
            </button>
        </div>
    </div>

    <!-- Payroll Period Selector -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h3 class="text-lg font-medium text-gray-900">Payroll Period</h3>
                <p class="text-sm text-gray-600">Select the payroll period to view and manage</p>
            </div>
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <div>
                    <label for="payrollMonth" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select id="payrollMonth" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div>
                    <label for="payrollYear" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select id="payrollYear" class="w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Load
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Employees</p>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($summary['total_employees']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Gross Pay</p>
                    <p class="text-lg font-semibold text-gray-900">₱{{ number_format($summary['gross_pay'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-minus-circle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Total Deductions</p>
                    <p class="text-lg font-semibold text-gray-900">₱{{ number_format($summary['total_deductions'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Net Pay</p>
                    <p class="text-lg font-semibold text-gray-900">₱{{ number_format($summary['net_pay'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Status Overview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Payroll Status</h3>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                    Pending Review
                </button>
                <button class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                    Ready for Payment
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $summary['pending_count'] }}</div>
                <div class="text-sm text-gray-600">Pending Review</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $summary['approved_count'] }}</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $summary['paid_count'] }}</div>
                <div class="text-sm text-gray-600">Paid</div>
            </div>
        </div>
    </div>

    <!-- Payroll List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Employee Payroll</h3>
                    <p class="mt-1 text-sm text-gray-600">Individual payroll records for the selected period</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                    <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-sort mr-2"></i>
                        Sort
                    </button>
                </div>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Basic Salary
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Overtime
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Allowances
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deductions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Net Pay
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payrolls as $payroll)
                        @php
                            $initials = strtoupper(substr($payroll->employee->first_name, 0, 1) . substr($payroll->employee->last_name, 0, 1));
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'approved' => 'bg-green-100 text-green-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                                'rejected' => 'bg-red-100 text-red-800'
                            ];
                            $statusColor = $statusColors[$payroll->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $payroll->employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $payroll->employee->employee_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payroll->employee->department->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₱{{ number_format($payroll->basic_salary, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₱{{ number_format($payroll->overtime_pay, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₱{{ number_format($payroll->allowances, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">₱{{ number_format($payroll->deductions, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₱{{ number_format($payroll->net_pay, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                    <div class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_replace('text-', 'bg-', $statusColor) }}"></div>
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="openPayrollModal('{{ $payroll->id }}')" class="text-blue-600 hover:text-blue-900 transition-colors" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($payroll->status === 'pending')
                                        <button class="text-green-600 hover:text-green-900 transition-colors" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 transition-colors" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($payroll->status === 'approved')
                                        <button class="text-purple-600 hover:text-purple-900 transition-colors" title="Pay">
                                            <i class="fas fa-credit-card"></i>
                                        </button>
                                    @elseif($payroll->status === 'paid')
                                        <button class="text-gray-600 hover:text-gray-900 transition-colors" title="Print Payslip">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                No payroll records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $payrolls->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $payrolls->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $payrolls->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $payrolls->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        {{ $payrolls->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden">
            <div class="p-4 space-y-4">
                @forelse($payrolls as $payroll)
                    @php
                        $initials = strtoupper(substr($payroll->employee->first_name, 0, 1) . substr($payroll->employee->last_name, 0, 1));
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'paid' => 'bg-blue-100 text-blue-800',
                            'rejected' => 'bg-red-100 text-red-800'
                        ];
                        $statusColor = $statusColors[$payroll->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ $initials }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $payroll->employee->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $payroll->employee->department->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                <div class="w-1.5 h-1.5 rounded-full mr-1 {{ str_replace('text-', 'bg-', $statusColor) }}"></div>
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                            <div>
                                <div class="text-gray-500">Basic Salary</div>
                                <div class="font-medium">₱{{ number_format($payroll->basic_salary, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Net Pay</div>
                                <div class="font-medium">₱{{ number_format($payroll->net_pay, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Overtime</div>
                                <div class="font-medium">₱{{ number_format($payroll->overtime_pay, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Deductions</div>
                                <div class="font-medium">₱{{ number_format($payroll->deductions, 2) }}</div>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button onclick="openPayrollModal('{{ $payroll->id }}')" class="text-blue-600 hover:text-blue-900 transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            @if($payroll->status === 'pending')
                                <button class="text-green-600 hover:text-green-900 transition-colors">
                                    <i class="fas fa-check mr-1"></i>Approve
                                </button>
                            @elseif($payroll->status === 'approved')
                                <button class="text-purple-600 hover:text-purple-900 transition-colors">
                                    <i class="fas fa-credit-card mr-1"></i>Pay
                                </button>
                            @elseif($payroll->status === 'paid')
                                <button class="text-gray-600 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-print mr-1"></i>Print
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-8">
                        No payroll records found
                    </div>
                @endforelse
            </div>
            
            <!-- Mobile Pagination -->
            <div class="px-4 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between">
                        {{ $payrolls->appends(request()->query())->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
            <button class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-check-double mr-2"></i>
                Approve All Pending
            </button>
            <button class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-credit-card mr-2"></i>
                Process Payments
            </button>
            <button class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>
                Generate Payslips
            </button>
            <button class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export to Excel
            </button>
        </div>
    </div>
</div>

<!-- Payroll Details Modal -->
<div id="payrollModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Payroll Details</h3>
                <button onclick="closePayrollModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Employee Info -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Employee Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Name:</span>
                            <span class="ml-2 font-medium">John Smith</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Employee ID:</span>
                            <span class="ml-2 font-medium">EMP-001</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Department:</span>
                            <span class="ml-2 font-medium">IT Department</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Position:</span>
                            <span class="ml-2 font-medium">Software Developer</span>
                        </div>
                    </div>
                </div>

                <!-- Earnings -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Earnings</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Basic Salary</span>
                            <span class="font-medium">₱25,000.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Overtime Pay</span>
                            <span class="font-medium">₱3,500.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Allowances</span>
                            <span class="font-medium">₱2,000.00</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 font-medium">
                            <span>Total Earnings</span>
                            <span>₱30,500.00</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="bg-red-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Deductions</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>SSS Contribution</span>
                            <span class="font-medium">₱1,200.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>PhilHealth</span>
                            <span class="font-medium">₱800.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pag-IBIG</span>
                            <span class="font-medium">₱200.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Withholding Tax</span>
                            <span class="font-medium">₱2,000.00</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 font-medium">
                            <span>Total Deductions</span>
                            <span>₱4,200.00</span>
                        </div>
                    </div>
                </div>

                <!-- Net Pay -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-gray-900">Net Pay</span>
                        <span class="text-2xl font-bold text-blue-600">₱26,300.00</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closePayrollModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Close
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Payslip
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openPayrollModal() {
    document.getElementById('payrollModal').classList.remove('hidden');
}

function closePayrollModal() {
    document.getElementById('payrollModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('payrollModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePayrollModal();
    }
});
</script>
@endsection
