@extends('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'])

@section('title', 'Performance & Development - ' . $employee->full_name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Performance & Development</h1>
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

    <!-- Performance Records -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Performance Evaluations -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Performance Evaluations</h2>
                    <p class="text-sm text-gray-600">Employee performance reviews and assessments</p>
                </div>
            </div>
            
            @if($performanceData['performance_evaluations']->count() > 0)
                <div class="space-y-3">
                    @foreach($performanceData['performance_evaluations'] as $evaluation)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $evaluation->subject }}</h3>
                                <p class="text-sm text-gray-500">{{ $evaluation->created_at->format('F j, Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ Str::limit($evaluation->message, 100) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Evaluation</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No performance evaluations found.
                </div>
            @endif
        </div>

        <!-- Disciplinary Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Disciplinary Actions</h2>
                    <p class="text-sm text-gray-600">Employee disciplinary records and warnings</p>
                </div>
            </div>
            
            @if($performanceData['disciplinary_actions']->count() > 0)
                <div class="space-y-3">
                    @foreach($performanceData['disciplinary_actions'] as $action)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $action->subject }}</h3>
                                <p class="text-sm text-gray-500">{{ $action->created_at->format('F j, Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ Str::limit($action->message, 100) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Disciplinary</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No disciplinary actions found.
                </div>
            @endif
        </div>

        <!-- Feedback Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Feedback Records</h2>
                    <p class="text-sm text-gray-600">Employee feedback and communication records</p>
                </div>
            </div>
            
            @if($performanceData['feedback_records']->count() > 0)
                <div class="space-y-3">
                    @foreach($performanceData['feedback_records'] as $feedback)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $feedback->subject }}</h3>
                                <p class="text-sm text-gray-500">{{ $feedback->created_at->format('F j, Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ Str::limit($feedback->message, 100) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Feedback</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No feedback records found.
                </div>
            @endif
        </div>

        <!-- Training Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Training Records</h2>
                    <p class="text-sm text-gray-600">Employee training and development activities</p>
                </div>
            </div>
            
            @if($performanceData['training_records']->count() > 0)
                <div class="space-y-3">
                    @foreach($performanceData['training_records'] as $training)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $training->subject }}</h3>
                                <p class="text-sm text-gray-500">{{ $training->created_at->format('F j, Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ Str::limit($training->message, 100) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Training</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No training records found.
                </div>
            @endif
        </div>

        <!-- Overtime Requests -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Overtime Requests</h2>
                    <p class="text-sm text-gray-600">Employee overtime requests and approvals</p>
                </div>
            </div>
            
            @if($performanceData['overtime_requests']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($performanceData['overtime_requests'] as $overtime)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $overtime->date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $overtime->hours }} hours
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Str::limit($overtime->reason, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($overtime->status === 'approved') bg-green-100 text-green-800
                                        @elseif($overtime->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($overtime->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $overtime->approved_by ?? 'Pending' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No overtime requests found.
                </div>
            @endif
        </div>

        <!-- Leave Requests -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Leave Requests</h2>
                    <p class="text-sm text-gray-600">Employee leave requests and approvals</p>
                </div>
            </div>
            
            @if($performanceData['leave_requests']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($performanceData['leave_requests'] as $leave)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($leave->type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $leave->start_date->format('M j') }} - {{ $leave->end_date->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Str::limit($leave->reason, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($leave->status === 'approved') bg-green-100 text-green-800
                                        @elseif($leave->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $leave->approved_by ?? 'Pending' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No leave requests found.
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload New Documents</h2>
        <p class="text-gray-600 mb-4">Upload additional performance and development documents for {{ $employee->full_name }}</p>
        
        <form action="{{ route('hr.employee-personnel-files.upload', [$employee->id, 'performance']) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Document Type</label>
                    <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="performance_evaluation">Performance Evaluation</option>
                        <option value="disciplinary_action">Disciplinary Action</option>
                        <option value="feedback">Feedback</option>
                        <option value="training">Training Record</option>
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
