

<?php $__env->startSection('title', 'Employee Info & Documents'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Employee Info & Documents</h1>
                        <p class="mt-1 text-sm text-gray-600">View employee information and manage documents</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Documents
                        </a>
                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Company Selector (for admin/hr only) -->
                <?php if(isset($currentCompany) && $currentCompany): ?>
                <div class="mt-4">
                    <div class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg">
                        <i class="fas fa-building mr-2"></i>
                        <span class="font-medium"><?php echo e($currentCompany->name); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filter Employees</h3>
            </div>
            <div class="p-6">
                <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select id="department" name="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Departments</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($department->id); ?>" <?php echo e(request('department_id') == $department->id ? 'selected' : ''); ?>>
                                    <?php echo e($department->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Employment Status</label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            <option value="on-leave" <?php echo e(request('status') == 'on-leave' ? 'selected' : ''); ?>>On Leave</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="flex">
                            <input type="text" id="search" name="search" value="<?php echo e(request('search')); ?>" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Search by name, ID, or department">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-r-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="button" onclick="clearFilters()" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employees List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Employee Information</h3>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600"><?php echo e($employees->total()); ?> employee(s) found</span>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-file-alt text-blue-500"></i>
                        <span>Documents</span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                <?php echo e(substr($employee->first_name, 0, 1)); ?><?php echo e(substr($employee->last_name, 0, 1)); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($employee->full_name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($employee->employee_id); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($employee->department->name ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo e($employee->position ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php if($employee->status == 'active'): ?> bg-green-100 text-green-800
                                    <?php elseif($employee->status == 'on-leave'): ?> bg-yellow-100 text-yellow-800
                                    <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                    <?php echo e(ucfirst($employee->status ?? 'active')); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-file mr-1"></i>
                                        <?php echo e($employee->documents_count ?? 0); ?> documents
                                    </span>
                                    <?php if($employee->documents_count > 0): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Has Documents
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewEmployeeDocuments(<?php echo e($employee->id); ?>)" 
                                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-folder mr-2"></i>
                                        View Documents
                                    </button>
                                    <a href="<?php echo e(route('employees.show', $employee)); ?>" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Profile
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-3"></i>
                                    <p class="text-lg">No employees found</p>
                                    <p class="text-sm mt-1">Try adjusting your filters or search criteria</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($employees->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($employees->withQueryString()->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Employee Documents Modal -->
<div id="employeeDocumentsModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-folder text-blue-600 text-xl mr-3"></i>
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Employee Documents</h3>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="exportEmployeeDocuments(currentEmployeeId)" 
                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-export mr-2"></i>
                    Export Documents
                </button>
                <button onclick="closeDocumentsModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div id="employeeDocumentsContent">
                <!-- Employee documents will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i>
                    <p class="mt-2 text-gray-600">Loading employee documents...</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between">
            <div class="flex items-center text-sm text-gray-500">
                <i class="fas fa-info-circle mr-2"></i>
                <span id="documentsCount">0 documents</span>
            </div>
            <div class="flex space-x-2">
                <button onclick="closeDocumentsModal()" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Close
                </button>
                <a id="viewFullProfileBtn" href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-user mr-2"></i>
                    View Full Profile
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let currentEmployeeId = null;
    
    // Auto-submit form when department or status changes
    document.getElementById('department').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    document.getElementById('status').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    // Clear filters function
    function clearFilters() {
        document.getElementById('department').value = '';
        document.getElementById('status').value = '';
        document.getElementById('search').value = '';
        document.getElementById('filterForm').submit();
    }
    
    // View employee documents in modal
    function viewEmployeeDocuments(employeeId) {
        currentEmployeeId = employeeId;
        
        // Show modal
        const modal = document.getElementById('employeeDocumentsModal');
        modal.classList.remove('hidden');
        
        // Load employee documents
        fetch(`/employee-info/${employeeId}/documents`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('employeeDocumentsContent').innerHTML = data.html;
                    document.getElementById('modalTitle').textContent = data.employee.full_name + ' - Documents';
                    document.getElementById('documentsCount').textContent = data.employee.full_name + ' - ' + data.employee.employee_id;
                    
                    // Update the View Full Profile button
                    const viewProfileBtn = document.getElementById('viewFullProfileBtn');
                    viewProfileBtn.href = `/employees/${employeeId}`;
                    
                    // Add export button functionality
                    const exportBtn = document.querySelector('#employeeDocumentsModal .bg-green-600');
                    if (exportBtn) {
                        exportBtn.onclick = function() {
                            exportEmployeeDocuments(employeeId);
                        };
                    }
                } else {
                    showError('Failed to load employee documents');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error loading employee documents: ' + error.message);
            });
    }
    
    // Export employee documents
    function exportEmployeeDocuments(employeeId) {
        // Create a simple export of document list
        const employeeName = document.getElementById('modalTitle').textContent.replace(' - Documents', '');
        const documentsList = document.querySelectorAll('#employeeDocumentsContent .document-item');
        
        if (documentsList.length === 0) {
            alert('No documents to export for this employee.');
            return;
        }
        
        // Create CSV content
        let csvContent = "Employee Name,Employee ID,Document Name,Document Type,Uploaded Date\n";
        
        documentsList.forEach(doc => {
            const name = doc.querySelector('.document-name').textContent;
            const type = doc.querySelector('.document-type').textContent;
            const date = doc.querySelector('.document-date').textContent;
            csvContent += `"${employeeName}","${document.getElementById('documentsCount').textContent.split(' - ')[1]}","${name}","${type}","${date}"\n`;
        });
        
        // Download CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `${employeeName.replace(/\s+/g, '_')}_documents.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Close documents modal
    function closeDocumentsModal() {
        document.getElementById('employeeDocumentsModal').classList.add('hidden');
    }
    
    // Show error message
    function showError(message) {
        document.getElementById('employeeDocumentsContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                <p class="mt-2 text-gray-600">${message}</p>
            </div>
        `;
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDocumentsModal();
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('employeeDocumentsModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeDocumentsModal();
        }
    });
    
    // Add delete document functionality
    function deleteDocument(documentId, documentName) {
        if (confirm(`Are you sure you want to delete "${documentName}"? This action cannot be undone.`)) {
            fetch(`/employee-info/document/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the document from the DOM
                    const documentElement = document.querySelector(`[data-document-id="${documentId}"]`);
                    if (documentElement) {
                        documentElement.remove();
                    }
                    
                    // Show success message
                    showSuccess('Document deleted successfully');
                    
                    // Update document count
                    const countElement = document.querySelector('.documents-count');
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent);
                        countElement.textContent = currentCount - 1;
                    }
                } else {
                    showError('Failed to delete document: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error deleting document');
            });
        }
    }
    
    // Show success message
    function showSuccess(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'employee-info.index'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sushitrash\Desktop\Aeternitas-System-V2-1\resources\views/employee-info/index.blade.php ENDPATH**/ ?>