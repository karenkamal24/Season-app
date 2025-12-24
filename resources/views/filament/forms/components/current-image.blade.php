@php
    $viewData = $getViewData();
    $imageUrl = $viewData['imageUrl'] ?? null;
    $label = $viewData['label'] ?? 'Current Image';
@endphp

@if($imageUrl)
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
        <div class="relative inline-block">
            <img 
                src="{{ $imageUrl }}" 
                alt="{{ $label }}"
                class="max-w-xs max-h-64 rounded-lg shadow-md object-cover border border-gray-300 dark:border-gray-700"
                style="max-width: 300px; max-height: 256px;"
            />
        </div>
    </div>
@endif

