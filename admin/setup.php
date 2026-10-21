<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/auth.php';

if (admin_logged_in()) {
    redirect('/admin/');
}

$config = app_config();
$setupKey = trim((string) ($config['admin_setup_key'] ?? ''));
$keyConfigured = preg_match('/^[a-f0-9]{64}$/i', $setupKey) === 1;
$error = '';
$disabled = false;

$adminCount = (int) q_value('SELECT COUNT(*) FROM users', [], 0);
$setupDone = (string) q_value(
    'SELECT setting_value FROM settings WHERE setting_key = ?',
    ['admin_setup_complete'],
    '0'
) === '1';
$disabled = $adminCount > 0 || $setupDone;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$disabled) {
    csrf_check();
    $providedKey = isset($_POST['setup_key']) && is_string($_POST['setup_key']) ? trim($_POST['setup_key']) : '';
    $username = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
    $displayName = isset($_POST['display_name']) && is_string($_POST['display_name']) ? trim($_POST['display_name']) : '';
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $confirm = isset($_POST['password_confirm']) && is_string($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    if (!$keyConfigured || !hash_equals($setupKey, $providedKey)) {
        $error = 'کلید راه‌اندازی معتبر نیست. کلید ۶۴ رقمی inc/config.php را وارد کنید.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,60}$/', $username)) {
        $error = 'نام کاربری باید ۳ تا ۶۰ نویسه و فقط شامل حروف لاتین، عدد، نقطه، خط تیره یا زیرخط باشد.';
    } elseif ($displayName === '' || preg_match('/^.{1,120}$/us', $displayName) !== 1) {
        $error = 'نام نمایشی الزامی است و باید حداکثر ۱۲۰ نویسه باشد.';
    } elseif (strlen($password) < 12) {
        $error = 'رمز عبور باید حداقل ۱۲ نویسه باشد.';
    } elseif (!hash_equals($password, $confirm)) {
        $error = 'تکرار رمز عبور با رمز واردشده یکسان نیست.';
    } else {
        $lockName = 'mirkhalili_admin_bootstrap';
        $locked = (int) q_value('SELECT GET_LOCK(?, 10) AS lock_status', [$lockName], 0) === 1;
        if (!$locked) {
            $error = 'راه‌اندازی هم‌زمان ممکن نشد؛ چند لحظه بعد دوباره تلاش کنید.';
        } else {
            $created = false;
            try {
                $adminCount = (int) q_value('SELECT COUNT(*) FROM users', [], 0);
                $setupDone = (string) q_value(
                    'SELECT setting_value FROM settings WHERE setting_key = ?',
                    ['admin_setup_complete'],
                    '0'
                ) === '1';
                if ($adminCount > 0 || $setupDone) {
                    $disabled = true;
                } else {
                    q('INSERT INTO users (username, password_hash, display_name) VALUES (?, ?, ?)', [
                        $username,
                        password_hash($password, PASSWORD_DEFAULT),
                        $displayName,
                    ]);
                    q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)', [
                        'admin_setup_complete',
                        '1',
                    ]);
                    $created = true;
                }
            } catch (PDOException $exception) {
                $error = 'ساخت مدیر انجام نشد. نام کاربری ممکن است تکراری باشد؛ پس از بررسی دوباره تلاش کنید.';
            } finally {
                q('SELECT RELEASE_LOCK(?)', [$lockName]);
            }
            if ($created) {
                redirect('/admin/login.php?created=1');
            }
        }
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>ساخت اولین مدیر — <?= e(setting('site_name')) ?></title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="grid min-h-screen place-items-center bg-ink px-5 py-10">
  <main class="w-full max-w-lg bg-paper p-8 md:p-10">
    <p class="mono-label text-soft">راه‌اندازی یک‌باره</p>
    <h1 class="mt-2 text-2xl font-black tracking-tight">ساخت اولین کاربر مدیر</h1>
    <?php if ($disabled): ?>
      <p class="mt-5 border border-line bg-paper-deep px-4 py-3 leading-7">راه‌اندازی اولیه بسته است؛ کاربر مدیر از قبل ساخته شده است. برای ورود به پنل از صفحهٔ ورود استفاده کنید.</p>
      <a href="/admin/login.php" class="btn btn-primary mt-5">رفتن به ورود</a>
    <?php elseif (!$keyConfigured): ?>
      <p class="mt-5 border border-accent bg-accent/10 px-4 py-3 leading-7 text-accent">برای امنیت، ابتدا در فایل <code>inc/config.php</code> یک کلید تصادفی ۶۴ رقمی در مقدار <code>admin_setup_key</code> قرار دهید. روش ساخت کلید در مستندات نصب نوشته شده است.</p>
      <a href="/admin/login.php" class="link-slide mt-5 inline-block text-sm font-bold">بازگشت به ورود</a>
    <?php else: ?>
      <?php if ($error !== ''): ?><p class="mt-5 border border-accent bg-accent/10 px-4 py-3 text-sm font-bold text-accent" role="alert"><?= e($error) ?></p><?php endif; ?>
      <p class="mt-4 text-sm leading-7 text-soft">این فرم فقط تا پیش از ساخت اولین کاربر فعال است. کلید راه‌اندازی را از فایل پیکربندی وارد کنید؛ پس از ساخت اکانت، فرم خودکار غیرفعال می‌شود.</p>
      <form method="post" class="mt-6 space-y-4">
        <?= csrf_field() ?>
        <label class="block"><span class="mono-label mb-2 block text-soft">کلید راه‌اندازی</span><input name="setup_key" type="password" required autocomplete="off" class="input" dir="ltr"></label>
        <label class="block"><span class="mono-label mb-2 block text-soft">نام کاربری</span><input name="username" required minlength="3" maxlength="60" autocomplete="username" class="input" dir="ltr"></label>
        <label class="block"><span class="mono-label mb-2 block text-soft">نام نمایشی</span><input name="display_name" required maxlength="120" autocomplete="name" class="input"></label>
        <label class="block"><span class="mono-label mb-2 block text-soft">رمز عبور (حداقل ۱۲ نویسه)</span><input name="password" type="password" required minlength="12" autocomplete="new-password" class="input" dir="ltr"></label>
        <label class="block"><span class="mono-label mb-2 block text-soft">تکرار رمز عبور</span><input name="password_confirm" type="password" required minlength="12" autocomplete="new-password" class="input" dir="ltr"></label>
        <button class="btn btn-primary w-full">ساخت مدیر و بستن راه‌اندازی</button>
      </form>
    <?php endif; ?>
  </main>
</body>
</html>
