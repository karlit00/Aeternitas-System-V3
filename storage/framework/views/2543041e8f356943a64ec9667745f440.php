<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'confirmationModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmClass' => 'bg-blue-600 hover:bg-blue-700',
    'icon' => 'exclamation-circle',
    'iconColor' => 'text-blue-600',
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
    'id' => 'confirmationModal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmClass' => 'bg-blue-600 hover:bg-blue-700',
    'icon' => 'exclamation-circle',
    'iconColor' => 'text-blue-600',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<!-- Confirmation Modal -->
<div id="<?php echo e($id); ?>" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300" style="opacity: 0; pointer-events: none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95" style="transform: scale(0.95); pointer-events: auto;">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                <i class="fas fa-<?php echo e($icon); ?> <?php echo e($iconColor); ?> text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900"><?php echo e($title); ?></h3>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-4">
            <p class="text-gray-600 text-sm leading-relaxed" id="<?php echo e($id); ?>-message"><?php echo e($message); ?></p>
            <?php echo e($slot); ?>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end bg-gray-50 rounded-b-xl">
            <button type="button" onclick="closeConfirmationModal('<?php echo e($id); ?>')" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors font-medium">
                <?php echo e($cancelText); ?>

            </button>
            <button type="button" id="<?php echo e($id); ?>-confirm" class="px-4 py-2 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors font-medium <?php echo e($confirmClass); ?>">
                <i class="fas fa-check mr-2"></i>
                <?php echo e($confirmText); ?>

            </button>
        </div>
    </div>
</div>

<script>
// Only define functions once
if (typeof window.showConfirmationModal === 'undefined') {
    console.log('[Modal Component] Defining showConfirmationModal function');
    
    // Show confirmation modal
    window.showConfirmationModal = function(modalId, onConfirm, message = null) {
        console.log('[showConfirmationModal] Called with:', { modalId, hasCallback: typeof onConfirm === 'function', hasMessage: !!message });
        
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error('[showConfirmationModal] Modal not found:', modalId);
            return;
        }
        
        console.log('[showConfirmationModal] Modal element found');
        
        // Update message if provided
        if (message) {
            const messageEl = document.getElementById(modalId + '-message');
            if (messageEl) {
                messageEl.innerHTML = message; // Use innerHTML to render HTML
                console.log('[showConfirmationModal] Message updated');
            } else {
                console.error('[showConfirmationModal] Message element not found:', modalId + '-message');
            }
        }
        
        // Show modal with animation
        console.log('[showConfirmationModal] Showing modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.pointerEvents = 'auto';
        setTimeout(() => {
            modal.style.opacity = '1';
            const modalContent = modal.querySelector('div');
            if (modalContent) {
                modalContent.style.transform = 'scale(1)';
            }
        }, 10);
        
        // Set up confirm button
        const confirmBtn = document.getElementById(modalId + '-confirm');
        if (confirmBtn) {
            console.log('[showConfirmationModal] Confirm button found, setting up handler');
            confirmBtn.onclick = () => {
                console.log('[showConfirmationModal] Confirm button clicked');
                window.closeConfirmationModal(modalId);
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            };
        } else {
            console.error('[showConfirmationModal] Confirm button not found:', modalId + '-confirm');
        }
        
        // Close on escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                window.closeConfirmationModal(modalId);
                document.removeEventListener('keydown', handleEscape);
            }
        };
        document.addEventListener('keydown', handleEscape);
        
        // Close on outside click
        modal.onclick = (e) => {
            if (e.target === modal) {
                window.closeConfirmationModal(modalId);
            }
        };
        
        console.log('[showConfirmationModal] Setup complete');
    };

    // Close confirmation modal
    window.closeConfirmationModal = function(modalId) {
        console.log('[closeConfirmationModal] Called for:', modalId);
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        modal.style.opacity = '0';
        modal.style.pointerEvents = 'none';
        const modalContent = modal.querySelector('div');
        if (modalContent) {
            modalContent.style.transform = 'scale(0.95)';
        }
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    };
    
    console.log('[Modal Component] Functions defined successfully. window.showConfirmationModal is now available.');
} else {
    console.log('[Modal Component] Functions already defined, skipping');
}
</script>
<?php /**PATH C:\xampp\htdocs\Aeternitas-System-V2\resources\views/components/confirmation-modal.blade.php ENDPATH**/ ?>