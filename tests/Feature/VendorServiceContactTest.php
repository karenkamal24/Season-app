<?php

namespace Tests\Feature;

use App\Http\Middleware\SaveUserPreferredLanguage;
use App\Http\Middleware\UpdateUserLastActive;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\VendorService;
use Database\Seeders\VendorServicesDemoSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\VendorService\StoreVendorServiceRequest;
use App\Http\Requests\VendorService\UpdateVendorServiceRequest;
use Tests\TestCase;

class VendorServiceContactTest extends TestCase
{
    private User $user;
    private ServiceType $type;

    protected function setUp(): void
    {
        parent::setUp();
        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '2025_10_19_173407_add_is_vendor_to_users_table.php',
            '2025_10_19_152359_create_settings_table.php',
            '2025_10_19_174123_create_service_types_table.php',
            '2025_10_19_180451_create_vendor_services_table.php',
            '2025_10_21_181254_create_countries_table.php',
            '2025_12_11_143647_add_country_to_vendor_services_table.php',
            '2026_11_24_010000_add_phone_and_whatsapp_to_vendor_services.php',
        ] as $file) {
            (require database_path('migrations/'.$file))->up();
        }

        $this->withoutMiddleware(UpdateUserLastActive::class);
        $this->withoutMiddleware(SaveUserPreferredLanguage::class);
        $this->user = User::create(['name' => 'Vendor', 'email' => 'vendor@example.com']);
        $this->type = ServiceType::create(['name_ar' => 'نقل', 'name_en' => 'Transport', 'is_active' => true]);
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_store_rejects_invalid_contact_and_coordinates(): void
    {
        $payload = [
            'service_type_id' => $this->type->id,
            'name' => 'Car',
            'phone' => '01044444445',
            'whatsapp' => '+201044444445',
            'latitude' => 91,
            'longitude' => -181,
        ];
        $this->postJson('/api/vendor-services', $payload)->assertBadRequest();
        $errors = Validator::make($payload, (new StoreVendorServiceRequest())->rules())->errors();
        foreach (['phone', 'whatsapp', 'latitude', 'longitude'] as $field) {
            $this->assertTrue($errors->has($field), $field);
        }
    }

    public function test_store_accepts_valid_contact_and_public_response_has_required_fields(): void
    {
        $response = $this->postJson('/api/vendor-services', [
            'service_type_id' => $this->type->id,
            'name' => 'Car',
            'phone' => '+201012345678',
            'whatsapp' => '201012345678',
            'address' => 'Cairo',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ])->assertCreated();

        $id = $response->json('data.id');
        $this->assertSame('+201012345678', DB::table('vendor_services')->where('id', $id)->value('contact_number'));
        DB::table('vendor_services')->where('id', $id)->update([
            'status' => 'approved',
            'images' => json_encode(['vendor_services/images/photo.jpg']),
        ]);

        $this->getJson('/api/vendor-services/'.$id)
            ->assertOk()->assertJsonStructure(['success', 'data' => [
                'id', 'name', 'service_type', 'phone', 'whatsapp', 'address', 'latitude', 'longitude', 'images',
            ]])
            ->assertJsonPath('data.phone', '+201012345678')
            ->assertJsonPath('data.whatsapp', '201012345678')
            ->assertJsonPath('data.address', 'Cairo')
            ->assertJsonPath('data.images.0', asset('storage/vendor_services/images/photo.jpg'));
    }

    public function test_update_rejects_invalid_contact_and_coordinates(): void
    {
        $service = VendorService::create([
            'user_id' => $this->user->id,
            'service_type_id' => $this->type->id,
            'name' => 'Car',
            'status' => 'pending',
        ]);

        $payload = [
            'phone' => '1234',
            'whatsapp' => '00123456789',
            'latitude' => -91,
            'longitude' => 181,
        ];
        $this->putJson('/api/vendor-services/'.$service->id, $payload)->assertBadRequest();
        $errors = Validator::make($payload, (new UpdateVendorServiceRequest())->rules())->errors();
        foreach (['phone', 'whatsapp', 'latitude', 'longitude'] as $field) {
            $this->assertTrue($errors->has($field), $field);
        }
    }

    public function test_update_accepts_valid_contact_and_syncs_legacy_number(): void
    {
        $service = VendorService::create([
            'user_id' => $this->user->id,
            'service_type_id' => $this->type->id,
            'name' => 'Car',
            'status' => 'pending',
        ]);

        $this->putJson('/api/vendor-services/'.$service->id, [
            'phone' => '+966512345678',
            'whatsapp' => '966512345678',
            'latitude' => -90,
            'longitude' => 180,
        ])->assertOk()->assertJsonPath('data.phone', '+966512345678');

        $this->assertSame('+966512345678', $service->refresh()->contact_number);
    }

    public function test_audit_flags_suspicious_numbers_without_modifying_rows(): void
    {
        $service = VendorService::create([
            'user_id' => $this->user->id,
            'service_type_id' => $this->type->id,
            'name' => 'Suspicious service',
            'phone' => '+201044444445',
            'status' => 'pending',
        ]);
        $before = DB::table('vendor_services')->where('id', $service->id)->first();

        $this->assertSame(0, Artisan::call('vendor-services:audit-phones'));
        $report = Artisan::output();
        $this->assertStringContainsString('suspicious pattern', $report);
        $this->assertStringContainsString((string) $service->id, $report);
        $this->assertEquals($before, DB::table('vendor_services')->where('id', $service->id)->first());
    }

    public function test_directory_seeder_replaces_old_services_with_public_contact_data(): void
    {
        VendorService::create([
            'user_id' => $this->user->id,
            'service_type_id' => $this->type->id,
            'name' => 'Old service',
            'status' => 'approved',
        ]);

        $this->seed(VendorServicesDemoSeeder::class);

        $totalServices = DB::table('vendor_services')->count();
        $this->assertSame(0, DB::table('vendor_services')->where('name', 'Old service')->count());
        $this->assertSame(10, $totalServices);
        $this->assertSame(10, DB::table('vendor_services')->where('status', 'approved')->count());
        $this->assertSame(10, DB::table('vendor_services')->whereNotNull('phone')->count());
        $this->assertSame(10, DB::table('vendor_services')->whereNotNull('whatsapp')->count());
        $this->assertSame(10, DB::table('vendor_services')->whereNotNull('latitude')->whereNotNull('longitude')->count());

        $services = VendorService::all();
        foreach ($services as $service) {
            $this->assertMatchesRegularExpression('/^\+[1-9]\d{7,14}$/', $service->phone);
            $this->assertNotEmpty($service->address);
            $this->assertStringContainsString('https://', $service->description);
            $this->withHeader('Accept-Country', $service->country->code)
                ->getJson('/api/vendor-services/'.$service->id)
                ->assertOk()
                ->assertJsonPath('data.phone', $service->phone)
                ->assertJsonPath('data.address', $service->address);
            $this->withHeader('Accept-Country', $service->country->code)
                ->getJson('/api/vendor-services')
                ->assertOk()
                ->assertJsonFragment(['name' => $service->name]);
        }
    }
}
