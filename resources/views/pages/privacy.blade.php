<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سياسة الخصوصية - Season App</title>
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

        .privacy-badge {
            display: inline-flex;
            align-items: center;
            background: #092C4C;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            margin: 5px;
        }

        .privacy-badge .icon {
            margin-left: 8px;
            font-size: 18px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .data-table th {
            background: #092C4C;
            color: white;
            padding: 15px;
            text-align: right;
            font-weight: 600;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background: #f8f9fa;
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

        .commitment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .commitment-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .commitment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .commitment-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .commitment-card h4 {
            color: #092C4C;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .commitment-card p {
            color: #757575;
            font-size: 14px;
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

            .data-table {
                font-size: 14px;
            }

            .data-table th, .data-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔒</div>
            <h1>سياسة الخصوصية</h1>
            <p class="subtitle">حماية خصوصيتك أولويتنا القصوى</p>
        </div>

        <div class="content">
            <div class="highlight">
                <p><strong>📅 تاريخ آخر تحديث:</strong> {{ date('Y/m/d') }}</p>
                <p>في Season، نحن ملتزمون بحماية خصوصيتك وبياناتك الشخصية. توضح هذه السياسة كيفية جمع واستخدام وحماية معلوماتك.</p>
            </div>

            <div class="commitment-grid">
                <div class="commitment-card">
                    <div class="icon">🔐</div>
                    <h4>تشفير متقدم</h4>
                    <p>جميع بياناتك محمية بتشفير من الدرجة الأولى</p>
                </div>
                <div class="commitment-card">
                    <div class="icon">🚫</div>
                    <h4>عدم البيع</h4>
                    <p>لن نبيع بياناتك لأي طرف ثالث أبداً</p>
                </div>
                <div class="commitment-card">
                    <div class="icon">✓</div>
                    <h4>شفافية كاملة</h4>
                    <p>نوضح بدقة كيف نستخدم بياناتك</p>
                </div>
            </div>

            <div class="section">
                <h2>1. المعلومات التي نجمعها</h2>

                <h3>1.1 المعلومات الشخصية</h3>
                <p>عند التسجيل في التطبيق، نجمع المعلومات التالية:</p>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>نوع البيانات</th>
                            <th>الغرض من الجمع</th>
                            <th>إلزامي/اختياري</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>📧 البريد الإلكتروني</td>
                            <td>تسجيل الدخول والتواصل</td>
                            <td><span class="privacy-badge">إلزامي</span></td>
                        </tr>
                        <tr>
                            <td>📱 رقم الهاتف</td>
                            <td>التحقق والأمان</td>
                            <td><span class="privacy-badge">إلزامي</span></td>
                        </tr>
                        <tr>
                            <td>👤 الاسم</td>
                            <td>التعريف داخل المجموعات</td>
                            <td><span class="privacy-badge">إلزامي</span></td>
                        </tr>
                        <tr>
                            <td>🖼️ الصورة الشخصية</td>
                            <td>التعريف البصري</td>
                            <td><span class="privacy-badge">اختياري</span></td>
                        </tr>
                        <tr>
                            <td>🏷️ اللقب</td>
                            <td>التخصيص الشخصي</td>
                            <td><span class="privacy-badge">اختياري</span></td>
                        </tr>
                    </tbody>
                </table>

                <h3>1.2 بيانات الموقع الجغرافي</h3>
                <div class="info-box">
                    <p><strong>📍 متى نجمع بيانات الموقع؟</strong></p>
                    <ul>
                        <li>فقط عندما تكون عضواً نشطاً في مجموعة</li>
                        <li>فقط عندما يكون التطبيق مفتوحاً أو يعمل في الخلفية (بموافقتك)</li>
                        <li>يمكنك إيقاف المشاركة في أي وقت</li>
                    </ul>
                    <p><strong>🎯 كيف نستخدم بيانات الموقع؟</strong></p>
                    <ul>
                        <li>عرض موقعك لأعضاء مجموعتك فقط</li>
                        <li>حساب المسافة من نقطة التجمع</li>
                        <li>إرسال تنبيهات خروج من نطاق الأمان</li>
                        <li>تنبيهات الطوارئ (SOS)</li>
                    </ul>
                </div>

                <h3>1.3 بيانات الاستخدام</h3>
                <ul>
                    <li>معلومات الجهاز (نوع الجهاز، نظام التشغيل، الإصدار)</li>
                    <li>سجل الأنشطة داخل التطبيق</li>
                    <li>معلومات الاتصال بالإنترنت</li>
                    <li>أوقات استخدام التطبيق</li>
                </ul>
            </div>

            <div class="section">
                <h2>2. كيف نستخدم معلوماتك</h2>
                <div class="info-box">
                    <p>نستخدم معلوماتك فقط للأغراض التالية:</p>
                    <ol>
                        <li><strong>توفير الخدمة:</strong> تمكين ميزات التطبيق الأساسية</li>
                        <li><strong>تحسين التجربة:</strong> تطوير وتحسين التطبيق</li>
                        <li><strong>الأمان:</strong> حماية حسابك ومنع الاحتيال</li>
                        <li><strong>التواصل:</strong> إرسال تحديثات مهمة وإشعارات</li>
                        <li><strong>الدعم الفني:</strong> مساعدتك في حل المشاكل</li>
                        <li><strong>الامتثال القانوني:</strong> الالتزام بالقوانين واللوائح</li>
                    </ol>
                </div>
            </div>

            <div class="section">
                <h2>3. مشاركة المعلومات</h2>

                <h3>3.1 داخل التطبيق</h3>
                <div class="highlight">
                    <p><strong>مع من نشارك بياناتك؟</strong></p>
                    <ul>
                        <li>✓ أعضاء مجموعاتك فقط (الموقع، الاسم، الصورة)</li>
                        <li>✗ لا نشارك بياناتك مع مستخدمين آخرين خارج مجموعاتك</li>
                        <li>✗ لا نبيع بياناتك لأي شركات إعلانية</li>
                    </ul>
                </div>

                <h3>3.2 مع أطراف ثالثة</h3>
                <p>قد نشارك معلومات محدودة مع:</p>
                <ul>
                    <li><strong>مزودي الخدمات:</strong> شركات الاستضافة والتخزين السحابي (بموجب اتفاقيات حماية البيانات)</li>
                    <li><strong>خدمات التحليلات:</strong> لفهم كيفية استخدام التطبيق (بيانات مجهولة فقط)</li>
                    <li><strong>خدمات الإشعارات:</strong> لإرسال التنبيهات (Firebase Cloud Messaging)</li>
                    <li><strong>السلطات القانونية:</strong> فقط عند الإلزام قانونياً</li>
                </ul>
            </div>

            <div class="section">
                <h2>4. الأمان وحماية البيانات</h2>

                <div class="commitment-grid">
                    <div class="commitment-card">
                        <div class="icon">🔐</div>
                        <h4>تشفير SSL/TLS</h4>
                        <p>جميع الاتصالات مشفرة</p>
                    </div>
                    <div class="commitment-card">
                        <div class="icon">🛡️</div>
                        <h4>حماية كلمات المرور</h4>
                        <p>تخزين آمن بتشفير من طرف واحد</p>
                    </div>
                    <div class="commitment-card">
                        <div class="icon">🔍</div>
                        <h4>مراقبة أمنية</h4>
                        <p>كشف الأنشطة المشبوهة</p>
                    </div>
                </div>

                <div class="info-box">
                    <p><strong>إجراءات الأمان لدينا:</strong></p>
                    <ul>
                        <li>تشفير البيانات أثناء النقل والتخزين</li>
                        <li>مصادقة ثنائية متاحة للحسابات</li>
                        <li>نسخ احتياطية منتظمة ومؤمنة</li>
                        <li>فحص أمني دوري للأنظمة</li>
                        <li>وصول محدود للبيانات من قبل الموظفين</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2>5. حقوقك</h2>
                <p>لديك الحقوق التالية فيما يتعلقببياناتك:</p>

                <div class="info-box">
                    <h3>🔍 الحق في الوصول</h3>
                    <p>يمكنك طلب نسخة من جميع بياناتك المخزنة لدينا</p>

                    <h3>✏️ الحق في التصحيح</h3>
                    <p>يمكنك تحديث أو تصحيح معلوماتك الشخصية في أي وقت</p>

                    <h3>🗑️ الحق في الحذف</h3>
                    <p>يمكنك طلب حذف حسابك وجميع بياناتك المرتبطة به</p>

                    <h3>📤 الحق في نقل البيانات</h3>
                    <p>يمكنك طلب تصدير بياناتك بصيغة قابلة للقراءة</p>

                    <h3>🚫 الحق في الاعتراض</h3>
                    <p>يمكنك الاعتراض على معالجة بياناتك لأغراض معينة</p>

                    <h3>⏸️ الحق في التقييد</h3>
                    <p>يمكنك طلب تقييد معالجة بياناتك</p>
                </div>

                <p><strong>كيفية ممارسة حقوقك:</strong></p>
                <ul>
                    <li>من خلال إعدادات التطبيق</li>
                    <li>بالتواصل معنا عبر: <a href="mailto:privacy@seasonapp.com">privacy@seasonapp.com</a></li>
                </ul>
            </div>

            <div class="section">
                <h2>6. الاحتفاظ بالبيانات</h2>
                <p>نحتفظ ببياناتك طالما كان حسابك نشطاً. بعد حذف الحساب:</p>
                <ul>
                    <li>🗑️ يتم حذف معظم البيانات فوراً</li>
                    <li>📦 قد نحتفظ ببعض البيانات لمدة 90 يوماً للنسخ الاحتياطية</li>
                    <li>📊 البيانات المجهولة (للتحليلات) قد تُحفظ لفترة أطول</li>
                    <li>⚖️ البيانات المطلوبة قانونياً تُحفظ حسب القانون</li>
                </ul>
            </div>

            <div class="section">
                <h2>7. خصوصية الأطفال</h2>
                <div class="highlight">
                    <p><strong>⚠️ تنويه مهم:</strong></p>
                    <p>التطبيق غير مخصص للأطفال دون سن 13 عاماً. إذا علمنا بجمع بيانات من طفل دون هذا السن عن طريق الخطأ، سنقوم بحذفها فوراً.</p>
                    <p>المستخدمون بين 13-18 عاماً يجب أن يحصلوا على موافقة ولي الأمر.</p>
                </div>
            </div>

            <div class="section">
                <h2>8. ملفات تعريف الارتباط (Cookies)</h2>
                <p>نستخدم تقنيات مشابهة لملفات تعريف الارتباط لـ:</p>
                <ul>
                    <li>الحفاظ على جلسة تسجيل الدخول</li>
                    <li>حفظ تفضيلاتك</li>
                    <li>تحليل استخدام التطبيق</li>
                    <li>تحسين الأداء</li>
                </ul>
            </div>

            <div class="section">
                <h2>9. التحديثات على هذه السياسة</h2>
                <p>قد نقوم بتحديث سياسة الخصوصية من وقت لآخر. سنخطرك بأي تغييرات جوهرية عبر:</p>
                <ul>
                    <li>📱 إشعار داخل التطبيق</li>
                    <li>📧 رسالة بريد إلكتروني</li>
                    <li>📅 تحديث تاريخ "آخر تحديث" في أعلى هذه الصفحة</li>
                </ul>
                <p>استمرارك في استخدام التطبيق بعد التحديثات يعني موافقتك عليها.</p>
            </div>

            <div class="section">
                <h2>10. النقل الدولي للبيانات</h2>
                <p>قد يتم نقل بياناتك ومعالجتها في دول أخرى. نضمن حماية بياناتك بنفس المستوى أينما كانت مخزنة من خلال:</p>
                <ul>
                    <li>اتفاقيات نقل البيانات القياسية</li>
                    <li>اختيار مزودي خدمات معتمدين</li>
                    <li>تطبيق معايير الأمان الدولية</li>
                </ul>
            </div>

            <div class="section">
                <h2>11. معلومات الاتصال</h2>
                <div class="highlight">
                    <p><strong>لأي استفسارات أو طلبات متعلقة بالخصوصية:</strong></p>
                    <p>📧 البريد الإلكتروني: <a href="mailto:privacy@seasonapp.com">privacy@seasonapp.com</a></p>
                    <p>📧 الدعم العام: <a href="mailto:support@seasonapp.com">support@seasonapp.com</a></p>
                    <p>🌐 الموقع الإلكتروني: <a href="#">www.seasonapp.com</a></p>
                    <p><strong>مسؤول حماية البيانات:</strong> dpo@seasonapp.com</p>
                </div>
            </div>

            <div class="section">
                <h2>12. موافقتك</h2>
                <div class="info-box">
                    <p>باستخدامك لتطبيق Season، فإنك:</p>
                    <ul>
                        <li>✓ توافق على جمع واستخدام معلوماتك كما هو موضح في هذه السياسة</li>
                        <li>✓ تؤكد أنك قرأت وفهمت هذه السياسة</li>
                        <li>✓ توافق على إشعارنا بأي تغييرات في معلوماتك</li>
                    </ul>
                    <p>يمكنك سحب موافقتك في أي وقت بحذف حسابك.</p>
                </div>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="/" class="back-btn">← العودة للصفحة الرئيسية</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Season App</strong> - خصوصيتك وأمانك في المقام الأول</p>
            <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
            <p><a href="/privacy">سياسة الخصوصية</a> | <a href="/terms">الشروط والأحكام</a></p>
        </div>
    </div>
</body>
</html>

