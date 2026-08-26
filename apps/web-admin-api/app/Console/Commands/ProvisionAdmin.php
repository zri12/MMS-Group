<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProvisionAdmin extends Command
{
    protected $signature = 'mms:provision-admin
        {--name= : Nama administrator}
        {--username= : Username administrator}
        {--email= : Email administrator}
        {--password= : Password administrator}';

    protected $description = 'Membuat akun administrator pertama pada database kosong.';

    public function handle(): int
    {
        if (User::query()->where('role', UserRole::Admin)->exists()) {
            $this->error('Database sudah memiliki akun administrator. Gunakan halaman Profil Admin untuk mengubah akun yang ada.');

            return self::FAILURE;
        }

        $input = [
            'name' => Str::of((string) $this->option('name'))->squish()->toString(),
            'username' => Str::of((string) $this->option('username'))->trim()->lower()->toString(),
            'email' => Str::of((string) $this->option('email'))->trim()->lower()->toString(),
            'password' => (string) $this->option('password'),
        ];

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8)],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::query()->create([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'] !== '' ? $input['email'] : null,
            'password' => Hash::make($input['password']),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->info('Akun administrator berhasil dibuat.');

        return self::SUCCESS;
    }
}
