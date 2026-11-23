<?php

return [
    'alert_webhook_url' => env('SMART_BAG_AI_ALERT_WEBHOOK_URL'),
    'fallback_categories' => [
        ['id' => 1, 'name' => 'clothing', 'name_ar' => 'الملابس والأحذية', 'icon' => 'shirt'],
        ['id' => 2, 'name' => 'electronics', 'name_ar' => 'الأجهزة والشواحن', 'icon' => 'devices'],
        ['id' => 3, 'name' => 'documents', 'name_ar' => 'المستندات والتذاكر', 'icon' => 'file_text'],
        ['id' => 4, 'name' => 'medical', 'name_ar' => 'الأدوية والإسعافات', 'icon' => 'medical'],
        ['id' => 5, 'name' => 'personal_care', 'name_ar' => 'العناية الشخصية', 'icon' => 'care'],
    ],
];
