<?php
declare(strict_types=1);
$admin_title = 'پروفایل من'; $admin_active = 'profile';
require_once __DIR__ . '/inc/auth.php';
admin_require_module('profile', ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');
$id = (int) $_SESSION['admin_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim((string)($_POST['display_name'] ?? '')); $email = trim((string)($_POST['email'] ?? ''));
    $current = (string)($_POST['current_password'] ?? ''); $new = (string)($_POST['new_password'] ?? '');
    $u = q_one('SELECT password_hash FROM users WHERE id = ?', [$id]);
    if ($name === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) flash_set('نام یا ایمیل معتبر نیست.', 'err');
    elseif ($new !== '' && (!password_verify($current, (string)$u['password_hash']) || strlen($new) < 10)) flash_set('برای تغییر رمز، رمز فعلی را وارد کنید و رمز تازه حداقل ۱۰ نویسه باشد.', 'err');
    else {
        q('UPDATE users SET display_name = ?, email = ? WHERE id = ?', [$name,$email,$id]);
        if ($new !== '') q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT),$id]);
        $_SESSION['admin_name'] = $name;
        flash_set('پروفایل به‌روزرسانی شد.'); redirect('/admin/profile.php');
    }
}
$user = q_one('SELECT username, display_name, email, role, created_at, last_login FROM users WHERE id = ?', [$id]);
require __DIR__ . '/inc/header.php'; ?>
<form method="post" class="max-w-2xl border border-line bg-paper p-6"><?= csrf_field() ?><p class="mb-5 text-sm text-soft">نام کاربری: <b dir="ltr"><?= e($user['username']) ?></b> · نقش: <?= e($user['role']) ?></p>
<label class="mb-4 block font-bold">نام نمایشی<input class="input mt-1" name="display_name" required value="<?= e($user['display_name']) ?>"></label><label class="mb-5 block font-bold">ایمیل<input class="input mt-1" type="email" name="email" value="<?= e($user['email']) ?>"></label><hr class="my-5 border-line"><h2 class="mb-3 font-extrabold">تغییر رمز عبور (اختیاری)</h2><label class="mb-3 block font-bold">رمز فعلی<input class="input mt-1" type="password" name="current_password" autocomplete="current-password"></label><label class="mb-5 block font-bold">رمز جدید، حداقل ۱۰ نویسه<input class="input mt-1" type="password" name="new_password" minlength="10" autocomplete="new-password"></label><button class="btn btn-primary">ذخیرهٔ پروفایل</button></form>
<?php require __DIR__ . '/inc/footer.php'; ?>
