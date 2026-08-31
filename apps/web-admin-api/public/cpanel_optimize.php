<?php

declare(strict_types=1);

require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';

$token = cpanel_setup_token();
$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $message = cpanel_setup_run('optimize');
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$result = $message !== null ? '<pre>'.cpanel_setup_escape($message).'</pre>' : '';
$result .= $error !== null ? '<pre class="warn">'.cpanel_setup_escape($error).'</pre>' : '';

cpanel_setup_page('Optimasi aplikasi', '<h1>Optimasi aplikasi</h1><p>Gunakan setelah migration dan konfigurasi selesai agar cache produksi dibuat ulang.</p>'.$result.'<form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><button type="submit">Optimalkan aplikasi</button></form>');
