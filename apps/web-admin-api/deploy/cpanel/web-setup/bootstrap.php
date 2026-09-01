<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

function cpanel_setup_base_path(): string
{
    return dirname(__DIR__, 3);
}

function cpanel_setup_abort(string $message, int $status = 403): never
{
    http_response_code($status);
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex, nofollow');

    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    exit("<!doctype html><html lang=\"id\"><meta charset=\"utf-8\"><title>Setup cPanel</title><body><h1>Setup cPanel tidak dapat dijalankan</h1><p>{$safeMessage}</p></body></html>");
}

function cpanel_setup_token(): string
{
    $tokenFile = __DIR__.'/setup-token.php';

    if (! is_file($tokenFile)) {
        cpanel_setup_abort('Token setup tidak tersedia. Buat ulang paket deployment.');
    }

    $expected = require $tokenFile;
    $received = (string) ($_REQUEST['token'] ?? '');

    if (! is_string($expected) || strlen($expected) < 48 || ! hash_equals($expected, $received)) {
        cpanel_setup_abort('Token setup tidak valid.');
    }

    if (is_file(cpanel_setup_base_path().'/storage/app/private/cpanel-setup.disabled')) {
        cpanel_setup_abort('Setup web telah dinonaktifkan. Hapus file setup melalui File Manager bila benar-benar perlu menjalankannya kembali.', 410);
    }

    return $received;
}

function cpanel_setup_bootstrap_application(): void
{
    static $booted = false;

    if ($booted) {
        return;
    }

    $basePath = cpanel_setup_base_path();
    $autoload = $basePath.'/vendor/autoload.php';

    if (! is_file($autoload)) {
        throw new RuntimeException('Dependensi Laravel tidak ditemukan. Pastikan folder vendor dari paket aplikasi telah diekstrak di root proyek.');
    }

    require_once $autoload;

    $application = require $basePath.'/bootstrap/app.php';
    $publicPath = getenv('MMS_CPANEL_PUBLIC_PATH');

    if (is_string($publicPath) && $publicPath !== '' && is_dir($publicPath)) {
        $application->usePublicPath($publicPath);
    }

    $application->make(Kernel::class)->bootstrap();
    $booted = true;
}

/**
 * @param  array<string, mixed>  $parameters
 */
function cpanel_setup_run(string $command, array $parameters = []): string
{
    cpanel_setup_bootstrap_application();
    Artisan::call($command, $parameters);

    return trim(Artisan::output());
}

function cpanel_setup_environment_exists(): bool
{
    return is_file(cpanel_setup_base_path().'/.env');
}

function cpanel_setup_create_environment(): void
{
    $basePath = cpanel_setup_base_path();
    $environment = $basePath.'/.env';
    $example = $basePath.'/.env.example';

    if (is_file($environment)) {
        return;
    }

    if (! is_file($example) || ! copy($example, $environment)) {
        throw new RuntimeException('File .env tidak dapat dibuat. Gunakan File Manager untuk menyalin .env.example menjadi .env.');
    }
}

function cpanel_setup_disable(): void
{
    $path = cpanel_setup_base_path().'/storage/app/private/cpanel-setup.disabled';
    $directory = dirname($path);

    if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
        throw new RuntimeException('Folder pengaman setup tidak dapat dibuat.');
    }

    if (file_put_contents($path, 'disabled '.gmdate(DATE_ATOM).PHP_EOL, LOCK_EX) === false) {
        throw new RuntimeException('Setup web tidak dapat dinonaktifkan.');
    }
}

function cpanel_setup_page(string $title, string $body): never
{
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex, nofollow');

    exit('<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'</title><style>body{margin:0;background:#080c12;color:#edf1f7;font:16px system-ui,sans-serif}.wrap{max-width:720px;margin:48px auto;padding:0 20px}section{background:#121923;border:1px solid #2a3441;border-radius:8px;padding:24px;margin:16px 0}h1{margin:0 0 8px}h2{font-size:18px}p,li{color:#b8c2d1;line-height:1.55}label{display:block;margin:12px 0 6px}input{box-sizing:border-box;width:100%;padding:10px;border:1px solid #465365;border-radius:5px;background:#0c121a;color:#fff}button{margin-top:18px;padding:10px 14px;border:0;border-radius:5px;background:#d7b52b;color:#0b0d10;font-weight:700;cursor:pointer}pre{white-space:pre-wrap;background:#080c12;border:1px solid #2a3441;padding:12px;overflow:auto}.warn{color:#f1ca55}</style></head><body><main class="wrap">'.$body.'</main></body></html>');
}

function cpanel_setup_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
