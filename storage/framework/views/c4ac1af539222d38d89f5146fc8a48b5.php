

<?php $__env->startSection('title', 'Offboarding - ' . $employee->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Offboarding Documents</h1>
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

    <!-- Offboarding Documents -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Resignation Letter -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Resignation Letter</h2>
                    <p class="text-sm text-gray-600">Employee resignation documentation</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Resignation Letter</h3>
                            <p class="text-sm text-gray-500">Formal resignation notice</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <?php if($offboardingData['resignation_letter']): ?>
                            <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Resignation Letter', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'offboarding', basename($offboardingData['resignation_letter'])])); ?>')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'offboarding', basename($offboardingData['resignation_letter'])])); ?>" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">No file uploaded</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exit Interview Records -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Exit Interview Records</h2>
                    <p class="text-sm text-gray-600">Employee exit interview documentation</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Exit Interview Records</h3>
                            <p class="text-sm text-gray-500">Interview notes and feedback</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <?php if($offboardingData['exit_interview_records']): ?>
                            <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Exit Interview Records', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'offboarding', basename($offboardingData['exit_interview_records'])])); ?>')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'offboarding', basename($offboardingData['exit_interview_records'])])); ?>" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">No file uploaded</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Termination Documentation -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Termination Documentation</h2>
                    <p class="text-sm text-gray-600">Employee termination records (if applicable)</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Termination Documentation</h3>
                            <p class="text-sm text-gray-500">Termination notice and records</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <?php if($offboardingData['termination_documentation']): ?>
                            <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Termination Documentation', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'offboarding', basename($offboardingData['termination_documentation'])])); ?>')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                                <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'offboarding', basename($offboardingData['termination_documentation'])])); ?>" 
                                      method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Uploaded
                                </span>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">No file uploaded</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Payroll Records -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Final Payroll Records</h2>
                    <p class="text-sm text-gray-600">Employee's final payroll and compensation</p>
                </div>
            </div>
            
            <?php if($offboardingData['final_payroll_records']->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $offboardingData['final_payroll_records']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo e($payroll->pay_period_start->format('M j')); ?> - <?php echo e($payroll->pay_period_end->format('M j, Y')); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₱<?php echo e(number_format($payroll->gross_pay, 2)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₱<?php echo e(number_format($payroll->net_pay, 2)); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php if($payroll->status === 'paid'): ?> bg-green-100 text-green-800
                                        <?php elseif($payroll->status === 'approved'): ?> bg-blue-100 text-blue-800
                                        <?php else: ?> bg-yellow-100 text-yellow-800 <?php endif; ?>">
                                        <?php echo e(ucfirst($payroll->status)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo e(route('payrolls.download-payslip', $payroll->id)); ?>" 
                                       class="text-blue-600 hover:text-blue-800 mr-4">
                                        Download Payslip
                                    </a>
                                    <a href="<?php echo e(route('payrolls.show', $payroll->id)); ?>" 
                                       class="text-gray-600 hover:text-gray-800">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="border rounded-lg p-4 text-center text-gray-500">
                    No final payroll records found.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload New Documents</h2>
        <p class="text-gray-600 mb-4">Upload additional offboarding documents for <?php echo e($employee->full_name); ?></p>
        
        <form action="<?php echo e(route('hr.employee-personnel-files.upload', [$employee->id, 'offboarding'])); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Document Type</label>
                    <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="resignation_letter">Resignation Letter</option>
                        <option value="exit_interview_records">Exit Interview Records</option>
                        <option value="termination_documentation">Termination Documentation</option>
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

<?php echo $__env->make('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sushitrash\Desktop\Aeternitas-System-V2-1\resources\views/hr/employee-personnel-files/offboarding.blade.php ENDPATH**/ ?>