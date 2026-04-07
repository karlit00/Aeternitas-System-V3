

<?php $__env->startSection('title', 'Performance & Development - ' . $employee->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Performance & Development</h1>
                        <p class="mt-1 text-sm text-gray-600">Employee: <?php echo e($employee->full_name); ?> (<?php echo e($employee->employee_id); ?>)</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="<?php echo e(route('hr.employee-personnel-files')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to All Employees
                        </a>
                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="mt-4">
                    <div class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg">
                        <i class="fas fa-user mr-2"></i>
                        <span class="font-medium"><?php echo e($employee->full_name); ?></span>
                        <span class="ml-2 text-blue-600">• <?php echo e($employee->employee_id); ?></span>
                        <span class="ml-2 text-blue-600">• <?php echo e($employee->position->name ?? 'N/A'); ?></span>
                        <span class="ml-2 text-blue-600">• <?php echo e($employee->department->name ?? 'N/A'); ?></span>
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
            
            <?php if($performanceData['performance_evaluations']->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $performanceData['performance_evaluations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evaluation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900"><?php echo e($evaluation->subject); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($evaluation->created_at->format('F j, Y')); ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($evaluation->message, 100)); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Evaluation</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No performance evaluations found.
                </div>
            <?php endif; ?>
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
            
            <?php if($performanceData['disciplinary_actions']->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $performanceData['disciplinary_actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900"><?php echo e($action->subject); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($action->created_at->format('F j, Y')); ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($action->message, 100)); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Disciplinary</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No disciplinary actions found.
                </div>
            <?php endif; ?>
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
            
            <?php if($performanceData['feedback_records']->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $performanceData['feedback_records']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900"><?php echo e($feedback->subject); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($feedback->created_at->format('F j, Y')); ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($feedback->message, 100)); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Feedback</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No feedback records found.
                </div>
            <?php endif; ?>
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
            
            <?php if($performanceData['training_records']->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $performanceData['training_records']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900"><?php echo e($training->subject); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($training->created_at->format('F j, Y')); ?></p>
                                <p class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($training->message, 100)); ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Training</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No training records found.
                </div>
            <?php endif; ?>
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
            
            <?php if($performanceData['overtime_requests']->count() > 0): ?>
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
                            <?php $__currentLoopData = $performanceData['overtime_requests']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($overtime->date->format('M j, Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($overtime->hours); ?> hours
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(Str::limit($overtime->reason, 50)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php if($overtime->status === 'approved'): ?> bg-green-100 text-green-800
                                        <?php elseif($overtime->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                        <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($overtime->status)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($overtime->approved_by ?? 'Pending'); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No overtime requests found.
                </div>
            <?php endif; ?>
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
            
            <?php if($performanceData['leave_requests']->count() > 0): ?>
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
                            <?php $__currentLoopData = $performanceData['leave_requests']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e(ucfirst($leave->type)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($leave->start_date->format('M j')); ?> - <?php echo e($leave->end_date->format('M j, Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e(Str::limit($leave->reason, 50)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php if($leave->status === 'approved'): ?> bg-green-100 text-green-800
                                        <?php elseif($leave->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                        <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($leave->status)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo e($leave->approved_by ?? 'Pending'); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No leave requests found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload New Documents</h2>
        <p class="text-gray-600 mb-4">Upload additional performance and development documents for <?php echo e($employee->full_name); ?></p>
        
        <form action="<?php echo e(route('hr.employee-personnel-files.upload', [$employee->id, 'performance'])); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
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
                <a href="<?php echo e(route('hr.employee-personnel-files')); ?>" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-200 transition-colors">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sushitrash\Desktop\Aeternitas-System-V2-1\resources\views/hr/employee-personnel-files/performance.blade.php ENDPATH**/ ?>