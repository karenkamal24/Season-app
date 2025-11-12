<x-filament-panels::page>
    <div class="flex justify-center">
        <div class="flex gap-3 items-center">
            <span class="text-sm font-medium">{{ app()->getLocale() === 'ar' ? 'اختر اللغة:' : 'Select Language:' }}</span>
            
            <a 
                href="?locale=en" 
                wire:click.prevent="switchLanguage('en')"
                class="px-6 py-3 rounded-lg text-sm font-medium transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
            >
                🇬🇧 English
            </a>
            
            <a 
                href="?locale=ar" 
                wire:click.prevent="switchLanguage('ar')"
                class="px-6 py-3 rounded-lg text-sm font-medium transition-colors {{ app()->getLocale() === 'ar' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
            >
                🇸🇦 العربية
            </a>
        </div>
    </div>
</x-filament-panels::page>

