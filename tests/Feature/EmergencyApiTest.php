<?php

namespace Tests\Feature;

use App\Http\Middleware\UpdateUserLastActive;
use App\Http\Middleware\SaveUserPreferredLanguage;
use App\Models\User;
use Database\Seeders\EmbassiesSeeder;
use Database\Seeders\EmergencyNumbersSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EmergencyApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '2025_10_21_181254_create_countries_table.php',
            '2025_10_21_181603_create_emergency_numbers_table.php',
            '2025_11_01_201353_add_embassy_column_to_emergency_numbers_table.php',
            '2026_11_24_000000_extend_emergency_numbers_and_create_embassies.php',
        ] as $file) {
            (require database_path('migrations/'.$file))->up();
        }

        $this->seed(EmergencyNumbersSeeder::class);
        $this->seed(EmbassiesSeeder::class);
        $this->withoutMiddleware(UpdateUserLastActive::class);
        $this->withoutMiddleware(SaveUserPreferredLanguage::class);
    }

    private function actingAsNationality(string $nationality): void
    {
        $user = User::create([
            'name' => 'Traveler',
            'email' => strtolower($nationality).uniqid().'@example.com',
            'nationality' => $nationality,
        ]);
        $this->actingAs($user, 'sanctum');
    }

    public function test_egyptian_in_saudi_arabia_gets_egyptian_embassy(): void
    {
        $this->actingAsNationality('EGY');

        $this->getJson('/api/emergency?country_code=SAU')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.country_name', 'المملكة العربية السعودية')
            ->assertJsonPath('data.police', '999')
            ->assertJsonPath('data.ambulance', '997')
            ->assertJsonPath('data.fire', '998')
            ->assertJsonPath('data.unified_emergency', '911')
            ->assertJsonPath('data.embassy.country_represented', 'مصر')
            ->assertJsonPath('data.embassy.name', 'سفارة جمهورية مصر العربية في الرياض')
            ->assertJsonPath('data.embassy.phone', 'PLACEHOLDER - VERIFY PHONE');
    }

    public function test_saudi_in_egypt_gets_saudi_embassy_from_header(): void
    {
        $this->actingAsNationality('SAU');

        $this->withHeader('Accept-Country', 'EGY')->getJson('/api/emergency')
            ->assertOk()
            ->assertJsonPath('data.country_code', 'EGY')
            ->assertJsonPath('data.unified_emergency', '112')
            ->assertJsonPath('data.embassy.country_represented', 'المملكة العربية السعودية')
            ->assertJsonPath('data.embassy.name', 'سفارة المملكة العربية السعودية في القاهرة');
    }

    public function test_same_nationality_and_host_has_no_embassy(): void
    {
        $this->actingAsNationality('SAU');

        $this->getJson('/api/emergency?country_code=SAU')
            ->assertOk()->assertJsonPath('data.embassy', null);
    }

    public function test_missing_embassy_returns_null(): void
    {
        $this->actingAsNationality('KWT');

        $this->getJson('/api/emergency?country_code=SAU')
            ->assertOk()->assertJsonPath('data.embassy', null);
    }

    public function test_unsupported_country_returns_arabic_404(): void
    {
        $this->getJson('/api/emergency?country_code=USA')
            ->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'الدولة غير مدعومة أو لا توجد لها أرقام طوارئ.');
    }

    public function test_seeders_cover_all_requested_countries_and_embassies(): void
    {
        $this->assertSame(7, DB::table('emergency_numbers')->whereNotNull('country_code')->count());
        $this->assertSame(17, DB::table('embassies')->count());
        $this->assertSame('112', DB::table('emergency_numbers')->where('country_code', 'KWT')->value('fire'));
        $this->assertSame('9999', DB::table('emergency_numbers')->where('country_code', 'OMN')->value('ambulance'));
    }

    public function test_emergency_seeder_updates_legacy_country_codes_without_duplicate_rows(): void
    {
        DB::table('countries')->where('code', 'SAU')->update(['code' => 'SAU ']);
        DB::table('countries')->where('code', 'ARE')->update(['code' => 'UAE']);
        DB::table('emergency_numbers')->whereIn('country_code', ['SAU', 'ARE'])->update(['country_code' => null]);

        $this->seed(EmergencyNumbersSeeder::class);

        $this->assertSame(7, DB::table('countries')->count());
        $this->assertSame(7, DB::table('emergency_numbers')->count());
        $this->assertSame('911', DB::table('emergency_numbers')->where('country_code', 'SAU')->value('unified_emergency'));
        $this->assertSame('999', DB::table('emergency_numbers')->where('country_code', 'ARE')->value('unified_emergency'));
    }
}
