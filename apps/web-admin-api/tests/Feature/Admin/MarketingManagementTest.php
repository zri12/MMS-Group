<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\UserRole;
use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_marketing_list_and_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();

        $this->actingAs($admin)
            ->get('/admin/marketing')
            ->assertOk()
            ->assertSee('Pengaturan PDL')
            ->assertSee($marketing->code)
            ->assertSee($marketing->user->name);

        $this->actingAs($admin)
            ->get(route('admin.marketing.show', $marketing))
            ->assertOk()
            ->assertSee($marketing->user->username)
            ->assertSee('Reset Password');
    }

    public function test_admin_can_create_marketing_with_work_days(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.marketing.store'), [
            'name' => 'Marketing Baru',
            'username' => 'm20.baru',
            'email' => 'm20@example.test',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'code' => 'm20',
            'phone' => '081200000020',
            'area' => 'Gedebage',
            'work_days' => [DayName::Monday->value, DayName::Tuesday->value],
        ]);

        $profile = MarketingProfile::query()->where('code', 'M20')->firstOrFail();

        $response->assertRedirect(route('admin.marketing.show', $profile));
        $this->assertDatabaseHas('users', [
            'username' => 'm20.baru',
            'role' => UserRole::Marketing->value,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('marketing_work_days', [
            'marketing_profile_id' => $profile->id,
            'day_name' => DayName::Monday->value,
        ]);
        $this->assertTrue(Hash::check('StrongPassword123!', $profile->user->password));
    }

    public function test_admin_can_upload_marketing_profile_photo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.marketing.store'), [
            'name' => 'Marketing Foto',
            'username' => 'm21.foto',
            'email' => 'm21@example.test',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'code' => 'M21',
            'phone' => '081200000021',
            'area' => 'Gedebage',
            'profile_photo' => UploadedFile::fake()->image('marketing.jpg', 320, 320),
            'work_days' => [DayName::Monday->value],
        ]);

        $profile = MarketingProfile::query()->where('code', 'M21')->firstOrFail();

        $response->assertRedirect(route('admin.marketing.show', $profile));
        $this->assertNotNull($profile->profile_photo_path);
        Storage::disk('public')->assertExists($profile->profile_photo_path);
    }

    public function test_admin_can_replace_marketing_profile_photo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('marketing-profiles', 'public');
        $marketing->forceFill(['profile_photo_path' => $oldPath])->save();

        $this->actingAs($admin)->put(route('admin.marketing.update', $marketing), [
            'name' => 'Nama Foto Baru',
            'username' => 'm01.photo',
            'email' => '',
            'code' => 'M01',
            'phone' => '081299999999',
            'area' => 'Rancasari',
            'profile_photo' => UploadedFile::fake()->image('new.jpg', 320, 320),
            'work_days' => [DayName::Wednesday->value],
        ])->assertRedirect(route('admin.marketing.show', $marketing));

        $marketing->refresh();

        $this->assertNotSame($oldPath, $marketing->profile_photo_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($marketing->profile_photo_path);
    }

    public function test_marketing_create_validates_unique_username_code_and_work_days(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = $this->marketingProfile();

        $response = $this->actingAs($admin)
            ->from(route('admin.marketing.create'))
            ->post(route('admin.marketing.store'), [
                'name' => 'Marketing Duplikat',
                'username' => $existing->user->username,
                'password' => 'StrongPassword123!',
                'password_confirmation' => 'StrongPassword123!',
                'code' => $existing->code,
                'area' => 'Gedebage',
                'work_days' => ['Minggu'],
            ]);

        $response->assertRedirect(route('admin.marketing.create'));
        $response->assertSessionHasErrors(['username', 'code', 'work_days.0']);
    }

    public function test_admin_can_update_marketing_without_changing_status_or_password(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $oldPassword = $marketing->user->password;

        $response = $this->actingAs($admin)->put(route('admin.marketing.update', $marketing), [
            'name' => 'Nama Diperbarui',
            'username' => 'm01.update',
            'email' => '',
            'code' => 'M01A',
            'phone' => '081299999999',
            'area' => 'Rancasari',
            'work_days' => [DayName::Wednesday->value, DayName::Thursday->value],
        ]);

        $response->assertRedirect(route('admin.marketing.show', $marketing));
        $marketing->refresh();

        $this->assertSame('Nama Diperbarui', $marketing->user->name);
        $this->assertSame('m01.update', $marketing->user->username);
        $this->assertNull($marketing->user->email);
        $this->assertSame('M01A', $marketing->code);
        $this->assertTrue($marketing->user->is_active);
        $this->assertSame($oldPassword, $marketing->user->password);
        $this->assertDatabaseMissing('marketing_work_days', [
            'marketing_profile_id' => $marketing->id,
            'day_name' => DayName::Monday->value,
        ]);
        $this->assertDatabaseHas('marketing_work_days', [
            'marketing_profile_id' => $marketing->id,
            'day_name' => DayName::Wednesday->value,
        ]);
    }

    public function test_admin_can_disable_and_enable_marketing_and_disable_revokes_tokens(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $token = $marketing->user->createToken('mms-marketing:Device', ['marketing-mobile']);

        $this->actingAs($admin)->patch(route('admin.marketing.status', $marketing), [
            'is_active' => false,
        ])->assertRedirect();

        $this->assertFalse($marketing->refresh()->user->is_active);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);

        $this->actingAs($admin)->patch(route('admin.marketing.status', $marketing), [
            'is_active' => true,
        ])->assertRedirect();

        $this->assertTrue($marketing->refresh()->user->is_active);
    }

    public function test_admin_can_reset_marketing_password_and_revoke_tokens(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $token = $marketing->user->createToken('mms-marketing:Device', ['marketing-mobile']);

        $this->actingAs($admin)->patch(route('admin.marketing.reset-password', $marketing), [
            'password' => 'NewStrongPassword123!',
            'password_confirmation' => 'NewStrongPassword123!',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('NewStrongPassword123!', $marketing->refresh()->user->password));
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_guest_and_marketing_user_cannot_manage_marketing_data(): void
    {
        $marketing = $this->marketingProfile();
        $marketingUser = User::factory()->marketing()->create();

        $this->get(route('admin.marketing.index'))->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketingUser)
            ->get(route('admin.marketing.index'))
            ->assertForbidden();

        $this->actingAs($marketingUser)
            ->post(route('admin.marketing.store'), [])
            ->assertForbidden();

        $this->actingAs($marketingUser)
            ->patch(route('admin.marketing.status', $marketing), ['is_active' => false])
            ->assertForbidden();
    }

    private function marketingProfile(): MarketingProfile
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Deden Marketing',
            'username' => 'm01.deden',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $profile = MarketingProfile::factory()->create([
            'user_id' => $user->id,
            'code' => 'M01',
            'area' => 'Gedebage',
            'phone' => '081200000001',
        ]);

        $profile->workDays()->createMany([
            ['day_name' => DayName::Monday->value],
            ['day_name' => DayName::Tuesday->value],
        ]);

        return $profile->load(['user', 'workDays']);
    }
}
