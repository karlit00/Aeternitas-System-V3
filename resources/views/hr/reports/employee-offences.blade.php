@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.reports.employee-offences'])

@section('title', 'Employee Offences')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Employee Offences</h1>
                    <p class="mt-1 text-sm text-gray-600">Track and manage employee infractions and violations</p>
                </div>
                <div>
                    <button onclick="openAddModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Offence
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Offences</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-gavel"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Verified</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['verified'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Serious Offences</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['serious'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('hr.reports.employee-offences') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Employee name or ID..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                    <select name="severity" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Severities</option>
                        <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="major" {{ request('severity') == 'major' ? 'selected' : '' }}>Major</option>
                        <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Serious</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <div class="flex space-x-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="lg:col-span-5 flex justify-end space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('hr.reports.employee-offences') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Offences Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Offence Records</h2>
                <p class="text-sm text-gray-600 mt-1">Showing {{ $offences->firstItem() ?? 0 }} to {{ $offences->lastItem() ?? 0 }} of {{ $offences->total() }} records</p>
            </div>
            
            @if($offences->isEmpty())
                <div class="p-8 text-center">
                    <i class="fas fa-gavel text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Offences Found</h3>
                    <p class="text-gray-600">No employee offences match your criteria.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offence Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($offences as $offence)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ strtoupper(substr($offence->employee->first_name ?? 'N', 0, 1)) }}{{ strtoupper(substr($offence->employee->last_name ?? 'A', 0, 1)) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $offence->employee->full_name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $offence->employee->employee_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $offence->offence_type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $offence->offence_date->format('M j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $offence->severity_badge_class }}">
                                        {{ ucfirst($offence->severity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $offence->status_badge_class }}">
                                        {{ ucfirst($offence->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button type="button" onclick="openViewModal()" class="text-blue-600 hover:text-blue-900 transition-colors font-semibold bg-transparent border-0 cursor-pointer p-0">View</button>
                                    <button type="button" onclick="openEditModal()" class="text-green-600 hover:text-green-900 transition-colors font-semibold bg-transparent border-0 cursor-pointer p-0">Update</button>
                                    <form action="{{ route('hr.reports.employee-offences.destroy', $offence->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this offence record?')" class="text-red-600 hover:text-red-900 transition-colors font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $offences->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Offence Modal -->
<div id="addOffenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto">
    <div class="relative mx-auto p-6 w-full max-w-2xl border shadow-lg rounded-lg bg-white my-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Add Employee Offence</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('hr.reports.employee-offences.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Employee Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Employee</label>
                    <div class="relative">
                        <input type="text" id="employeeSearch" placeholder="Search employee by name or ID..." 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute right-4 top-4 text-gray-400"></i>
                    </div>
                    <input type="hidden" name="employee_id" id="selectedEmployeeId" required>
                    
                    <!-- Employee Dropdown -->
                    <div id="employeeDropdown" class="hidden mt-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg bg-white shadow-lg">
                        @foreach($employees as $employee)
                            <div class="employee-option p-3 hover:bg-blue-50 cursor-pointer transition-colors border-b border-gray-100 last:border-b-0"
                                 data-employee-id="{{ $employee->id }}"
                                 data-employee-name="{{ $employee->full_name }}"
                                 data-employee-id-number="{{ $employee->employee_id }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-700">
                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->employee_id }} - {{ $employee->department->name ?? 'No Department' }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Selected Employee Display -->
                    <div id="selectedEmployeeDisplay" class="hidden mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-200 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-800" id="selectedEmployeeInitials"></span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900" id="selectedEmployeeName"></div>
                                    <div class="text-sm text-gray-600" id="selectedEmployeeIdNumber"></div>
                                </div>
                            </div>
                            <button type="button" onclick="clearEmployeeSelection()" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Offence Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Offence Type</label>
                        <input type="text" name="offence_type" required placeholder="e.g., Tardiness, Absent, Misconduct" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Offence</label>
                        <input type="date" name="offence_date" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                        <select name="severity" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="minor">Minor</option>
                            <option value="major">Major</option>
                            <option value="serious">Serious</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reported By</label>
                        <input type="text" name="reported_by" value="{{ auth()->user()->name ?? '' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" required rows="4" placeholder="Provide detailed description of the offence..." 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeAddModal()" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Save Offence
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Offence Modal -->
<div id="viewOffenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto p-4">
    <div class="relative mx-auto w-full max-w-2xl border shadow-lg rounded-lg bg-white my-8">
        <div class="flex justify-between items-center mb-6 p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900">Offence Details</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="viewOffenceContent" class="p-6 space-y-6">
            <div class="text-center py-8">
                <div class="inline-block">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                </div>
                <p class="mt-2 text-gray-600">Loading offence details...</p>
            </div>
        </div>
        <div class="mt-6 pt-6 border-t border-gray-200 p-6 flex justify-end">
            <button onclick="closeViewModal()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Offence Modal -->
<div id="editOffenceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto p-4">
    <div class="relative mx-auto w-full max-w-2xl border shadow-lg rounded-lg bg-white my-8">
        <div class="flex justify-between items-center mb-6 p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900">Edit Offence Record</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editOffenceForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="editOffenceId" name="offence_id">
            <input type="hidden" id="editOffenceEmployeeId">
            <div class="space-y-6" id="editOffenceContent">
                <div class="text-center py-8">
                    <div class="inline-block">
                        <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                    </div>
                    <p class="mt-2 text-gray-600">Loading offence data...</p>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center overflow-y-auto p-4">
    <div class="relative mx-auto w-full max-w-lg border shadow-lg rounded-lg bg-white my-8">
        <div class="flex justify-between items-center mb-6 p-6 border-b border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900">Update Status</h3>
            <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="updateStatusForm" class="p-6" method="POST">
            @csrf
            <input type="hidden" id="updateOffenceId">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="updateStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="dismissed">Dismissed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action Taken</label>
                    <textarea name="action_taken" id="updateActionTaken" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="updateNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeUpdateModal()" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Modal Functions
function openAddModal() {
    document.getElementById('addOffenceModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addOffenceModal').classList.add('hidden');
    document.body.style.overflow = '';
    resetAddModal();
}

function openViewModal() {
    document.getElementById('viewOffenceModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeViewModal() {
    document.getElementById('viewOffenceModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openEditModal() {
    document.getElementById('editOffenceModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editOffenceModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function resetAddModal() {
    document.getElementById('employeeSearch').value = '';
    document.getElementById('selectedEmployeeId').value = '';
    document.getElementById('employeeDropdown').classList.add('hidden');
    document.getElementById('selectedEmployeeDisplay').classList.add('hidden');
    document.querySelector('form[action*="store"]').reset();
}

// Employee Search and Selection
const employeeSearch = document.getElementById('employeeSearch');
const employeeDropdown = document.getElementById('employeeDropdown');
const selectedEmployeeDisplay = document.getElementById('selectedEmployeeDisplay');
const selectedEmployeeId = document.getElementById('selectedEmployeeId');

employeeSearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    
    if (searchTerm.length > 0) {
        employeeDropdown.classList.remove('hidden');
        
        const options = employeeDropdown.querySelectorAll('.employee-option');
        options.forEach(option => {
            const name = option.dataset.employeeName.toLowerCase();
            const id = option.dataset.employeeIdNumber.toLowerCase();
            
            if (name.includes(searchTerm) || id.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    } else {
        employeeDropdown.classList.add('hidden');
    }
});

employeeSearch.addEventListener('focus', function() {
    if (this.value.length > 0) {
        employeeDropdown.classList.remove('hidden');
    }
});

document.addEventListener('click', function(e) {
    if (!employeeSearch.contains(e.target) && !employeeDropdown.contains(e.target)) {
        employeeDropdown.classList.add('hidden');
    }
});

document.querySelectorAll('.employee-option').forEach(option => {
    option.addEventListener('click', function() {
        const employeeId = this.dataset.employeeId;
        const employeeName = this.dataset.employeeName;
        const employeeIdNumber = this.dataset.employeeIdNumber;
        const initials = this.querySelector('span').textContent;
        
        selectedEmployeeId.value = employeeId;
        document.getElementById('selectedEmployeeInitials').textContent = initials;
        document.getElementById('selectedEmployeeName').textContent = employeeName;
        document.getElementById('selectedEmployeeIdNumber').textContent = employeeIdNumber;
        
        employeeDropdown.classList.add('hidden');
        selectedEmployeeDisplay.classList.remove('hidden');
        employeeSearch.value = '';
    });
});

function clearEmployeeSelection() {
    selectedEmployeeId.value = '';
    selectedEmployeeDisplay.classList.add('hidden');
    employeeSearch.focus();
}

function closeEditModal() {
    document.getElementById('editOffenceModal').classList.add('hidden');
    document.getElementById('editOffenceForm').reset();
    document.body.style.overflow = '';
}

function editOffence(id) {
    console.log('editOffence called with id:', id);
    
    const content = document.getElementById('editOffenceContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <div class="inline-block">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
            </div>
            <p class="mt-2 text-gray-600">Loading offence data...</p>
        </div>
    `;
    
    // Open modal
    document.getElementById('editOffenceModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Store offence ID
    document.getElementById('editOffenceId').value = id;
</script>
@endpush