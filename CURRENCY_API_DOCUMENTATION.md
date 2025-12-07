# Currency Exchange API Documentation

## نظرة عامة
هذا API يستخدم **ExchangeRate-API** لتحويل العملات والحصول على أسعار الصرف.

## Base URL
```
/api/currency
```

## Endpoints

### 1. تحويل العملات
**POST** `/api/currency/convert`

تحويل مبلغ من عملة إلى أخرى.

**Request Body:**
```json
{
    "from": "USD",      // العملة المصدر (3 أحرف)
    "to": "SAR",        // العملة المستهدفة (3 أحرف)
    "amount": 100,      // المبلغ المراد تحويله
    "date": "2024-01-15" // (اختياري) تاريخ محدد للحصول على سعر تاريخي
}
```

**Response:**
```json
{
    "status": 200,
    "message": "Currency converted successfully",
    "data": {
        "items": {
            "from": "USD",
            "to": "SAR",
            "amount": 100,
            "converted_amount": 375.00,
            "rate": 3.75,
            "date": "2024-01-15"
        }
    }
}
```

**Example:**
```bash
curl -X POST http://your-domain.com/api/currency/convert \
  -H "Content-Type: application/json" \
  -d '{
    "from": "USD",
    "to": "SAR",
    "amount": 100
  }'
```

--
