<?php

return [
    // Bag Management
    'bag' => 'حقيبة',
    'bags' => 'الحقائب',
    'smart_bag' => 'حقيبة ذكية',
    'smart_bags' => 'الحقائب الذكية',
    'create_bag' => 'إنشاء حقيبة',
    'edit_bag' => 'تعديل الحقيبة',
    'delete_bag' => 'حذف الحقيبة',
    'bag_details' => 'تفاصيل الحقيبة',
    'my_bags' => 'حقائبي',

    // Bag Fields
    'bag_name' => 'اسم الحقيبة',
    'trip_type' => 'نوع الرحلة',
    'duration' => 'مدة الرحلة',
    'destination' => 'الوجهة',
    'departure_date' => 'تاريخ المغادرة',
    'max_weight' => 'الحد الأقصى للوزن',
    'total_weight' => 'الوزن الإجمالي',
    'remaining_weight' => 'الوزن المتبقي',
    'weight_percentage' => 'نسبة الوزن',
    'status' => 'الحالة',
    'preferences' => 'التفضيلات',

    // Trip Types
    'trip_types' => [
        'عمل' => 'رحلة عمل',
        'سياحة' => 'رحلة سياحية',
        'عائلية' => 'رحلة عائلية',
        'علاج' => 'رحلة علاجية',
    ],

    // Statuses
    'statuses' => [
        'draft' => 'مسودة',
        'in_progress' => 'قيد التجهيز',
        'completed' => 'مكتملة',
        'cancelled' => 'ملغاة',
    ],

    // Bag Items
    'item' => 'غرض',
    'items' => 'الأغراض',
    'add_item' => 'إضافة غرض',
    'edit_item' => 'تعديل الغرض',
    'delete_item' => 'حذف الغرض',
    'item_name' => 'اسم الغرض',
    'item_weight' => 'الوزن',
    'item_category' => 'الفئة',
    'item_quantity' => 'الكمية',
    'item_notes' => 'ملاحظات',
    'essential' => 'ضروري',
    'packed' => 'محزوم',
    'unpacked' => 'غير محزوم',

    // Categories
    'categories' => [
        'ملابس' => 'ملابس',
        'أحذية' => 'أحذية',
        'إلكترونيات' => 'إلكترونيات',
        'أدوية وعناية' => 'أدوية وعناية',
        'مستندات' => 'مستندات',
        'أخرى' => 'أخرى',
    ],

    // Analysis
    'analysis' => 'التحليل',
    'analyses' => 'التحليلات',
    'analyze_bag' => 'تحليل الحقيبة',
    'latest_analysis' => 'آخر تحليل',
    'analysis_history' => 'سجل التحليلات',
    'missing_items' => 'أغراض ناقصة',
    'extra_items' => 'أغراض زائدة',
    'weight_optimization' => 'تحسين الوزن',
    'additional_suggestions' => 'اقتراحات إضافية',
    'smart_alert' => 'تنبيه ذكي',
    'confidence_score' => 'درجة الثقة',
    'processing_time' => 'وقت المعالجة',
    'analyzed_at' => 'تم التحليل في',

    // Priority Levels
    'priority' => [
        'high' => 'عالية',
        'medium' => 'متوسطة',
        'low' => 'منخفضة',
    ],

    // Severity Levels
    'severity' => [
        'high' => 'عالية',
        'medium' => 'متوسطة',
        'low' => 'منخفضة',
    ],

    // Messages
    'messages' => [
        'bag_created' => 'تم إنشاء الحقيبة بنجاح',
        'bag_updated' => 'تم تحديث الحقيبة بنجاح',
        'bag_deleted' => 'تم حذف الحقيبة بنجاح',
        'item_added' => 'تم إضافة الغرض بنجاح',
        'item_updated' => 'تم تحديث الغرض بنجاح',
        'item_deleted' => 'تم حذف الغرض بنجاح',
        'item_packed' => 'تم تحديث حالة التحزيم',
        'bag_analyzed' => 'تم تحليل الحقيبة بنجاح',
        'empty_bag' => 'لا يمكن تحليل حقيبة فارغة',
        'no_analysis' => 'لا يوجد تحليل لهذه الحقيبة',
        'no_alerts' => 'لا توجد تنبيهات لهذه الحقيبة',
        'already_analyzed' => 'تم تحليل الحقيبة مؤخراً',
    ],

    // Alerts
    'alerts' => [
        'medicines_missing' => 'حقيبة الأدوية غير مكتملة',
        'documents_missing' => 'لا توجد وثائق عمل في الحقيبة',
        'overweight' => 'الوزن قريب من الحد الأقصى',
        'unpacked_essentials' => 'يوجد أغراض ضرورية غير محزومة',
    ],

    // Actions
    'actions' => [
        'review_medicines' => 'راجع الأدوية الأساسية',
        'review_documents' => 'راجع المستندات المطلوبة للاجتماعات',
        'reduce_weight' => 'راجع الأغراض وقلل الوزن',
        'pack_essentials' => 'راجع الأغراض الضرورية وقم بتحزيمها',
    ],

    // Time
    'days_until_departure' => 'أيام حتى المغادرة',
    'hours_until_departure' => 'ساعات حتى المغادرة',
    'minutes_until_departure' => 'دقائق حتى المغادرة',
    'time_remaining' => 'الوقت المتبقي',

    // Units
    'kg' => 'كجم',
    'days' => 'أيام',
    'hours' => 'ساعات',
    'minutes' => 'دقائق',
];

