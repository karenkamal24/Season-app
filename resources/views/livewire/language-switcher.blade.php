<div class="flex items-center gap-2">
    <button 
        wire:click="switchLanguage('en')" 
        class="px-3 py-1.5 rounded {{ app()->getLocale() === 'en' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
        title="English"
    >
        EN
    </button>
    <button 
        wire:click="switchLanguage('ar')" 
        class="px-3 py-1.5 rounded {{ app()->getLocale() === 'ar' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
        title="العربية"
    >
        AR
    </button>
</div>
