@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.reports.new-employees'])

@section('title', 'New Employees Report')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">New Employees Report</h1>
                        <p class="mt-1 text-sm text-gray-600">Track and manage recently hired employees</p>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
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
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total New Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $newEmployees->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $newEmployees->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar text-lg"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Per Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ round($newEmployees->total() / 3) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- New Employees List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">New Employee Records</h2>
            <p class="text-sm text-gray-600 mt-1">Showing {{ $newEmployees->firstItem() }} to {{ $newEmployees->lastItem() }} of {{ $newEmployees->total() }} new employees</p>
        </div>
        
        @if($newEmployees->isEmpty())
            <div class="p-8 text-center">
                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No New Employees Found</h3>
                <p class="text-gray-600">No new employees were hired in the selected date range.</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($newEmployees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->created_at->format('M j, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('hr.employee-personnel-files.hiring', $employee->id) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    View Files
                                </a>
                                <a href="{{ route('employees.show', $employee->id) }}" 
                                   class="text-gray-600 hover:text-gray-900">
                                    View Profile
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900">
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $newEmployees->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Summary Report -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary Report</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Hiring Trend</h4>
                <p class="text-2xl font-bold text-blue-600">{{ $newEmployees->count() }}</p>
                <p class="text-xs text-blue-600">New hires in selected period</p>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-green-900 mb-2">Average Tenure</h4>
                <p class="text-2xl font-bold text-green-600">0-3 months</p>
                <p class="text-xs text-green-600">New employee range</p>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-purple-900 mb-2">Departments</h4>
                <p class="text-2xl font-bold text-purple-600">{{ $departments->count() }}</p>
                <p class="text-xs text-purple-600">Departments with new hires</p>
            </div>
            
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-orange-900 mb-2">Positions</h4>
                <p class="text-2xl font-bold text-orange-600">{{ $positions->count() }}</p>
                <p class="text-xs text-orange-600">Positions filled</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function applyFilters() {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const search = document.getElementById('search-input').value;
        const department = document.getElementById('department-filter').value;
        const position = document.getElementById('position-filter').value;
        
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        url.searchParams.set('search', search);
        url.searchParams.set('department', department);
        url.searchParams.set('position', position);
        
        window.location.href = url.toString();
    }

    function clearFilters() {
        document.getElementById('start-date').value = '';
        document.getElementById('end-date').value = '';
        document.getElementById('search-input').value = '';
        document.getElementById('department-filter').value = '';
        document.getElementById('position-filter').value = '';
        
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');
        url.searchParams.delete('search');
        url.searchParams.delete('department');
        url.searchParams.delete('position');
        
        window.location.href = url.toString();
    }

    function exportReport() {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const search = document.getElementById('search-input').value;
        const department = document.getElementById('department-filter').value;
        const position = document.getElementById('position-filter').value;
        
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (search) params.append('search', search);
        if (department) params.append('department', department);
        if (position) params.append('position', position);
        
        // For now, just show a message since export functionality would need backend implementation
        alert('Export functionality would be implemented here. Parameters: ' + params.toString());
    }

    // Add real-time search functionality
    document.getElementById('search-input').addEventListener('input', function() {
        // Debounce the search to avoid too many requests
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
</script>
@endpush
@endsection