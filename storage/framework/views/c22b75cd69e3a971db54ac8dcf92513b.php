<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => '', 'backRoute' => null, 'backText' => 'Back']));

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

foreach (array_filter((['title', 'subtitle' => '', 'backRoute' => null, 'backText' => 'Back']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo e($title); ?></h1>
        <?php if($subtitle): ?>
            <p class="mt-1 text-sm text-gray-500"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>
    <?php if($backRoute): ?>
        <a href="<?php echo e($backRoute); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            <span class="hidden sm:inline"><?php echo e($backText); ?></span>
            <span class="sm:hidden">Back</span>
        </a>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\sushitrash\Desktop\Aeternitas-System-V2-1\resources\views/components/forms/page-header.blade.php ENDPATH**/ ?>