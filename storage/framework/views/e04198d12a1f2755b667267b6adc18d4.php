<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'rows' => null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'rows' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div>
    <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700 mb-2">
        <?php echo e($label); ?>

        <?php if($required): ?>
            <span class="text-red-500">*</span>
        <?php endif; ?>
    </label>
    
    <?php if($type === 'textarea'): ?>
        <textarea 
            name="<?php echo e($name); ?>" 
            id="<?php echo e($name); ?>" 
            <?php echo e($required ? 'required' : ''); ?>

            <?php if($rows): ?> rows="<?php echo e($rows); ?>" <?php endif; ?>
            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="<?php echo e($placeholder); ?>"
            <?php echo e($attributes); ?>

        ><?php echo e(old($name, $value)); ?></textarea>
    <?php else: ?>
        <input 
            type="<?php echo e($type); ?>" 
            name="<?php echo e($name); ?>" 
            id="<?php echo e($name); ?>" 
            value="<?php echo e(old($name, $value)); ?>"
            <?php echo e($required ? 'required' : ''); ?>

            <?php if($maxlength): ?> maxlength="<?php echo e($maxlength); ?>" <?php endif; ?>
            <?php if($min !== null): ?> min="<?php echo e($min); ?>" <?php endif; ?>
            <?php if($max !== null): ?> max="<?php echo e($max); ?>" <?php endif; ?>
            <?php if($step): ?> step="<?php echo e($step); ?>" <?php endif; ?>
            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            placeholder="<?php echo e($placeholder); ?>"
            <?php echo e($attributes); ?>

        >
    <?php endif; ?>
    
    <?php $__errorArgs = [$name];
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
<?php /**PATH C:\internship\Aeternitas-System-V2\resources\views/components/forms/input.blade.php ENDPATH**/ ?>