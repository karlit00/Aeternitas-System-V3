

<?php $__env->startSection('title', 'Hiring & Onboarding - ' . $employee->full_name); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Hiring & Onboarding Documents</h1>
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

    <!-- File Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Job Application & Resume -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Job Application & Resume</h2>
                    <p class="text-sm text-gray-600">Application forms and resume documents</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <!-- Job Application -->
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Job Application</h3>
                            <p class="text-sm text-gray-500">Initial job application and cover letter</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                            <?php if($hiringData['job_application']): ?>
                            <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Job Application', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'hiring', basename($hiringData['job_application'])])); ?>')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                            <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'hiring', basename($hiringData['job_application'])])); ?>" 
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

                <!-- Resume -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Resume/CV</h3>
                                <p class="text-sm text-gray-500">Professional resume</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <?php if($hiringData['resume']): ?>
                                <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Resume/CV', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'hiring', basename($hiringData['resume'])])); ?>')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'hiring', basename($hiringData['resume'])])); ?>" 
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
        </div>

        <!-- Offer Letter & Contract -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Offer Letter & Contract</h2>
                    <p class="text-sm text-gray-600">Employment offer and contract documents</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <!-- Offer Letter -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Offer Letter</h3>
                                <p class="text-sm text-gray-500">Employment offer document</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <?php if($hiringData['offer_letter']): ?>
                                <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Offer Letter', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'hiring', basename($hiringData['offer_letter'])])); ?>')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'hiring', basename($hiringData['offer_letter'])])); ?>" 
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

                <!-- Employment Contract -->
                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                            <div>
                                <h3 class="font-medium text-gray-900">Employment Contract</h3>
                                <p class="text-sm text-gray-500">Signed employment agreement</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <?php if($hiringData['employment_contract']): ?>
                                <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Employment Contract', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'hiring', basename($hiringData['employment_contract'])])); ?>')"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View
                                </button>
                                <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'hiring', basename($hiringData['employment_contract'])])); ?>" 
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
        </div>

        <!-- Onboarding Checklist -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Onboarding Checklist</h2>
                    <p class="text-sm text-gray-600">New employee onboarding completion checklist</p>
                </div>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-file-pdf text-red-500 text-lg"></i>
                        <div>
                            <h3 class="font-medium text-gray-900">Onboarding Checklist</h3>
                            <p class="text-sm text-gray-500">Completed onboarding tasks and documentation</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                            <?php if($hiringData['onboarding_checklist']): ?>
                            <button onclick="openDocumentModal('<?php echo e($employee->full_name); ?>', 'Onboarding Checklist', '<?php echo e(route('hr.employee-personnel-files.view', [$employee->id, 'hiring', basename($hiringData['onboarding_checklist'])])); ?>')"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </button>
                            <form action="<?php echo e(route('hr.employee-personnel-files.delete', [$employee->id, 'hiring', basename($hiringData['onboarding_checklist'])])); ?>" 
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
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload New Documents</h2>
        <p class="text-gray-600 mb-4">Upload additional hiring and onboarding documents for <?php echo e($employee->full_name); ?></p>
        
        <form action="<?php echo e(route('hr.employee-personnel-files.upload', [$employee->id, 'hiring'])); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Document Type</label>
                    <select name="document_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="job_application">Job Application</option>
                        <option value="resume">Resume/CV</option>
                        <option value="offer_letter">Offer Letter</option>
                        <option value="employment_contract">Employment Contract</option>
                        <option value="onboarding_checklist">Onboarding Checklist</option>
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

<?php echo $__env->make('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sushitrash\Desktop\Aeternitas-System-V2-1\resources\views/hr/employee-personnel-files/hiring.blade.php ENDPATH**/ ?>