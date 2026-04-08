@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'])

@section('title', 'Employment Details - ' . $employee->full_name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Employment Details</h1>
                        <p class="mt-1 text-sm text-gray-600">Employee: {{ $employee->full_name }} ({{ $employee->employee_id }})</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('hr.employee-personnel-files') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to All Employees
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="mt-4">
                    <div class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg">
                        <i class="fas fa-user mr-2"></i>
                        <span class="font-medium">{{ $employee->full_name }}</span>
                        <span class="ml-2 text-blue-600">• {{ $employee->employee_id }}</span>
                        <span class="ml-2 text-blue-600">• {{ $employee->position->name ?? 'N/A' }}</span>
                        <span class="ml-2 text-blue-600">• {{ $employee->department->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- File Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Job Description & Tax Forms -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Job Description & Tax Forms</h2>
                    <p class="text-sm text-gray-600">Position details and tax documentation</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <!-- Job Description -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Job Description</h3>
                                <p class="text-sm text-gray-500">Detailed job responsibilities and requirements</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($employmentData['job_description'])
                                <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Job Description', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'employment', basename($employmentData['job_description'])]) }}')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'employment', basename($employmentData['job_description'])]) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">No file uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tax Forms -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Tax Forms</h3>
                                <p class="text-sm text-gray-500">W-4 and other tax documentation</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($employmentData['tax_forms'])
                                <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Tax Forms', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'employment', basename($employmentData['tax_forms'])]) }}')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'employment', basename($employmentData['tax_forms'])]) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">No file uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact & Salary History -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Emergency Contact & Salary History</h2>
                    <p class="text-sm text-gray-600">Contact information and compensation history</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <!-- Emergency Contact -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Emergency Contact</h3>
                                <p class="text-sm text-gray-500">Emergency contact information</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($employmentData['emergency_contact'])
                                <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Emergency Contact', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'employment', basename($employmentData['emergency_contact'])]) }}')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'employment', basename($employmentData['emergency_contact'])]) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">No file uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Salary History -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Salary History</h3>
                                <p class="text-sm text-gray-500">Compensation and salary changes</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($employmentData['salary_history'])
                                <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Salary History', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'employment', basename($employmentData['salary_history'])]) }}')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'employment', basename($employmentData['salary_history'])]) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">No file uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Employment -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Previous Employment</h2>
                    <p class="text-sm text-gray-600">Work history and previous employer information</p>
                </div>
            </div>
            
            @if($employmentData['previous_employments']->count() > 0)
                <div class="space-y-4">
                    @foreach($employmentData['previous_employments'] as $prevEmp)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $prevEmp->company_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $prevEmp->position }} • {{ $prevEmp->start_date }} to {{ $prevEmp->end_date }}</p>
                                    <p class="text-xs text-gray-400">Reason for leaving: {{ $prevEmp->reason_for_leaving }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                @if($prevEmp->document)
                                    <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Previous Employment Document', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'employment', basename($prevEmp->document)]) }}')"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </button>
                                    <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'employment', basename($prevEmp->document)]) }}" 
                                          method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Uploaded
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">No document</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No previous employment records found.
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload New Documents</h2>
        <p class="text-gray-600 mb-4">Upload additional employment-related documents for {{ $employee->full_name }}</p>
        
        <form action="{{ route('hr.employee-personnel-files.upload', [$employee->id, 'employment']) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Document Type</label>
                    <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="job_description">Job Description</option>
                        <option value="tax_forms">Tax Forms</option>
                        <option value="emergency_contact">Emergency Contact</option>
                        <option value="salary_history">Salary History</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Upload Document
                </button>
                <a href="{{ route('hr.employee-personnel-files') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Document Modal -->
    <div id="documentModal" class="fixed inset-0 bg-black bg-opacity-90 overflow-hidden hidden" style="z-index: 1000;">
        <div class="relative w-full h-full flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 bg-gray-900 text-white border-b border-gray-700">
                <h3 id="modalTitle" class="text-lg font-semibold"></h3>
                <div class="flex space-x-4">
                    <a id="downloadLink" href="#" download class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>
                    <button onclick="closeDocumentModal()" class="text-gray-300 hover:text-white transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 p-4 bg-black">
                <iframe id="documentFrame" src="" class="w-full h-full border-0" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openDocumentModal(employeeName, documentType, documentUrl) {
            document.getElementById('modalTitle').textContent = employeeName + ' - ' + documentType;
            document.getElementById('documentFrame').src = documentUrl;
            document.getElementById('downloadLink').href = documentUrl;
            document.getElementById('documentModal').classList.remove('hidden');
        }

        function closeDocumentModal() {
            document.getElementById('documentModal').classList.add('hidden');
            document.getElementById('documentFrame').src = '';
        }
    </script>
</div>
@endsection
