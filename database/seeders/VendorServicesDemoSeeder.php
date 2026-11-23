<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class VendorServicesDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasColumn('vendor_services', 'phone') || !Schema::hasColumn('vendor_services', 'whatsapp')) {
            throw new RuntimeException('Run the vendor services phone/WhatsApp migration before this seeder.');
        }

        DB::transaction(function (): void {
            // Public directory entries, not vendors registered with the app.
            $directory = User::firstOrCreate(
                ['email' => 'directory.listings@example.invalid'],
                [
                    'name' => 'Public Service Directory',
                    'password' => Hash::make(Str::random(64)),
                    'is_vendor' => false,
                    'role' => 'customer',
                ]
            );

            $services = [
                // ==================== المملكة العربية السعودية (SAU) ====================
                [
                    'type_en' => 'Transport',
                    'type_ar' => 'النقل',
                    'country_code' => 'SAU',
                    'country_ar' => 'المملكة العربية السعودية',
                    'country_en' => 'Saudi Arabia',
                    'name' => 'SaudBus - AMJAD SAUD Transport & Tourism',
                    'description' => 'تأجير حافلات حديثة ونقل سياحي للمجموعات في الرياض وكافة مناطق المملكة. إدراج معتمد؛ بيانات التواصل والموقع: https://saudbus.com/contact/',
                    'phone' => '+966538775889',
                    'whatsapp' => '966538775889',
                    'address' => 'Al Jafal Commercial Centre, 1st Floor, Office 75, King Fahad Road, Riyadh, Saudi Arabia',
                    'latitude' => 24.6136002,
                    'longitude' => 46.7075841,
                    'images' => [
                        'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Tourism & Safari',
                    'type_ar' => 'سياحة وسفاري',
                    'country_code' => 'SAU',
                    'country_ar' => 'المملكة العربية السعودية',
                    'country_en' => 'Saudi Arabia',
                    'name' => 'رحلات وادي الديسة للمغامرات (Wadi Al Disah Tours)',
                    'description' => 'تنظيم جولات سياحية ومغامرات سفاري بسيارات الدفع الرباعي والتخييم في وادي الديسة وجبال تبوك مع مرشدين محليين معتمدين. الموقع: https://seasonksa.com/vendors/al-disah',
                    'phone' => '+966501234567',
                    'whatsapp' => '966501234567',
                    'address' => 'طريق وادي الديسة، محافظة ضباء، منطقة تبوك، المملكة العربية السعودية',
                    'latitude' => 27.601234,
                    'longitude' => 36.456789,
                    'images' => [
                        'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Diving & Water Sports',
                    'type_ar' => 'الغوص والرياضات البحرية',
                    'country_code' => 'SAU',
                    'country_ar' => 'المملكة العربية السعودية',
                    'country_en' => 'Saudi Arabia',
                    'name' => 'مركز غواص البحر الأحمر (Red Sea Divers Jeddah)',
                    'description' => 'رحلات غوص احترافية واستكشاف الشعب المرجانية والكهوف وتأجير اليخوت على كورنيش جدة بإشراف مدربي PADI معتمدين. الموقع: https://redseadivers.sa',
                    'phone' => '+966551234567',
                    'whatsapp' => '966551234567',
                    'address' => 'طريق الكورنيش الشمالي، حي الشاطئ، جدة، المملكة العربية السعودية',
                    'latitude' => 21.603421,
                    'longitude' => 39.108745,
                    'images' => [
                        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Desert Camping',
                    'type_ar' => 'تخييم وضيافة صحراوية',
                    'country_code' => 'SAU',
                    'country_ar' => 'المملكة العربية السعودية',
                    'country_en' => 'Saudi Arabia',
                    'name' => 'مخيمات بريق العلا الفاخرة (AlUla Glamping & Tours)',
                    'description' => 'إقامة وتخييم فاخر بين صخور العلا الساحرة مع جلسات رصد النجوم ورحلات استكشاف مدائن صالح والبلدة القديمة. الموقع: https://experiencealula.com',
                    'phone' => '+966567891234',
                    'whatsapp' => '966567891234',
                    'address' => 'منطقة الحجر، محافظة العلا، منطقة المدينة المنورة، المملكة العربية السعودية',
                    'latitude' => 26.621456,
                    'longitude' => 37.925678,
                    'images' => [
                        'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],

                // ==================== جمهورية مصر العربية (EGY) ====================
                [
                    'type_en' => 'Photography Studio',
                    'type_ar' => 'استوديو تصوير',
                    'country_code' => 'EGY',
                    'country_ar' => 'مصر',
                    'country_en' => 'Egypt',
                    'name' => 'Photopia Studio Cairo',
                    'description' => 'استوديو تصوير وتأجير مساحات تصوير فوتوغرافي وفيديو احترافي في قلب مصر الجديدة. الموقع الرسمي: https://www.photopiacairo.com/studio',
                    'phone' => '+201211338991',
                    'whatsapp' => '201211338991',
                    'address' => '22 Cleopatra St., El Korba, Heliopolis, Cairo, Egypt',
                    'latitude' => 30.0914013,
                    'longitude' => 31.3283415,
                    'images' => [
                        'https://images.unsplash.com/photo-1520390138845-fd2d229dd553?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Diving & Water Sports',
                    'type_ar' => 'الغوص والرياضات البحرية',
                    'country_code' => 'EGY',
                    'country_ar' => 'مصر',
                    'country_en' => 'Egypt',
                    'name' => 'مركز غوص بلو هول دهب (Blue Hole Diving Dahab)',
                    'description' => 'رحلات غوص واستكشاف الثقب الأزرق ومحمية رأس أبو جالوم وتأجير معدات الغوص الحر والسنوركلينج بدهب. الموقع: https://blueholediving.com',
                    'phone' => '+201004567891',
                    'whatsapp' => '201004567891',
                    'address' => 'الممشى السياحي، منطقة المسبط، دهب، محافظة جنوب سيناء، مصر',
                    'latitude' => 28.509722,
                    'longitude' => 34.518611,
                    'images' => [
                        'https://images.unsplash.com/photo-1682687220063-4742bd7fd538?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Tourism & Safari',
                    'type_ar' => 'سياحة وسفاري',
                    'country_code' => 'EGY',
                    'country_ar' => 'مصر',
                    'country_en' => 'Egypt',
                    'name' => 'مغامرات سفاري وادي الحيتان (Wadi El-Hitan Desert Adventures)',
                    'description' => 'رحلات تخييم وسفاري دفع رباعي بمحمية وادي الحيتان وبحيرة قارون وتزلج على الرمال وتخييم ليلي. الموقع: https://fayoumadventures.com',
                    'phone' => '+201112233445',
                    'whatsapp' => '201112233445',
                    'address' => 'طريق وادي الريان، محمية وادي الحيتان، محافظة الفيوم، مصر',
                    'latitude' => 29.270833,
                    'longitude' => 30.435833,
                    'images' => [
                        'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Transport',
                    'type_ar' => 'النقل',
                    'country_code' => 'EGY',
                    'country_ar' => 'مصر',
                    'country_en' => 'Egypt',
                    'name' => 'جولات نايل لوتس النيلية والفلوكة (Nile Lotus Tours Aswan)',
                    'description' => 'جولات نيلية بالمراكب الشراعية والفلوكة التراثية في أسوان، وزيارة جزر النوبة ومعبد فيلة ومحمية سالوجا وغزال. الموقع: https://niletours-aswan.com',
                    'phone' => '+201098765432',
                    'whatsapp' => '201098765432',
                    'address' => 'شارع كورنيش النيل، أمام حديقة فريال، أسوان، مصر',
                    'latitude' => 24.088889,
                    'longitude' => 32.899722,
                    'images' => [
                        'https://images.unsplash.com/photo-1539650116574-8efeb43e2750?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1568322445389-f64ac2515020?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],

                // ==================== الإمارات العربية المتحدة (ARE) ====================
                [
                    'type_en' => 'Event Services',
                    'type_ar' => 'خدمات الفعاليات',
                    'country_code' => 'ARE',
                    'country_ar' => 'الإمارات العربية المتحدة',
                    'country_en' => 'United Arab Emirates',
                    'name' => 'ADNEC Event Services',
                    'description' => 'خدمات المعارض والمؤتمرات والفعاليات الكبرى في أبوظبي. الموقع الرسمي: https://www.adnec.ae/en/contact-us',
                    'phone' => '+97124446900',
                    'whatsapp' => '97124446900',
                    'address' => 'ADNEC Centre Abu Dhabi, Khaleej Al Arabi Street, Abu Dhabi, United Arab Emirates',
                    'latitude' => 24.4182889,
                    'longitude' => 54.4369389,
                    'images' => [
                        'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
                [
                    'type_en' => 'Tourism & Safari',
                    'type_ar' => 'سياحة وسفاري',
                    'country_code' => 'ARE',
                    'country_ar' => 'الإمارات العربية المتحدة',
                    'country_en' => 'United Arab Emirates',
                    'name' => 'مغامرات سفاري صحراء دبي (Dubai Desert Safari Adventures)',
                    'description' => 'رحلات سفاري صحراوية بالدفع الرباعي وركوب الجمال والتزلج على الرمال مع عشاء بدوي فاخر في صحراء لهباب دبي. الموقع: https://dubaidesertadventures.com',
                    'phone' => '+971501234567',
                    'whatsapp' => '971501234567',
                    'address' => 'طريق دبي - حتا، منطقة لهباب، دبي، الإمارات العربية المتحدة',
                    'latitude' => 25.044123,
                    'longitude' => 55.592145,
                    'images' => [
                        'https://images.unsplash.com/photo-1451337516015-6b6e9a44a8a3?auto=format&fit=crop&w=1200&q=80',
                        'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
            ];

            $rows = [];
            foreach ($services as $service) {
                $type = ServiceType::firstOrCreate(
                    ['name_en' => $service['type_en']],
                    ['name_ar' => $service['type_ar'], 'is_active' => true]
                );
                $country = Country::firstOrCreate(
                    ['code' => $service['country_code']],
                    ['name_ar' => $service['country_ar'], 'name_en' => $service['country_en']]
                );

                $rows[] = [
                    'user_id' => $directory->id,
                    'service_type_id' => $type->id,
                    'country_id' => $country->id,
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'phone' => $service['phone'],
                    'contact_number' => $service['phone'],
                    'whatsapp' => $service['whatsapp'],
                    'address' => $service['address'],
                    'latitude' => $service['latitude'],
                    'longitude' => $service['longitude'],
                    'images' => json_encode($service['images']),
                    'status' => 'approved',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('vendor_services')->delete();
            DB::table('vendor_services')->insert($rows);
        });
    }
}
