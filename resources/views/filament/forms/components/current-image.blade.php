@php
    $viewData = $getViewData();
    $imageUrl = $viewData['imageUrl'] ?? null;
    $label = $viewData['label'] ?? 'Current Image';
    $removeFieldId = 'remove_icon_' . uniqid();
@endphp

@if($imageUrl)
    <div class="space-y-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $label }}
            </label>
            <button 
                type="button"
                onclick="
                    const form = this.closest('form');
                    // Try to find the toggle input by name
                    let toggleInput = form.querySelector('input[name=\"remove_icon\"]');
                    // If not found, try to find by data-field-name attribute (Filament uses this)
                    if (!toggleInput) {
                        toggleInput = form.querySelector('[data-field-name=\"remove_icon\"] input[type=\"checkbox\"]');
                    }
                    // If still not found, try to find any checkbox near a label containing 'remove_icon'
                    if (!toggleInput) {
                        const labels = form.querySelectorAll('label');
                        labels.forEach(label => {
                            if (label.textContent.includes('remove_icon') || label.getAttribute('for')?.includes('remove_icon')) {
                                const id = label.getAttribute('for');
                                if (id) {
                                    toggleInput = form.querySelector('#' + id);
                                }
                            }
                        });
                    }
                    if (toggleInput) {
                        toggleInput.checked = true;
                        // Trigger change event for Filament
                        toggleInput.dispatchEvent(new Event('change', { bubbles: true }));
                        this.closest('.space-y-3').querySelector('img').style.opacity = '0.5';
                        this.disabled = true;
                        this.textContent = '{{ $viewData['removeButtonText'] ?? 'سيتم الحذف' }}';
                        this.classList.add('bg-gray-500', 'cursor-not-allowed');
                        this.classList.remove('bg-red-600', 'hover:bg-red-700');
                    }
                "
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            >
                {{ $viewData['removeButtonText'] ?? 'حذف الصورة' }}
            </button>
        </div>
        <div class="relative inline-block">
            <img 
                src="{{ $imageUrl }}" 
                alt="{{ $label }}"
                class="max-w-xs max-h-64 rounded-lg shadow-md object-cover border border-gray-300 dark:border-gray-700 transition-opacity duration-200"
                style="max-width: 300px; max-height: 256px;"
            />
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            {{ $viewData['helperText'] ?? 'انقر على زر "حذف الصورة" أعلاه لحذف الصورة الحالية. سيتم الحذف عند حفظ النموذج.' }}
        </p>
    </div>
@endif

