<?php

declare(strict_types=1);

require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';

$token = cpanel_setup_token();
$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $target = cpanel_setup_base_path().'/storage/app/public';
        $link = __DIR__.'/storage';

        if (! is_dir($target)) {
            throw new RuntimeException('Folder storage aplikasi tidak ditemukan.');
        }

        if (is_link($link)) {
            $message = 'Storage link sudah tersedia.';
        } elseif (file_exists($link)) {
            throw new RuntimeException('public/storage sudah ada dan bukan symbolic link. Hapus folder tersebut melalui File Manager lalu coba kembali.');
        } elseif (! symlink($target, $link)) {
            throw new RuntimeException('Hosting menolak pembuatan symbolic link. Hubungi provider hosting untuk mengaktifkan symlink.');
        } else {
            $message = 'Storage link berhasil dibuat.';
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$result = $message !== null ? '<pre>'.cpanel_setup_escape($message).'</pre>' : '';
$result .= $error !== null ? '<pre class="warn">'.cpanel_setup_escape($error).'</pre>' : '';

cpanel_setup_page('Setup storage', '<h1>Setup storage</h1><p>Membuat link publik untuk foto dan dokumen yang diunggah.</p>'.$result.'<form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><button type="submit">Buat storage link</button></form>');
