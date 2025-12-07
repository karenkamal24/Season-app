# ExchangeRate-API Setup Guide

## الإعداد

### 1. إضافة API Key إلى ملف .env

أضف السطر التالي إلى ملف `.env`:

```env
EXCHANGERATE_API_KEY=826eb98e5751d5255c4edfe1
```

### 2. تحديث ملف الإعدادات

تم إضافة الإعدادات في `config/services.php`:

```php
'exchangerate' => [
    'api_key' => env('EXCHANGERATE_API_KEY', '826eb98e5751d5255c4edfe1'),
    'base_url' => env('EXCHANGERATE_BASE_URL', 'https://v6.exchangerate-api.com/v6'),
],
```

### 3. مسح الكاش

بعد إضافة API Key، قم بمسح كاش الإعدادات:

```bash
php artisan config:cache
```

## API Endpoints

### Base URL
```
https://v6.exchangerate-api.com/v6/{API_KEY}
```

### Endpoints المستخدمة

1. **Pair Conversion**: `/pair/{FROM}/{TO}/{AMOUNT}`
   - للتحويل المباشر بين عملتين

2. **Latest Rates**: `/latest/{BASE}`
   - للحصول على أحدث أسعار الصرف لعملة أساسية

3. **Historical Rates**: `/history/{BASE}/{DATE}`
   - للحصول على أسعار تاريخية (YYYY-MM-DD)

4. **Supported Codes**: `/codes`
   - للحصول على قائمة جميع العملات المدعومة

## ملاحظات

- API Key الحالي: `826eb98e5751d5255c4edfe1`
- يمكن تغيير API Key من ملف `.env`
- جميع الطلبات يتم تخزينها مؤقتًا (caching) لتحسين الأداء
- يدعم أكثر من 160 عملة عالمية

## Testing

يمكنك تجربة API باستخدام Postman أو cURL:

```bash
# تحويل 100 USD إلى SAR
curl -X POST http://localhost:8000/api/currency/convert \
  -H "Content-Type: application/json" \
  -d '{"from":"USD","to":"SAR","amount":100}'

# الحصول على أحدث أسعار من USD
curl http://localhost:8000/api/currency/latest?from=USD

# قائمة العملات
curl http://localhost:8000/api/currency/currencies
```

