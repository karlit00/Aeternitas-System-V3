

<?php $__env->startSection('title', 'Employee Personnel Files'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Employee Personnel Files</h1>
                        <p class="mt-1 text-sm text-gray-600">Comprehensive employee records management system</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
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

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Employee</label>
                <input type="text" id="search-employee" placeholder="Search by name, ID, or position..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Department</label>
                <select id="filter-department" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    <?php $__currentLoopData = $employees->pluck('department.name', 'department.id')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Position</label>
                <select id="filter-position" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Positions</option>
                    <?php $__currentLoopData = $employees->pluck('position.name', 'position.id')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Employee Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300 personnel-file-card"
             data-employee-name="<?php echo e($employee->full_name); ?>"
             data-department-id="<?php echo e($employee->department_id); ?>"
             data-position-id="<?php echo e($employee->position_id); ?>">
            
            <!-- Employee Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg"><?php echo e($employee->full_name); ?></h3>
                        <p class="text-blue-100 text-sm"><?php echo e($employee->employee_id); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm"><?php echo e($employee->position->name ?? 'N/A'); ?></p>
                        <p class="text-xs text-blue-100"><?php echo e($employee->department->name ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <!-- File Categories -->
            <div class="p-4 space-y-3">
                <!-- Hiring/Onboarding -->
                <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Hiring & Onboarding</h4>
                                <p class="text-xs text-gray-500">Job applications, contracts, checklists</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('hr.employee-personnel-files.hiring', $employee->id)); ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Files →
                        </a>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-id-card text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Employment Details</h4>
                                <p class="text-xs text-gray-500">Job descriptions, tax forms, salary history</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('hr.employee-personnel-files.employment', $employee->id)); ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Files →
                        </a>
                    </div>
                </div>

                <!-- Performance & Development -->
                <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Performance & Development</h4>
                                <p class="text-xs text-gray-500">Evaluations, training, disciplinary actions</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('hr.employee-personnel-files.performance', $employee->id)); ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Files →
                        </a>
                    </div>
                </div>

                <!-- Offboarding -->
                <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-sign-out-alt text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Offboarding</h4>
                                <p class="text-xs text-gray-500">Resignation letters, exit interviews</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('hr.employee-personnel-files.offboarding', $employee->id)); ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Files →
                        </a>
                    </div>
                </div>

                <!-- Confidential Files -->
                <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Confidential Files</h4>
                                <p class="text-xs text-gray-500">Medical records, background checks</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('hr.employee-personnel-files.confidential', $employee->id)); ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Files →
                        </a>
                    </div>
                </div>
            </div>

            <!-- File Statistics -->
            <div class="bg-gray-50 px-4 py-3 border-t">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Last Updated: <?php echo e($employee->updated_at->diffForHumans()); ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($employees->isEmpty()): ?>
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Employee Records Found</h3>
        <p class="text-gray-600">No employee personnel files are available at this time.</p>
    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-employee');
    const departmentFilter = document.getElementById('filter-department');
    const positionFilter = document.getElementById('filter-position');
    const cards = document.querySelectorAll('.personnel-file-card');

    function filterEmployees() {
        const searchTerm = searchInput.value.toLowerCase();
        const departmentId = departmentFilter.value;
        const positionId = positionFilter.value;

        cards.forEach(card => {
            const employeeName = card.dataset.employeeName.toLowerCase();
            const cardDepartmentId = card.dataset.departmentId;
            const cardPositionId = card.dataset.positionId;

            const matchesSearch = employeeName.includes(searchTerm);
            const matchesDepartment = !departmentId || cardDepartmentId === departmentId;
            const matchesPosition = !positionId || cardPositionId === positionId;

            if (matchesSearch && matchesDepartment && matchesPosition) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterEmployees);
    departmentFilter.addEventListener('change', filterEmployees);
    positionFilter.addEventListener('change', filterEmployees);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard-base', ['user' => auth()->user(), 'activeRoute' => 'hr.employee-personnel-files'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sushitrash\Desktop\Aeternitas-System-V2-1\resources\views/hr/employee-personnel-files/index.blade.php ENDPATH**/ ?>