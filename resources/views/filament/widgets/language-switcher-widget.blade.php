<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-center gap-3 items-center">
            <span class="text-sm font-medium">{{ app()->getLocale() === 'ar' ? 'اللغة:' : 'Language:' }}</span>
            
            <a 
                href="?locale=en" 
                onclick="event.preventDefault(); @this.call('switchLanguage', 'en')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ app()->getLocale() === 'en' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
            >
                English
            </a>
            
            <a 
                href="?locale=ar" 
                onclick="event.preventDefault(); @this.call('switchLanguage', 'ar')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ app()->getLocale() === 'ar' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}"
            >
                العربية
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

