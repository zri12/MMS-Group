<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\OperationalRecap;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ResetDevelopmentUsers extends Command
{
    protected $signature = 'mms:reset-dev-users {--force-local : Konfirmasi reset khusus environment local/development}';

    protected $description = 'Hapus kredensial development, pertahankan riwayat bisnis, lalu buat satu Admin.';

    public function handle(): int
    {
        if (! app()->environment(['local', 'development', 'testing'])) {
            $this->error('Reset akun hanya diizinkan pada environment local, development, atau testing.');

            return self::FAILURE;
        }

        if (! $this->option('force-local')) {
            $this->error('Gunakan --force-local setelah memverifikasi environment development.');

            return self::FAILURE;
        }

        $password = Str::password(20, true, true, true, false);

        DB::transaction(function () use ($password): void {
            $temporaryAdmin = User::query()->create([
                'name' => 'Administrator',
                'username' => 'reset-'.Str::lower(Str::random(24)),
                'email' => 'reset-'.Str::lower(Str::random(24)).'@mms.local',
                'password' => $password,
                'role' => UserRole::Admin,
                'is_active' => true,
            ]);

            $oldUserIds = User::withTrashed()
                ->whereKeyNot($temporaryAdmin->id)
                ->pluck('id');

            if (Schema::hasTable('personal_access_tokens')) {
                PersonalAccessToken::query()->delete();
            }

            if (Schema::hasTable('password_reset_tokens')) {
                DB::table('password_reset_tokens')->delete();
            }

            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->whereIn('user_id', $oldUserIds)->delete();
            }

            MarketingProfile::query()->whereIn('user_id', $oldUserIds)->update(['user_id' => null]);
            MarketingSchedule::query()->whereIn('created_by', $oldUserIds)->update(['created_by' => $temporaryAdmin->id]);
            OperationalRecap::query()->whereIn('created_by', $oldUserIds)->update(['created_by' => $temporaryAdmin->id]);

            User::withTrashed()->whereIn('id', $oldUserIds)->forceDelete();

            $temporaryAdmin->forceFill([
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@mms.local',
                'password' => $password,
                'role' => UserRole::Admin,
                'is_active' => true,
                'last_login_at' => null,
                'remember_token' => null,
            ])->save();
        });

        $this->newLine();
        $this->info('Development auth reset selesai. Simpan password berikut sekarang; password tidak disimpan di source.');
        $this->line('Username: admin');
        $this->line('Password: '.$password);
        $this->table(['Total User', 'Admin', 'PDL Login', 'Sanctum Token'], [[
            User::query()->count(),
            User::query()->where('role', UserRole::Admin)->count(),
            User::query()->where('role', UserRole::Marketing)->count(),
            PersonalAccessToken::query()->count(),
        ]]);

        return self::SUCCESS;
    }
}
