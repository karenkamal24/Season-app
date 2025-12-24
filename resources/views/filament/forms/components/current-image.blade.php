@if(isset($imageUrl) && $imageUrl)
    <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center">
            <img 
                src="{{ $imageUrl }}" 
                alt="Current Icon"
                class="rounded-lg shadow-sm object-contain border border-gray-300 dark:border-gray-700"
                style="max-width: 120px; max-height: 120px; width: auto; height: auto;"
            />
        </div>
    </div>
@endif

