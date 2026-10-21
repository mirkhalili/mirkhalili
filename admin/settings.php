<?php
declare(strict_types=1);
$admin_title = 'تنظیمات سایت';
$admin_active = 'settings';
require_once __DIR__ . '/inc/auth.php';

$field_defaults = [
    'home_kicker' => 'پورتفولیو — نسخهٔ ۱۴۰۵', 'hero_accent' => 'مانده',
    'home_work_kicker' => 'کارهای منتخب', 'home_work_title' => 'پروژه‌ها، مسئله به مسئله',
    'home_work_intro' => 'گزیده‌ای از پروژه‌های اخیر؛ از هویت بصری و لوگو تا پوستر، کتاب، بسته‌بندی و تایپوگرافی.',
    'home_services_title' => 'چه کاری از دستم برمی‌آید',
    'home_services_intro' => 'از تعریف مسئله تا تحویل جزئیات نهایی، کنار شما هستم.',
    'home_cta_kicker' => 'قدم بعدی', 'home_cta_title' => "پروژه‌ای در ذهن دارید؟\nبیایید جدی حرف بزنیم.",
    'home_studio_label' => 'استودیو', 'about_path_title' => 'از آژانس تا استودیوی مستقل',
    'about_tools_title' => 'جعبه‌ابزار روزانه',
    'about_tools_note' => 'اما ابزار واقعی من این‌هاست: یک خط‌کش فلزی، کاغذ پوستی، مرکب چین و کلی کلیپ برداشتی از دیوار تهران.',
    'about_awards_title' => 'تأییدهای بیرونی', 'services_title' => 'راه‌هایی برای اینکه با هم کار کنیم.',
    'services_process_title' => 'چهار قدم، بدون سورپرایز',
    'services_process_intro' => 'در هر مرحله دقیقاً می‌دانید کجاییم و قدم بعدی چیست.',
    'contact_title' => 'بیایید حرف بزنیم.',
    'contact_success' => 'پیام شما ثبت شد؛ معمولاً کمتر از ۲۴ ساعت جواب می‌دهم. ✳',
    'contact_response' => 'ساعت پاسخ‌گویی', 'work_title' => 'کارها',
    'work_intro' => 'همهٔ پروژه‌ها: هویت بصری، لوگو، پوستر، کتاب، بسته‌بندی، کارت ویزیت، بنر و تایپوگرافی.',
];

$fields = [
    'site_name'    => ['نام سایت', 'text'],
    'site_name_en' => ['نام لاتین', 'text'],
    'role'         => ['عنوان حرفه‌ای', 'text'],
    'city'         => ['شهر', 'text'],
    'email'        => ['ایمیل', 'text'],
    'phone'        => ['تلفن', 'text'],
    'availability' => ['وضعیت دسترس‌پذیری', 'text'],
    'home_kicker' => ['خانه — برچسب بالای عنوان', 'text'],
    'hero_accent' => ['خانه — واژهٔ رنگی تیتر', 'text'],
    'home_work_kicker' => ['خانه — برچسب بخش کارها', 'text'],
    'home_work_title' => ['خانه — تیتر بخش کارها', 'text'],
    'home_work_intro' => ['خانه — توضیح بخش کارها', 'rich'],
    'home_services_title' => ['خانه — تیتر خدمات', 'text'],
    'home_services_intro' => ['خانه — توضیح خدمات', 'rich'],
    'home_cta_kicker' => ['خانه — برچسب دعوت به اقدام', 'text'],
    'home_cta_title' => ['خانه — تیتر دعوت به اقدام (دو خط با Enter)', 'rich'],
    'home_studio_label' => ['خانه — برچسب استودیو', 'text'],
    'about_path_title' => ['درباره — تیتر مسیر حرفه‌ای', 'text'],
    'about_tools_title' => ['درباره — تیتر ابزارها', 'text'],
    'about_tools_note' => ['درباره — توضیح ابزارها', 'rich'],
    'about_awards_title' => ['درباره — تیتر جوایز', 'text'],
    'services_title' => ['خدمات — تیتر اصلی', 'text'],
    'services_process_title' => ['خدمات — تیتر فرآیند', 'text'],
    'services_process_intro' => ['خدمات — توضیح فرآیند', 'rich'],
    'contact_title' => ['تماس — تیتر اصلی', 'text'],
    'contact_success' => ['تماس — پیام موفقیت', 'text'],
    'contact_response' => ['تماس — عنوان ساعت پاسخ‌گویی', 'text'],
    'work_title' => ['کارها — تیتر آرشیو', 'text'],
    'work_intro' => ['کارها — توضیح آرشیو', 'rich'],
    'hero_l1'      => ['هیرو — خط ۱', 'text'],
    'hero_l2'      => ['هیرو — خط ۲', 'text'],
    'hero_l3'      => ['هیرو — خط ۳', 'text'],
    'hero_intro'   => ['معرفی کوتاه هیرو', 'rich'],
    'hero_areas'   => ['حوزه‌ها (با | جدا کنید)', 'text'],
    'marquee'      => ['نوار متحرک (با | جدا کنید)', 'text'],
    'studio_title' => ['عنوان بخش استودیو', 'text'],
    'studio_text'  => ['متن بخش استودیو', 'rich'],
    'about_title'  => ['تیتر درباره', 'textarea'],
    'about_intro'  => ['مقدمهٔ درباره', 'rich'],
    'about_p1'     => ['پاراگراف ۱ درباره', 'rich'],
    'about_p2'     => ['پاراگراف ۲ درباره', 'rich'],
    'about_p3'     => ['پاراگراف ۳ درباره', 'rich'],
    'services_intro' => ['مقدمهٔ صفحهٔ خدمات', 'rich'],
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
  <form method="post" id="homepage-content" class="border border-line bg-paper p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_settings">
    <h2 class="mb-2 text-lg font-extrabold">متن‌ها و محتوای صفحات</h2><p class="mb-6 text-sm leading-7 text-soft">محتوای صفحهٔ نخست، درباره، خدمات و تماس را از این فرم مدیریت کنید. قالب‌بندی امن برای فیلدهای متنی در دسترس است.</p>
    <div class="grid gap-4 md:grid-cols-2">
      <?php foreach ($fields as $key => [$label, $type]): ?>
        <label class="block <?= in_array($type, ['textarea', 'rich'], true) ? 'md:col-span-2' : '' ?>">
          <span class="mono-label mb-2 block text-soft"><?= e($label) ?></span>
          <?php if ($type === 'textarea' || $type === 'rich'): ?>
            <?php if ($type === 'rich'): ?>
              <p class="mb-1 text-xs leading-6 text-soft">قالب مجاز: **پررنگ**، *کج*، Enter برای شکست خط و خط خالی برای پاراگراف. HTML پذیرفته نمی‌شود.</p>
              <span class="mb-1 flex gap-1" role="group" aria-label="قالب‌بندی متن">
                <button type="button" class="btn btn-sm" data-format="**">پررنگ</button>
                <button type="button" class="btn btn-sm" data-format="*">کج</button>
              </span>
            <?php endif; ?>
            <textarea name="<?= e($key) ?>" rows="<?= $type === 'rich' ? '4' : '3' ?>" class="input"<?= $type === 'rich' ? ' data-rich-editor' : '' ?>><?= e(setting($key, $field_defaults[$key] ?? '')) ?></textarea>
          <?php else: ?>
            <input name="<?= e($key) ?>" class="input" value="<?= e(setting($key, $field_defaults[$key] ?? '')) ?>">
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
