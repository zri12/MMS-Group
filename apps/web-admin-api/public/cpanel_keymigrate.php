<?php

declare(strict_types=1);

require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';

$token = cpanel_setup_token();
$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'create_env') {
            cpanel_setup_create_environment();
            $message = 'File .env telah tersedia. Isi APP_URL dan seluruh DB_* melalui File Manager sebelum menjalankan migration.';
        } elseif ($action === 'generate_key') {
            if (! cpanel_setup_environment_exists()) {
                throw new RuntimeException('Buat file .env terlebih dahulu.');
            }

            $message = cpanel_setup_run('key:generate', ['--force' => true]);
        } elseif ($action === 'migrate') {
            if (! cpanel_setup_environment_exists()) {
                throw new RuntimeException('Buat dan isi file .env terlebih dahulu.');
            }

            $message = cpanel_setup_run('migrate', ['--force' => true]);
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$result = $message !== null ? '<pre>'.cpanel_setup_escape($message).'</pre>' : '';
$result .= $error !== null ? '<pre class="warn">'.cpanel_setup_escape($error).'</pre>' : '';
$environmentStatus = cpanel_setup_environment_exists() ? 'tersedia' : 'belum tersedia';

cpanel_setup_page('Setup key dan database', '<h1>Setup key dan database</h1><p>Gunakan halaman ini satu kali setelah ZIP diekstrak. Status .env: <strong>'.cpanel_setup_escape($environmentStatus).'</strong>.</p>'.$result.'<section><h2>1. Buat .env</h2><p>Setelah dibuat, edit .env melalui File Manager dan isi APP_URL, DB_DATABASE, DB_USERNAME, serta DB_PASSWORD.</p><form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><input type="hidden" name="action" value="create_env"><button type="submit">Buat file .env</button></form></section><section><h2>2. Buat APP_KEY</h2><form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><input type="hidden" name="action" value="generate_key"><button type="submit">Buat APP_KEY</button></form></section><section><h2>3. Buat tabel database</h2><p class="warn">Pastikan data database di .env sudah benar sebelum menjalankan migration.</p><form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><input type="hidden" name="action" value="migrate"><button type="submit">Jalankan migration</button></form></section>');
