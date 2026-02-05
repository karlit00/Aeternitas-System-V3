<?php $__env->startSection('title', 'Create Schedule'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Create Employee Schedule</h1>
                        <?php if($employee): ?>
                            <p class="mt-1 text-sm text-gray-600">Add a new work schedule for <?php echo e($employee->full_name); ?></p>
                        <?php else: ?>
                            <p class="mt-1 text-sm text-gray-600">Add a new work schedule for an employee</p>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo e(isset($currentFilters) ? route('schedule-v2.index', array_filter($currentFilters)) : route('schedule-v2.index')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Schedules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="<?php echo e(route('schedule-v2.store')); ?>" method="POST" class="p-6 space-y-6">
                <?php echo csrf_field(); ?>
                
                <!-- Hidden inputs to preserve filter state -->
                <?php if(isset($currentFilters)): ?>
                    <?php $__currentLoopData = $currentFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($value): ?>
                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Employee Selection Panel -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Employee Selection
                            </h4>
                            
                            <?php if($employee): ?>
                                <!-- Pre-selected Employee Info (when coming from direct link) -->
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-lg font-medium text-blue-600">
                                                    <?php echo e(substr($employee->first_name, 0, 1)); ?><?php echo e(substr($employee->last_name, 0, 1)); ?>

                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900"><?php echo e($employee->full_name); ?></h3>
                                            <p class="text-sm text-gray-600"><?php echo e($employee->position); ?> - <?php echo e($employee->department->name); ?></p>
                                            <p class="text-sm text-blue-600 font-medium">
                                                <i class="fas fa-check-circle mr-1"></i>Creating schedule for this employee
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden inputs for pre-selected employee -->
                                <input type="hidden" name="employee_id" value="<?php echo e($employee->id); ?>">
                                <input type="hidden" name="department_id" value="<?php echo e($employee->department_id); ?>">
                            <?php else: ?>
                                <!-- Department Selection (only show when no employee pre-selected) -->
                                <div class="space-y-4">
                                    <div>
                                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-building mr-1"></i>Department
                                        </label>
                                        <select name="department_id" id="department_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">Select a department</option>
                                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($dept->id); ?>" <?php echo e(old('department_id') == $dept->id ? 'selected' : ''); ?>>
                                                    <?php echo e($dept->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <!-- Employee Selection (only show when no employee pre-selected and user is not an employee) -->
                                    <?php if($user->role !== 'employee'): ?>
                                    <div>
                                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-users mr-1"></i>Employee
                                        </label>
                                        <select name="employee_id" id="employee_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value="">Select an employee</option>
                                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($emp->id); ?>" data-department="<?php echo e($emp->department_id); ?>" <?php echo e(old('employee_id') == $emp->id ? 'selected' : ''); ?>>
                                                    <?php echo e($emp->full_name); ?> - <?php echo e($emp->department->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <?php else: ?>
                                    <!-- For employees, use their own ID -->
                                    <input type="hidden" name="employee_id" value="<?php echo e($user->employee->id ?? ''); ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Schedule Details Panel -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-green-600"></i>
                                Schedule Details
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Date -->
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar-day mr-1"></i>Date
                                    </label>
                                    <input type="date" name="date" id="date" value="<?php echo e(old('date', $date)); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tasks mr-1"></i>Status
                                    </label>
                                    <select name="status" id="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value="">Select status</option>
                                        <option value="Working" <?php echo e(old('status') == 'Working' ? 'selected' : ''); ?>>Working</option>
                                        <option value="Day Off" <?php echo e(old('status') == 'Day Off' ? 'selected' : ''); ?>>Day Off</option>
                                        <option value="Leave" <?php echo e(old('status') == 'Leave' ? 'selected' : ''); ?>>Leave</option>
                                        <option value="Absent" <?php echo e(old('status') == 'Absent' ? 'selected' : ''); ?>>Absent</option>
                                        <option value="Regular Holiday" <?php echo e(old('status') == 'Regular Holiday' ? 'selected' : ''); ?>>Regular Holiday</option>
                                        <option value="Special Holiday" <?php echo e(old('status') == 'Special Holiday' ? 'selected' : ''); ?>>Special Holiday</option>
                                        <option value="Overtime" <?php echo e(old('status') == 'Overtime' ? 'selected' : ''); ?>>Overtime</option>
                                    </select>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <!-- Time In/Out (only show for working status) -->
                                <div id="timeFields" class="grid grid-cols-2 gap-4" style="display: none;">
                                    <div>
                                        <label for="time_in" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-clock mr-1"></i>Time In
                                        </label>
                                        <input type="time" name="time_in" id="time_in" value="<?php echo e(old('time_in')); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['time_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php $__errorArgs = ['time_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div>
                                        <label for="time_out" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-clock mr-1"></i>Time Out
                                        </label>
                                        <input type="time" name="time_out" id="time_out" value="<?php echo e(old('time_out')); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['time_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php $__errorArgs = ['time_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-sticky-note mr-1"></i>Notes (Optional)
                                    </label>
                                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Add any notes about this schedule..."><?php echo e(old('notes')); ?></textarea>
                                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="<?php echo e(isset($currentFilters) ? route('schedule-v2.index', array_filter($currentFilters)) : route('schedule-v2.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter employees based on selected department (only if department dropdown exists)
const departmentSelect = document.getElementById('department_id');
if (departmentSelect) {
    departmentSelect.addEventListener('change', function() {
        const selectedDepartmentId = this.value;
        const employeeSelect = document.getElementById('employee_id');
        const employeeOptions = employeeSelect.querySelectorAll('option[data-department]');
        
        // Reset employee selection
        employeeSelect.value = '';
        
        // Show/hide employee options based on department
        employeeOptions.forEach(option => {
            if (selectedDepartmentId === '' || option.getAttribute('data-department') === selectedDepartmentId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });
}

// Show/hide time fields based on status
document.getElementById('status').addEventListener('change', function() {
    const timeFields = document.getElementById('timeFields');
    const timeInField = document.getElementById('time_in');
    const timeOutField = document.getElementById('time_out');
    
    if (this.value === 'Working' || this.value === 'Overtime' || this.value === 'Regular Holiday' || this.value === 'Special Holiday' || this.value === 'Day Off' || this.value === 'Leave') {
        timeFields.style.display = 'grid';
        // Only require time fields for Working and Overtime
        if (this.value === 'Working' || this.value === 'Overtime') {
            timeInField.required = true;
            timeOutField.required = true;
        } else {
            timeInField.required = false;
            timeOutField.required = false;
        }
    } else {
        timeFields.style.display = 'none';
        timeInField.required = false;
        timeOutField.required = false;
        timeInField.value = '';
        timeOutField.value = '';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize department filter
    const departmentSelect = document.getElementById('department_id');
    if (departmentSelect.value) {
        departmentSelect.dispatchEvent(new Event('change'));
    }
    
    // Initialize time fields visibility
    const statusSelect = document.getElementById('status');
    if (statusSelect.value === 'Working' || statusSelect.value === 'Overtime' || statusSelect.value === 'Regular Holiday' || statusSelect.value === 'Special Holiday' || statusSelect.value === 'Day Off' || statusSelect.value === 'Leave') {
        document.getElementById('timeFields').style.display = 'grid';
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard-base', ['user' => $user, 'activeRoute' => 'attendance.timekeeping'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\internship\Aeternitas-System-V2\resources\views/attendance/schedule-v2/create.blade.php ENDPATH**/ ?>