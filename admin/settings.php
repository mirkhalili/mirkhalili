<?php
declare(strict_types=1);
$admin_title = 'تنظیمات سایت';
$admin_active = 'settings';
require_once __DIR__ . '/inc/auth.php';

$fields = [
    'site_name'    => ['نام سایت', 'text'],
    'site_name_en' => ['نام لاتین', 'text'],
    'role'         => ['عنوان حرفه‌ای', 'text'],
    'city'         => ['شهر', 'text'],
    'email'        => ['ایمیل', 'text'],
    'phone'        => ['تلفن', 'text'],
    'availability' => ['وضعیت دسترس‌پذیری', 'text'],
    'hero_l1'      => ['هیرو — خط ۱', 'text'],
    'hero_l2'      => ['هیرو — خط ۲', 'text'],
    'hero_l3'      => ['هیرو — خط ۳', 'text'],
    'hero_intro'   => ['معرفی کوتاه هیرو', 'textarea'],
    'hero_areas'   => ['حوزه‌ها (با | جدا کنید)', 'text'],
    'marquee'      => ['نوار متحرک (با | جدا کنید)', 'text'],
    'studio_title' => ['عنوان بخش استودیو', 'text'],
    'studio_text'  => ['متن بخش استودیو', 'textarea'],
    'about_title'  => ['تیتر درباره', 'textarea'],
    'about_intro'  => ['مقدمهٔ درباره', 'textarea'],
    'about_p1'     => ['پاراگراف ۱ درباره', 'textarea'],
    'about_p2'     => ['پاراگراف ۲ درباره', 'textarea'],
    'about_p3'     => ['پاراگراف ۳ درباره', 'textarea'],
    'services_intro' => ['مقدمهٔ صفحهٔ خدمات', 'textarea'],
    'process_note' => ['یادداشت زمان‌بندی فرآیند', 'textarea'],
    'contact_note' => ['یادداشت ساعت پاسخ‌گویی', 'textarea'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_settings') {
        foreach (array_keys($fields) as $key) {
            $value = trim((string) ($_POST[$key] ?? ''));
            q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
               ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)', [$key, $value]);
        }
        // شبکه‌های اجتماعی
        q('DELETE FROM socials');
        $sort = 0;
        foreach ((array) ($_POST['soc'] ?? []) as $s) {
            $label = trim((string) ($s['label'] ?? ''));
            $url = trim((string) ($s['url'] ?? ''));
            if ($label === '' || $url === '') {
                continue;
            }
            $sort++;
            q('INSERT INTO socials (label, url, sort) VALUES (?, ?, ?)', [$label, $url, $sort]);
        }
        flash_set('تنظیمات ذخیره شد.');
        redirect('/admin/settings.php');
    }

    if ($action === 'change_password') {
        $cur = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['new'] ?? '');
        $user = q_one('SELECT * FROM users WHERE id = ?', [(int) $_SESSION['admin_id']]);
        if ($user === null || !password_verify($cur, $user['password_hash'])) {
            flash_set('رمز فعلی درست نیست.', 'err');
        } elseif (strlen($new) < 8) {
            flash_set('رمز جدید باید حداقل ۸ کاراکتر باشد.', 'err');
        } else {
            q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), (int) $user['id']]);
            flash_set('رمز عبور تغییر کرد.');
        }
        redirect('/admin/settings.php');
    }
}

require __DIR__ . '/inc/header.php';
$socials = q_all('SELECT * FROM socials ORDER BY sort, id');
if ($socials === []) {
    $socials = [['label' => '', 'url' => '']];
}
?>

<div class="grid gap-8 xl:grid-cols-[1fr_22rem]">
  <form method="post" class="border border-line bg-paper p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_settings">
    <h2 class="mb-6 text-lg font-extrabold">متن‌ها و اطلاعات سایت</h2>
    <div class="grid gap-4 md:grid-cols-2">
      <?php foreach ($fields as $key => [$label, $type]): ?>
        <label class="block <?= $type === 'textarea' ? 'md:col-span-2' : '' ?>">
          <span class="mono-label mb-2 block text-soft"><?= e($label) ?></span>
          <?php if ($type === 'textarea'): ?>
            <textarea name="<?= e($key) ?>" rows="3" class="input"><?= e(setting($key)) ?></textarea>
          <?php else: ?>
            <input name="<?= e($key) ?>" class="input" value="<?= e(setting($key)) ?>">
          <?php endif; ?>
        </label>
      <?php endforeach; ?>
    </div>

    <p class="mono-label mb-2 mt-8 text-soft">شبکه‌های اجتماعی</p>
    <div data-repeater="soc">
      <?php foreach ($socials as $i => $s): ?>
        <div class="rep-row mb-2 grid gap-2 md:grid-cols-[10rem_1fr_auto]">
          <input name="soc[<?= $i ?>][label]" class="input" value="<?= e($s['label']) ?>" placeholder="Behance">
          <input name="soc[<?= $i ?>][url]" class="input" dir="ltr" value="<?= e($s['url']) ?>" placeholder="https://…">
          <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-3 flex gap-2">
      <button type="button" class="btn btn-sm" data-rep-add="soc">+ افزودن شبکه</button>
      <button class="btn btn-primary">ذخیرهٔ تنظیمات</button>
    </div>
  </form>

  <form method="post" class="h-fit border border-line bg-paper p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="change_password">
    <h2 class="mb-4 text-lg font-extrabold">تغییر رمز عبور</h2>
    <label class="block">
      <span class="mono-label mb-2 block text-soft">رمز فعلی</span>
      <input name="current" type="password" required class="input">
    </label>
    <label class="mt-4 block">
      <span class="mono-label mb-2 block text-soft">رمز جدید (حداقل ۸ کاراکتر)</span>
      <input name="new" type="password" required minlength="8" class="input">
    </label>
    <button class="btn btn-primary mt-6 w-full">تغییر رمز</button>
  </form>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>
