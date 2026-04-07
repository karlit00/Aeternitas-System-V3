<div class="space-y-6">
    <!-- Employee Header -->
    <div class="flex items-center space-x-4">
        <div class="flex-shrink-0 h-16 w-16">
            <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                <span class="text-2xl font-medium text-white">
                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                </span>
            </div>
        </div>
        <div>
            <h4 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h4>
            <p class="text-lg text-gray-600">{{ $employee->position ?? 'No position' }}</p>
            <p class="text-gray-500">{{ $employee->department->name ?? 'No Department' }}</p>
            @if($employee->company)
                <p class="text-sm text-gray-400">
                    <i class="fas fa-building mr-1"></i>
                    {{ $employee->company->name }}
                </p>
            @endif
        </div>
    </div>
    
    <!-- Employee Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 mb-3">Personal Information</h5>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-700">Employee ID:</span>
                    <p class="text-sm text-gray-900">{{ $employee->employee_id }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Email:</span>
                    <p class="text-sm text-gray-900">{{ $employee->email ?? 'Not provided' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Phone:</span>
                    <p class="text-sm text-gray-900">{{ $employee->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Address:</span>
                    <p class="text-sm text-gray-900">{{ $employee->address ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Employment Information -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 mb-3">Employment Information</h5>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-700">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($employee->status == 'active') bg-green-100 text-green-800
                        @elseif($employee->status == 'on-leave') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($employee->status ?? 'active') }}
                    </span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Hire Date:</span>
                    <p class="text-sm text-gray-900">
                        @if($employee->hire_date)
                            {{ \Carbon\Carbon::parse($employee->hire_date)->format('M j, Y') }}
                        @else
                            Not provided
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Employment Type:</span>
                    <p class="text-sm text-gray-900">{{ ucfirst($employee->employment_type ?? 'Regular') }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Department:</span>
                    <p class="text-sm text-gray-900">{{ $employee->department->name ?? 'Not assigned' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Documents Section -->
    <div class="border-t border-gray-200 pt-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="font-medium text-gray-900">Employee Documents</h5>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-file-alt text-blue-500"></i>
                <span class="documents-count">{{ $employee->documents_count ?? 0 }} documents</span>
            </div>
        </div>
        
        @if($employee->documents->count() > 0)
            <div class="space-y-4">
                @foreach($employee->documents as $document)
                <div class="document-item" data-document-id="{{ $document->id }}" 
                     class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                        </div>
                        <div>
                            <h6 class="font-medium text-gray-900 document-name">{{ $document->name }}</h6>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span class="document-type">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $document->type }}
                                </span>
                                <span class="document-date">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $document->created_at->format('M j, Y') }}
                                </span>
                                @if($document->description)
                                    <span class="document-description">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        {{ $document->description }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('employee-info.document.download', $document->id) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                            <i class="fas fa-eye mr-2"></i> View
                        </a>
                        <a href="{{ route('employee-info.document.download', $document->id) }}" 
                           download
                           class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                            <i class="fas fa-download mr-2"></i> Download
                        </a>
                        <button onclick="deleteDocument({{ $document->id }}, '{{ addslashes($document->name) }}')" 
                                class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 bg-gray-50 rounded-lg">
                <i class="fas fa-file text-gray-300 text-4xl mb-4"></i>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No documents uploaded</h4>
                <p class="text-gray-600">This employee currently has no documents.</p>
                <div class="mt-4 flex justify-center space-x-3">
                    <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Document
                    </button>
                    <a href="{{ route('employees.show', $employee) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        View Profile
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Document Types Needed -->
    <div class="border-t border-gray-200 pt-6">
        <h5 class="font-medium text-gray-900 mb-4">Document Types Needed</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-file-contract text-blue-500 mr-2"></i>
                    <span class="font-medium text-gray-900">Employment Contract</span>
                    <span class="ml-auto text-sm text-red-500">Missing</span>
                </div>
                <p class="text-sm text-gray-600">Signed employment agreement</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-id-card text-green-500 mr-2"></i>
                    <span class="font-medium text-gray-900">Government IDs</span>
                    <span class="ml-auto text-sm text-red-500">Missing</span>
                </div>
                <p class="text-sm text-gray-600">SSS, PhilHealth, Pag-IBIG, TIN</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-graduation-cap text-purple-500 mr-2"></i>
                    <span class="font-medium text-gray-900">Educational Records</span>
                    <span class="ml-auto text-sm text-yellow-500">Partial</span>
                </div>
                <p class="text-sm text-gray-600">Diploma, Transcript of Records</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-file-medical text-red-500 mr-2"></i>
                    <span class="font-medium text-gray-900">Medical Certificate</span>
                    <span class="ml-auto text-sm text-red-500">Missing</span>
                </div>
                <p class="text-sm text-gray-600">Pre-employment medical exam</p>
            </div>
        </div>
    </div>
</div>