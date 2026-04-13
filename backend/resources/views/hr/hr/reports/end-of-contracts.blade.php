@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.reports.end-of-contracts'])

@section('title', 'End of Contracts Report')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">End of Contracts Report</h1>
                        <p class="mt-1 text-sm text-gray-600">Monitor and manage employee contract expirations</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('hr.employee-personnel-files') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Employee Files
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Date Range Filter -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Contract Expiry Date Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="start_date" id="start-date" 
                           value="{{ $startDate->format('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="date" name="end_date" id="end-date" 
                           value="{{ $endDate->format('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="search-input" placeholder="Search by name, ID, or position..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="department-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Position Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <select id="position-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Positions</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Contract Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contract Type</label>
                <select id="contract-type-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Contract Types</option>
                    <option value="regular">Regular</option>
                    <option value="probationary">Probationary</option>
                    <option value="contractual">Contractual</option>
                    <option value="project">Project-based</option>
                </select>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="mt-4 flex space-x-3">
            <button onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
            <button onclick="clearFilters()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                <i class="fas fa-times mr-2"></i>Clear Filters
            </button>
            <button onclick="exportReport()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-download mr-2"></i>Export Report
            </button>
            <button onclick="sendReminders()" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                <i class="fas fa-bell mr-2"></i>Send Reminders
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Contracts Expiring</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalContracts }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-contract text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Expiring This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $expiringThisMonth }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Expiring Next Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ max(0, $totalContracts - $expiringThisMonth) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Already Renewed</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-sync text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- End of Contracts List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Contracts Expiring</h2>
            <p class="text-sm text-gray-600 mt-1">Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} contracts</p>
        </div>
        
        @if($employees->isEmpty())
            <div class="p-8 text-center">
                <i class="fas fa-file-contract text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Contracts Expiring</h3>
                <p class="text-gray-600">No employee contracts are expiring in the selected date range.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                        @php
                            // Calculate approximate contract end date
                            $hasContractEndDate = isset($hasContractEndDate) && $hasContractEndDate && isset($employee->contract_end_date);
                            if ($hasContractEndDate) {
                                $contractEndDate = \Carbon\Carbon::parse($employee->contract_end_date);
                            } else {
                                // Fallback: calculate based on hire date
                                $hireDate = \Carbon\Carbon::parse($employee->created_at);
                                // Assume 6-month probationary or 1-year contract
                                $contractEndDate = $hireDate->copy()->addMonths(6);
                            }
                            $daysRemaining = now()->diffInDays($contractEndDate, false);
                            $isUrgent = $daysRemaining <= 30;
                            $isVeryUrgent = $daysRemaining <= 14;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $isVeryUrgent ? 'bg-red-50' : ($isUrgent ? 'bg-yellow-50' : '') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">{{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->employee_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->position->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->employment_type === 'probationary' ? 'bg-yellow-100 text-yellow-800' : ($employee->employment_type === 'contractual' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($employee->employment_type ?? 'Regular') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $contractEndDate->format('M j, Y') }}
                                @if(!$hasContractEndDate)
                                    <span class="text-xs text-gray-500 ml-1">(estimated)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isVeryUrgent ? 'bg-red-100 text-red-800' : ($isUrgent ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $daysRemaining > 0 ? $daysRemaining . ' days' : 'Expired' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('hr.employee-personnel-files.employment', $employee->id) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View Employment Details">
                                    <i class="fas fa-file-contract"></i>
                                </a>
                                <a href="{{ route('employees.show', $employee->id) }}" 
                                   class="text-gray-600 hover:text-gray-900" title="View Profile">
                                    <i class="fas fa-user"></i>
                                </a>
                                <button onclick="renewContract({{ $employee->id }})" 
                                        class="text-green-600 hover:text-green-900" title="Renew Contract">
                                    <i class="fas fa-sync"></i>
                                </button>
                                <button onclick="sendReminder({{ $employee->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900" title="Send Reminder">
                                    <i class="fas fa-bell"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $employees->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Summary Report -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Expiry Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Contracts Expiring</h4>
                <p class="text-2xl font-bold text-blue-600">{{ $totalContracts }}</p>
                <p class="text-xs text-blue-600">In selected date range</p>
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-red-900 mb-2">Urgent Attention</h4>
                <p class="text-2xl font-bold text-red-600">{{ $expiringThisMonth }}</p>
                <p class="text-xs text-red-600">Expiring this month</p>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-yellow-900 mb-2">Upcoming</h4>
                <p class="text-2xl font-bold text-yellow-600">{{ max(0, $totalContracts - $expiringThisMonth) }}</p>
                <p class="text-xs text-yellow-600">Expiring next month</p>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-green-900 mb-2">Departments Affected</h4>
                <p class="text-2xl font-bold text-green-600">{{ $departments->count() }}</p>
                <p class="text-xs text-green-600">Departments with expiring contracts</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="exportContractsReport()" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-export mr-2"></i>
                Export All Contracts
            </button>
            <button onclick="generateRenewalLetters()" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-alt mr-2"></i>
                Generate Renewal Letters
            </button>
            <button onclick="scheduleReminders()" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-calendar-alt mr-2"></i>
                Schedule Automated Reminders
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show loading overlay
    function showLoading(message = 'Loading...') {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50';
        overlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 shadow-xl">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                    <span class="text-gray-700">${message}</span>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    // Hide loading overlay
    function hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) overlay.remove();
    }

    // Show success notification
    function showSuccess(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    // Show error notification
    function showError(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    // Apply filters
    function applyFilters() {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const search = document.getElementById('search-input').value;
        const department = document.getElementById('department-filter').value;
        const position = document.getElementById('position-filter').value;
        const contractType = document.getElementById('contract-type-filter').value;
        
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        url.searchParams.set('search', search);
        url.searchParams.set('department', department);
        url.searchParams.set('position', position);
        url.searchParams.set('contract_type', contractType);
        
        window.location.href = url.toString();
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('start-date').value = '';
        document.getElementById('end-date').value = '';
        document.getElementById('search-input').value = '';
        document.getElementById('department-filter').value = '';
        document.getElementById('position-filter').value = '';
        document.getElementById('contract-type-filter').value = '';
        
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');
        url.searchParams.delete('search');
        url.searchParams.delete('department');
        url.searchParams.delete('position');
        url.searchParams.delete('contract_type');
        
        window.location.href = url.toString();
    }

    // Export report to CSV
    function exportReport() {
        showLoading('Generating export file...');
        
        const params = new URLSearchParams();
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const search = document.getElementById('search-input').value;
        const department = document.getElementById('department-filter').value;
        const position = document.getElementById('position-filter').value;
        const contractType = document.getElementById('contract-type-filter').value;
        
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (search) params.append('search', search);
        if (department) params.append('department', department);
        if (position) params.append('position', position);
        if (contractType) params.append('contract_type', contractType);
        params.append('export', 'csv');
        
        // Create a temporary form to submit the export request
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = window.location.pathname + '?' + params.toString();
        document.body.appendChild(form);
        form.submit();
        
        setTimeout(() => {
            hideLoading();
            showSuccess('Export file generated successfully!');
        }, 2000);
    }

    // Send reminders to all employees in the list
    function sendReminders() {
        if (!confirm('Are you sure you want to send contract expiry reminders to all employees in this list?')) {
            return;
        }
        
        showLoading('Sending reminder emails...');
        
        // Get all employee IDs from the table
        const employeeIds = [];
        document.querySelectorAll('tbody tr').forEach(row => {
            const renewBtn = row.querySelector('button[onclick^="sendReminder("]');
            if (renewBtn) {
                const match = renewBtn.getAttribute('onclick').match(/sendReminder\((\d+)\)/);
                if (match) employeeIds.push(match[1]);
            }
        });
        
        // Send AJAX request
        fetch('{{ route("hr.reports.end-of-contracts") }}/send-reminders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ employee_ids: employeeIds })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showSuccess(`${data.sent} reminder(s) sent successfully!`);
            } else {
                showError(data.message || 'Failed to send reminders');
            }
        })
        .catch(error => {
            hideLoading();
            showError('An error occurred while sending reminders');
            console.error('Error:', error);
        });
    }

    // Renew contract for an employee
    function renewContract(employeeId) {
        if (!confirm('Are you sure you want to initiate the contract renewal process for this employee?')) {
            return;
        }
        
        // Redirect to employee edit page with contract tab
        window.location.href = `/employees/${employeeId}/edit?tab=contract&action=renew`;
    }

    // Send reminder to a specific employee
    function sendReminder(employeeId) {
        if (!confirm('Send a contract expiry reminder to this employee?')) {
            return;
        }
        
        showLoading('Sending reminder...');
        
        fetch(`{{ route("hr.reports.end-of-contracts") }}/send-reminder/${employeeId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showSuccess('Reminder sent successfully!');
            } else {
                showError(data.message || 'Failed to send reminder');
            }
        })
        .catch(error => {
            hideLoading();
            showError('An error occurred while sending reminder');
            console.error('Error:', error);
        });
    }

    // Export all contracts report
    function exportContractsReport() {
        showLoading('Generating contracts report...');
        
        fetch(`{{ route("hr.reports.end-of-contracts") }}?export=all`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Export failed');
        })
        .then(blob => {
            hideLoading();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'contracts-report-' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showSuccess('Contracts report exported successfully!');
        })
        .catch(error => {
            hideLoading();
            showError('Failed to export contracts report');
            console.error('Error:', error);
        });
    }

    // Generate renewal letters
    function generateRenewalLetters() {
        if (!confirm('Generate renewal letters for all employees with expiring contracts?')) {
            return;
        }
        
        showLoading('Generating renewal letters...');
        
        // Get all employee IDs from the table
        const employeeIds = [];
        document.querySelectorAll('tbody tr').forEach(row => {
            const renewBtn = row.querySelector('button[onclick^="renewContract("]');
            if (renewBtn) {
                const match = renewBtn.getAttribute('onclick').match(/renewContract\((\d+)\)/);
                if (match) employeeIds.push(match[1]);
            }
        });
        
        fetch('{{ route("hr.reports.end-of-contracts") }}/generate-renewal-letters', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ employee_ids: employeeIds })
        })
        .then(response => response.blob())
        .then(blob => {
            hideLoading();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'renewal-letters-' + new Date().toISOString().split('T')[0] + '.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showSuccess('Renewal letters generated successfully!');
        })
        .catch(error => {
            hideLoading();
            showError('Failed to generate renewal letters');
            console.error('Error:', error);
        });
    }

    // Schedule automated reminders
    function scheduleReminders() {
        if (!confirm('Schedule automated contract expiry reminders? This will send reminders 30 days, 14 days, and 7 days before contract expiry.')) {
            return;
        }
        
        showLoading('Scheduling automated reminders...');
        
        fetch('{{ route("hr.reports.end-of-contracts") }}/schedule-reminders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reminders: [
                    { days_before: 30, template: 'contract_expiry_30_days' },
                    { days_before: 14, template: 'contract_expiry_14_days' },
                    { days_before: 7, template: 'contract_expiry_7_days' }
                ]
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showSuccess('Automated reminders scheduled successfully!');
            } else {
                showError(data.message || 'Failed to schedule reminders');
            }
        })
        .catch(error => {
            hideLoading();
            showError('An error occurred while scheduling reminders');
            console.error('Error:', error);
        });
    }

    // Add real-time search functionality
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });

    // Initialize date pickers with default values
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        
        // Set default date range (next 3 months from today)
        if (!startDateInput.value) {
            startDateInput.value = new Date().toISOString().split('T')[0];
        }
        if (!endDateInput.value) {
            const threeMonthsLater = new Date();
            threeMonthsLater.setMonth(threeMonthsLater.getMonth() + 3);
            endDateInput.value = threeMonthsLater.toISOString().split('T')[0];
        }
    });
</script>
@endpush
@endsection