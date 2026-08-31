<?php

declare(strict_types=1);

require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';

$token = cpanel_setup_token();
$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'clear') {
            $message = cpanel_setup_run('optimize:clear');
        } elseif ($action === 'disable') {
            cpanel_setup_disable();
            $message = 'Setup web telah dinonaktifkan. Hapus file cpanel_*.php melalui File Manager sebagai langkah akhir.';
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$result = $message !== null ? '<pre>'.cpanel_setup_escape($message).'</pre>' : '';
$result .= $error !== null ? '<pre class="warn">'.cpanel_setup_escape($error).'</pre>' : '';

cpanel_setup_page('Clear dan pengamanan setup', '<h1>Clear dan pengamanan setup</h1><p>Gunakan clear bila konfigurasi di .env berubah. Setelah website selesai disiapkan, nonaktifkan seluruh halaman setup ini.</p>'.$result.'<section><h2>Clear cache</h2><form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><input type="hidden" name="action" value="clear"><button type="submit">Clear cache aplikasi</button></form></section><section><h2>Nonaktifkan setup</h2><p class="warn">Setelah dinonaktifkan, semua halaman cpanel_*.php tidak dapat dipakai lagi sampai file pengaman dihapus lewat File Manager.</p><form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><input type="hidden" name="action" value="disable"><button type="submit">Nonaktifkan setup web</button></form></section>');
