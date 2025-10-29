<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشروط والأحكام - Season App</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #092C4C 0%, #0a3d63 100%);
            color: #212121;
            line-height: 1.8;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #F8F9FA;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #092C4C 0%, #0d4470 100%);
            padding: 60px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(242, 153, 74, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .header h1 {
            color: white;
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header .subtitle {
            color: #F2994A;
            font-size: 18px;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #F2994A 0%, #f3a75e 100%);
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            box-shadow: 0 10px 30px rgba(242, 153, 74, 0.3);
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 50px 40px;
        }

        .section {
            margin-bottom: 35px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .section h2 {
            color: #092C4C;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #F2994A;
            display: inline-block;
        }

        .section h3 {
            color: #092C4C;
            font-size: 20px;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 12px;
        }

        .section p {
            color: #212121;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .section ul, .section ol {
            margin: 15px 0 15px 30px;
            color: #212121;
        }

        .section li {
            margin-bottom: 10px;
            padding-right: 10px;
        }

        .highlight {
            background: linear-gradient(120deg, rgba(242, 153, 74, 0.1) 0%, rgba(242, 153, 74, 0.2) 100%);
            padding: 20px;
            border-radius: 12px;
            border-right: 4px solid #F2994A;
            margin: 20px 0;
        }

        .info-box {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin: 25px 0;
        }

        .footer {
            background: #092C4C;
            color: white;
            padding: 30px 40px;
            text-align: center;
        }

        .footer p {
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .footer a {
            color: #F2994A;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: #f3a75e;
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            background: #F2994A;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .back-btn {
            display: inline-block;
            background: linear-gradient(135deg, #F2994A 0%, #f3a75e 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(242, 153, 74, 0.3);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(242, 153, 74, 0.4);
        }

        @media (max-width: 768px) {
            .header {
                padding: 40px 20px;
            }

            .header h1 {
                font-size: 32px;
            }

            .content {
                padding: 30px 20px;
            }

            .section h2 {
                font-size: 22px;
            }

            .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛡️</div>
            <h1>الشروط والأحكام</h1>
            <p class="subtitle">تطبيق Season للأمان والمتابعة العائلية</p>
        </div>

        <div class="content">
            <div class="highlight">
                <p><strong>📅 تاريخ آخر تحديث:</strong> {{ date('Y/m/d') }}</p>
                <p>مرحباً بك في تطبيق Season. يرجى قراءة هذه الشروط والأحكام بعناية قبل استخدام التطبيق.</p>
            </div>

            <div class="section">
                <h2>1. القبول والموافقة</h2>
                <p>باستخدامك لتطبيق Season، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا كنت لا توافق على أي جزء من هذه الشروط، يرجى عدم استخدام التطبيق.</p>
                <div class="info-box">
                    <p>✓ استخدام التطبيق يعني موافقتك الكاملة على جميع الشروط</p>
                    <p>✓ يحق لنا تحديث هذه الشروط في أي وقت</p>
                    <p>✓ استمرارك في استخدام التطبيق يعني قبولك للتحديثات</p>
                </div>
            </div>

            <div class="section">
                <h2>2. وصف الخدمة</h2>
                <p>تطبيق Season هو تطبيق للأمان والمتابعة العائلية يوفر الميزات التالية:</p>
                <ul>
                    <li>📍 تتبع الموقع الجغرافي للأعضاء في الوقت الفعلي</li>
                    <li>👥 إنشاء مجموعات عائلية أو مجموعات الأصدقاء</li>
                    <li>🔔 تنبيهات عند خروج الأعضاء من نطاق الأمان المحدد</li>
                    <li>🆘 نظام تنبيهات الطوارئ (SOS)</li>
                    <li>🌍 مشاركة الموقع مع أفراد المجموعة</li>
                </ul>
            </div>

            <div class="section">
                <h2>3. متطلبات الاستخدام</h2>
                <h3>3.1 الأهلية</h3>
                <p>يجب أن يكون عمرك 13 عاماً على الأقل لاستخدام هذا التطبيق. إذا كنت دون 18 عاماً، يجب عليك الحصول على موافقة ولي الأمر.</p>

                <h3>3.2 الحساب الشخصي</h3>
                <ul>
                    <li>يجب تقديم معلومات دقيقة وكاملة عند التسجيل</li>
                    <li>أنت مسؤول عن الحفاظ على سرية كلمة المرور الخاصة بك</li>
                    <li>يجب إخطارنا فوراً بأي استخدام غير مصرح به لحسابك</li>
                    <li>لا يجوز مشاركة حسابك مع الآخرين</li>
                </ul>

                <h3>3.3 الأذونات المطلوبة</h3>
                <div class="info-box">
                    <p><strong>للاستفادة الكاملة من التطبيق، نحتاج إلى:</strong></p>
                    <ul>
                        <li>📍 الوصول إلى موقعك الجغرافي</li>
                        <li>🔔 إرسال الإشعارات</li>
                        <li>📷 الوصول إلى الكاميرا (لمسح رموز QR)</li>
                        <li>📶 الاتصال بالإنترنت</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>4. الاستخدام المقبول</h2>
                <p><strong>يتعهد المستخدم بما يلي:</strong></p>
                <ol>
                    <li>استخدام التطبيق للأغراض المشروعة فقط</li>
                    <li>عدم إساءة استخدام ميزة تتبع الموقع</li>
                    <li>احترام خصوصية الآخرين</li>
                    <li>عدم محاولة اختراق أو تعطيل التطبيق</li>
                    <li>عدم استخدام التطبيق لأي أنشطة غير قانونية</li>
                </ol>
            </div>

            <div class="section">
                <h2>5. بيانات الموقع</h2>
                <div class="highlight">
                    <p><strong>⚠️ تنويه مهم:</strong></p>
                    <p>يتم جمع بيانات الموقع فقط عندما تكون عضواً نشطاً في مجموعة. يمكنك إيقاف مشاركة موقعك في أي وقت بمغادرة المجموعة أو إلغاء الأذونات من إعدادات جهازك.</p>
                </div>
            </div>

            <div class="section">
                <h2>6. المجموعات</h2>
                <h3>6.1 إنشاء المجموعات</h3>
                <ul>
                    <li>يمكن لأي مستخدم إنشاء مجموعة جديدة</li>
                    <li>صاحب المجموعة مسؤول عن إدارة الأعضاء والإعدادات</li>
                    <li>يمكن تعيين نطاق أمان محدد لكل مجموعة</li>
                </ul>

                <h3>6.2 الانضمام للمجموعات</h3>
                <ul>
                    <li>يتم الانضمام عبر رمز الدعوة أو رمز QR</li>
                    <li>الانضمام للمجموعة يعني الموافقة على مشاركة موقعك مع أعضائها</li>
                    <li>يحق لك مغادرة أي مجموعة في أي وقت</li>
                </ul>
            </div>

            <div class="section">
                <h2>7. تنبيهات SOS</h2>
                <p>ميزة تنبيهات الطوارئ مصممة للحالات الحقيقية فقط:</p>
                <div class="info-box">
                    <p>⚠️ استخدام تنبيهات SOS الكاذبة قد يؤدي إلى:</p>
                    <ul>
                        <li>تعليق حسابك مؤقتاً</li>
                        <li>إزالتك من المجموعات</li>
                        <li>إيقاف حسابك نهائياً في حالة التكرار</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>8. حدود المسؤولية</h2>
                <p>تطبيق Season هو أداة مساعدة وليس بديلاً عن خدمات الطوارئ الرسمية:</p>
                <ul>
                    <li>🚫 لا نضمن دقة بيانات الموقع بنسبة 100%</li>
                    <li>🚫 لا نتحمل مسؤولية انقطاع الخدمة</li>
                    <li>🚫 لا نتحمل مسؤولية سوء استخدام التطبيق من قبل المستخدمين</li>
                    <li>🚫 في حالات الطوارئ الحقيقية، اتصل بخدمات الطوارئ المحلية</li>
                </ul>
            </div>

            <div class="section">
                <h2>9. إنهاء الخدمة</h2>
                <p>نحتفظ بالحق في:</p>
                <ol>
                    <li>تعليق أو إنهاء حسابك في حالة انتهاك الشروط</li>
                    <li>تعديل أو إيقاف أي جزء من الخدمة دون إشعار مسبق</li>
                    <li>حذف البيانات القديمة أو غير النشطة بعد فترة معينة</li>
                </ol>
            </div>

            <div class="section">
                <h2>10. الملكية الفكرية</h2>
                <p>جميع حقوق الملكية الفكرية للتطبيق والمحتوى محفوظة. لا يجوز:</p>
                <ul>
                    <li>نسخ أو تعديل التطبيق</li>
                    <li>إجراء هندسة عكسية للكود</li>
                    <li>استخدام الشعارات أو العلامات التجارية دون إذن</li>
                </ul>
            </div>

            <div class="section">
                <h2>11. التحديثات والتعديلات</h2>
                <div class="info-box">
                    <p>نحتفظ بالحق في تحديث هذه الشروط والأحكام في أي وقت. سيتم إشعارك بأي تغييرات جوهرية عبر:</p>
                    <ul>
                        <li>إشعار داخل التطبيق</li>
                        <li>رسالة بريد إلكتروني</li>
                        <li>تحديث تاريخ "آخر تحديث" في أعلى هذه الصفحة</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>12. القانون الحاكم</h2>
                <p>تخضع هذه الشروط والأحكام لقوانين دولة الإمارات العربية المتحدة، وأي نزاع ينشأ عنها يخضع للاختصاص القضائي لمحاكم الإمارات.</p>
            </div>

            <div class="section">
                <h2>13. معلومات الاتصال</h2>
                <div class="highlight">
                    <p>إذا كان لديك أي أسئلة حول هذه الشروط والأحكام، يرجى التواصل معنا:</p>
                    <p>📧 البريد الإلكتروني: <a href="mailto:support@seasonapp.com">support@seasonapp.com</a></p>
                    <p>🌐 الموقع الإلكتروني: <a href="#">www.seasonapp.com</a></p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="/" class="back-btn">← العودة للصفحة الرئيسية</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Season App</strong> - تطبيق الأمان والمتابعة العائلية</p>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
            <p><a href="/privacy">سياسة الخصوصية</a> | <a href="/terms">الشروط والأحكام</a></p>
        </div>
    </div>
</body>
</html>

