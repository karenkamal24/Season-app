<?php

/**
 * ملف اختبار سريع لـ Gemini API
 *
 * الاستخدام:
 * php test_gemini.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 اختبار Gemini API\n";
echo "==================\n\n";

// 1. التحقق من الإعدادات
echo "1️⃣ التحقق من إعدادات Gemini...\n";
$apiKey = config('services.gemini.api_key');
$model = config('services.gemini.model');

if (empty($apiKey)) {
    echo "❌ خطأ: GEMINI_API_KEY غير موجود في .env\n";
    exit(1);
}

echo "✅ API Key موجود\n";
echo "✅ Model: {$model}\n\n";

// 2. اختبار بسيط
echo "2️⃣ اختبار الاتصال بـ Gemini API...\n";

try {
    $geminiService = app(\App\Services\GeminiService::class);

    $result = $geminiService->search("مرحباً، قل لي مرحباً بالعربية فقط");

    echo "✅ نجح الاتصال!\n";
    echo "📝 Response: " . substr($result['text'], 0, 100) . "...\n";
    echo "🤖 Model: {$result['model']}\n\n";

    echo "✅ كل شيء يعمل بشكل صحيح!\n";
    echo "\n💡 الآن يمكنك استخدام الـ API من Postman أو curl\n";
    echo "📖 راجع ملف TEST_GEMINI_API.md للتعليمات الكاملة\n";

} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "\n💡 تأكد من:\n";
    echo "   - وجود GEMINI_API_KEY في .env\n";
    echo "   - أن API Key صحيح\n";
    echo "   - الاتصال بالإنترنت\n";
    exit(1);
}

