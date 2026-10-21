<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';

if (admin_logged_in()) {
    redirect('/admin/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim((string) ($_POST['username'] ?? ''));
    $p = (string) ($_POST['password'] ?? '');
    if (admin_login($u, $p)) {
        redirect('/admin/');
    }
    $error = 'نام کاربری یا رمز عبور اشتباه است.';
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>ورود به پنل — <?= e(setting('site_name')) ?></title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="grid min-h-screen place-items-center bg-ink px-5">
  <form method="post" class="w-full max-w-sm bg-paper p-8 md:p-10">
    <?= csrf_field() ?>
    <p class="mono-label text-soft">پنل مدیریت</p>
    <h1 class="mt-2 text-2xl font-black tracking-tight">ورود به پنل</h1>
    <?php if ($error !== ''): ?><p class="mt-4 border border-accent bg-accent/10 px-4 py-2 text-sm font-bold text-accent"><?= e($error) ?></p><?php endif; ?>
    <label class="mt-6 block">
      <span class="mono-label mb-2 block text-soft">نام کاربری</span>
      <input name="username" required autocomplete="username" class="w-full border border-line bg-paper-deep px-4 py-3 outline-none focus:border-accent" value="<?= e($_POST['username'] ?? '') ?>">
    </label>
    <label class="mt-4 block">
      <span class="mono-label mb-2 block text-soft">رمز عبور</span>
      <input name="password" type="password" required autocomplete="current-password" class="w-full border border-line bg-paper-deep px-4 py-3 outline-none focus:border-accent">
    </label>
    <button class="mt-6 w-full bg-ink py-3.5 text-sm font-black text-paper transition-colors hover:bg-accent">ورود</button>
    <p class="mt-4 text-center"><a href="/" class="link-slide text-sm text-soft">بازگشت به سایت</a></p>
  </form>
</body>
</html>
