<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

class SeedDemoData extends Command
{
    protected $signature = 'mms:seed-demo {--force-local}';

    protected $description = 'Seed data demo MMS tanpa membuat akun login PDL.';

    public function handle(): int
    {
        if (! app()->environment(['local', 'development']) || ! $this->option('force-local')) {
            $this->error('Command ini hanya tersedia pada local/development dengan --force-local.');

            return self::FAILURE;
        }

        $this->call('db:seed', ['--class' => DemoDataSeeder::class]);

        return self::SUCCESS;
    }
}
