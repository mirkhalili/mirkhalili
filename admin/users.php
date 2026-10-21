<?php
declare(strict_types=1);
$admin_title = 'کاربران و دسترسی‌ها';
$admin_active = 'users';
require_once __DIR__ . '/inc/auth.php';
admin_require_module('users', ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save_user') {
        $id = (int) ($_POST['id'] ?? 0);
        $username = trim((string) ($_POST['username'] ?? ''));
        $name = trim((string) ($_POST['display_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $role = (string) ($_POST['role'] ?? 'viewer');
        $active = isset($_POST['is_active']) ? 1 : 0;
        $password = (string) ($_POST['password'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username) || !in_array($role, ['admin','editor','viewer'], true) || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            flash_set('نام کاربری، ایمیل یا نقش معتبر نیست.', 'err');
        } elseif ($id === (int) $_SESSION['admin_id'] && ($role !== 'admin' || !$active)) {
            flash_set('برای جلوگیری از قفل‌شدن پنل، نقش یا وضعیت حساب خودتان را از این صفحه تغییر ندهید.', 'err');
        } elseif ($id === 0 && strlen($password) < 10) {
            flash_set('رمز عبور کاربر جدید باید حداقل ۱۰ نویسه باشد.', 'err');
        } elseif ($id > 0 && $password !== '' && strlen($password) < 10) {
            flash_set('رمز جدید باید حداقل ۱۰ نویسه باشد.', 'err');
        } else {
            try {
                if ($id > 0) {
                    if (!$active || $role !== 'admin') {
                        $others = (int) q_value('SELECT COUNT(*) FROM users WHERE role = \'admin\' AND is_active = 1 AND id <> ?', [$id], 0);
                        if ($others < 1 && (int) q_value('SELECT COUNT(*) FROM users WHERE id = ? AND role = \'admin\' AND is_active = 1', [$id], 0) > 0) {
                            throw new RuntimeException('باید دست‌کم یک مدیر فعال باقی بماند.');
                        }
                    }
                    q('UPDATE users SET username = ?, display_name = ?, email = ?, role = ?, is_active = ? WHERE id = ?', [$username, $name, $email, $role, $active, $id]);
                    if ($password !== '') q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $id]);
                } else {
                    q('INSERT INTO users (username, password_hash, display_name, email, role, is_active) VALUES (?, ?, ?, ?, ?, ?)', [$username, password_hash($password, PASSWORD_DEFAULT), $name, $email, $role, $active]);
                    $id = (int) db()->lastInsertId();
                }
                flash_set('اطلاعات کاربر ذخیره شد.');
                redirect('/admin/users.php');
            } catch (Throwable $exception) {
                flash_set($exception instanceof RuntimeException ? $exception->getMessage() : 'ذخیرهٔ کاربر انجام نشد؛ نام کاربری باید یکتا باشد.', 'err');
            }
        }
    }
}
$edit = isset($_GET['edit']) ? q_one('SELECT id, username, display_name, email, role, is_active FROM users WHERE id = ?', [(int) $_GET['edit']]) : null;
$users = q_all('SELECT id, username, display_name, email, role, is_active, created_at, last_login FROM users ORDER BY id');
require __DIR__ . '/inc/header.php';
?>
<div class="grid gap-8 xl:grid-cols-[1fr_22rem]">
<section class="overflow-x-auto border border-line bg-paper"><table class="w-full text-right text-sm"><thead><tr class="border-b border-line"><th class="p-3">کاربر</th><th class="p-3">نقش</th><th class="p-3">وضعیت</th><th class="p-3">آخرین ورود</th><th class="p-3"></th></tr></thead><tbody>
<?php foreach ($users as $u): ?><tr class="border-b border-line"><td class="p-3"><b><?= e($u['display_name'] ?: $u['username']) ?></b><div class="text-soft" dir="ltr"><?= e($u['username']) ?> · <?= e($u['email']) ?></div></td><td class="p-3"><?= e(['admin'=>'مدیر','editor'=>'ویرایشگر','viewer'=>'فقط‌خواندنی'][$u['role']] ?? $u['role']) ?></td><td class="p-3"><?= (int)$u['is_active'] ? 'فعال' : 'غیرفعال' ?></td><td class="p-3"><?= e($u['last_login'] ?? '—') ?></td><td class="p-3"><a class="btn btn-sm" href="?edit=<?= (int)$u['id'] ?>">ویرایش</a></td></tr><?php endforeach; ?>
</tbody></table></section>
<form method="post" class="h-fit border border-line bg-paper p-5"><?= csrf_field() ?><input type="hidden" name="action" value="save_user"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><h2 class="mb-4 font-extrabold"><?= $edit ? 'ویرایش کاربر' : 'افزودن کاربر' ?></h2>
<?php foreach (['username'=>'نام کاربری','display_name'=>'نام نمایشی','email'=>'ایمیل'] as $key=>$label): ?><label class="mb-3 block text-sm font-bold"><?= e($label) ?><input class="input mt-1" name="<?= e($key) ?>" value="<?= e($edit[$key] ?? '') ?>" <?= $key==='username'?'required':'' ?>></label><?php endforeach; ?>
<label class="mb-3 block text-sm font-bold">نقش<select class="input mt-1" name="role"><?php foreach(['admin'=>'مدیر','editor'=>'ویرایشگر محتوا','viewer'=>'فقط‌خواندنی'] as $k=>$v): ?><option value="<?= $k ?>" <?= ($edit['role'] ?? 'viewer')===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?></select></label>
<label class="mb-3 block text-sm font-bold">رمز عبور <?= $edit?'(برای حفظ رمز خالی بگذارید)':'' ?><input class="input mt-1" type="password" name="password" <?= $edit?'':'required' ?> minlength="10" autocomplete="new-password"></label><label class="mb-4 flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?= !isset($edit['is_active']) || (int)$edit['is_active'] ? 'checked':'' ?>> حساب فعال</label><button class="btn btn-primary">ذخیرهٔ کاربر</button></form>
</div><?php require __DIR__ . '/inc/footer.php'; ?>
