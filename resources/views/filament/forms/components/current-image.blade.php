@if(isset($imageUrl) && $imageUrl)
    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center">
            <img 
                src="{{ $imageUrl }}" 
                alt="Current Icon"
                class="max-w-full max-h-64 rounded-lg shadow-md object-contain border border-gray-300 dark:border-gray-700"
                style="max-width: 300px; max-height: 256px;"
            />
        </div>
    </div>
@endif

