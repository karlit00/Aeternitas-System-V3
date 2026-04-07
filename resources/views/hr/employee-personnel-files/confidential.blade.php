@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'])

@section('title', 'Confidential Files - ' . $employee->full_name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Confidential Files</h1>
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

    <!-- Confidential Documents Warning -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-800">
                    <strong>Confidential Information:</strong> These documents contain sensitive employee information 
                    including medical records, background checks, and financial data. Access is restricted to 
                    authorized HR personnel only.
                </p>
            </div>
        </div>
    </div>

    <!-- Confidential Files Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Medical Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Medical Records</h2>
                    <p class="text-sm text-gray-600">Employee medical information and health records</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Medical Records</h3>
                            <p class="text-sm text-gray-500">Health information and medical documentation</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if($confidentialData['medical_records'])
                            <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Medical Records', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['medical_records'])]) }}')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['medical_records'])]) }}" 
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

        <!-- Medical Leave Documents -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Medical Leave Documents</h2>
                    <p class="text-sm text-gray-600">Medical certificates and leave documentation</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Medical Leave Documents</h3>
                            <p class="text-sm text-gray-500">Medical certificates and sick leave records</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if($confidentialData['medical_leave_documents'])
                            <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Medical Leave Documents', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['medical_leave_documents'])]) }}')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['medical_leave_documents'])]) }}" 
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

        <!-- Health Insurance Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Health Insurance Information</h2>
                    <p class="text-sm text-gray-600">Employee health insurance details</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Health Insurance Info</h3>
                            <p class="text-sm text-gray-500">Insurance policy and coverage details</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if($confidentialData['health_insurance_info'])
                            <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Health Insurance Info', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['health_insurance_info'])]) }}')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['health_insurance_info'])]) }}" 
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

        <!-- Background Checks -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Background Checks</h2>
                    <p class="text-sm text-gray-600">Employee background verification records</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Background Checks</h3>
                            <p class="text-sm text-gray-500">Criminal record and verification checks</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if($confidentialData['background_checks'])
                            <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Background Checks', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['background_checks'])]) }}')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['background_checks'])]) }}" 
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

        <!-- Child Support/Garnishment -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Child Support & Garnishment</h2>
                    <p class="text-sm text-gray-600">Legal deductions and garnishment orders</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Child Support/Garnishment</h3>
                            <p class="text-sm text-gray-500">Court orders and legal deductions</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if($confidentialData['child_support_garnishment'])
                            <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Child Support/Garnishment', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['child_support_garnishment'])]) }}')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['child_support_garnishment'])]) }}" 
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

        <!-- Bank Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Bank Details</h2>
                    <p class="text-sm text-gray-600">Employee bank account information</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Bank Details</h3>
                            <p class="text-sm text-gray-500">Bank account and payroll information</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if($confidentialData['bank_details'])
                            <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Bank Details', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['bank_details'])]) }}')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['bank_details'])]) }}" 
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

        <!-- I-9 Forms & Work Eligibility -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-passport"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">I-9 Forms & Work Eligibility</h2>
                    <p class="text-sm text-gray-600">Employment eligibility verification</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <!-- I-9 Forms -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">I-9 Forms</h3>
                                <p class="text-sm text-gray-500">Employment eligibility verification</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($confidentialData['i9_forms'])
                                <button onclick="openDocumentModal('{{ $employee->full_name }}', 'I-9 Forms', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['i9_forms'])]) }}')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                    <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['i9_forms'])]) }}" 
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

                <!-- Work Eligibility -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Work Eligibility</h3>
                                <p class="text-sm text-gray-500">Work authorization documents</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($confidentialData['work_eligibility'])
                                <button onclick="openDocumentModal('{{ $employee->full_name }}', 'Work Eligibility', '{{ route('hr.employee-personnel-files.view', [$employee->id, 'confidential', basename($confidentialData['work_eligibility'])]) }}')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                    <form action="{{ route('hr.employee-personnel-files.delete', [$employee->id, 'confidential', basename($confidentialData['work_eligibility'])]) }}" 
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
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload New Documents</h2>
        <p class="text-gray-600 mb-4">Upload additional confidential documents for {{ $employee->full_name }}</p>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-red-800">
                <strong>Security Notice:</strong> Only upload documents that are absolutely necessary for HR purposes. 
                All uploads are logged and monitored for security compliance.
            </p>
        </div>
        
        <form action="{{ route('hr.employee-personnel-files.upload', [$employee->id, 'confidential']) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Document Type</label>
                    <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="medical_records">Medical Records</option>
                        <option value="medical_leave_documents">Medical Leave Documents</option>
                        <option value="health_insurance_info">Health Insurance Info</option>
                        <option value="background_checks">Background Checks</option>
                        <option value="child_support_garnishment">Child Support/Garnishment</option>
                        <option value="bank_details">Bank Details</option>
                        <option value="i9_forms">I-9 Forms</option>
                        <option value="work_eligibility">Work Eligibility</option>
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
