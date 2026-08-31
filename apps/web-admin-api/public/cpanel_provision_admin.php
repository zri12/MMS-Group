<?php

declare(strict_types=1);

require __DIR__.'/../deploy/cpanel/web-setup/bootstrap.php';

$token = cpanel_setup_token();
$message = null;
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || $username === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
            throw new RuntimeException('Nama, username, email valid, dan password minimal 12 karakter wajib diisi.');
        }

        $message = cpanel_setup_run('mms:provision-admin', [
            '--name' => $name,
            '--username' => $username,
            '--email' => $email,
            '--password' => $password,
        ]);
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}

$result = $message !== null ? '<pre>'.cpanel_setup_escape($message).'</pre>' : '';
$result .= $error !== null ? '<pre class="warn">'.cpanel_setup_escape($error).'</pre>' : '';

cpanel_setup_page('Buat admin awal', '<h1>Buat admin awal</h1><p>Halaman ini hanya dapat membuat admin bila akun admin belum ada.</p>'.$result.'<form method="post"><input type="hidden" name="token" value="'.cpanel_setup_escape($token).'"><label>Nama</label><input name="name" required><label>Username</label><input name="username" required><label>Email</label><input name="email" type="email" required><label>Password baru</label><input name="password" type="password" minlength="12" required><button type="submit">Buat admin</button></form>');
