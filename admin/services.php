<?php
declare(strict_types=1);
$admin_title = 'خدمات و فرآیند';
$admin_active = 'services';
require_once __DIR__ . '/inc/auth.php';
admin_require_module('services', ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'save_service') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') {
            flash_set('عنوان خدمت الزامی است.', 'err');
        } else {
            $data = [
                'title'       => $title,
                'title_en'    => trim((string) ($_POST['title_en'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'sort'        => (int) ($_POST['sort'] ?? 0),
            ];
            if ($id > 0) {
                q('UPDATE services SET title = ?, title_en = ?, description = ?, sort = ? WHERE id = ?', array_merge(array_values($data), [$id]));
            } else {
                q('INSERT INTO services (title, title_en, description, sort) VALUES (?, ?, ?, ?)', array_values($data));
                $id = (int) db()->lastInsertId();
            }
            sync_children('service_items', 'service_id', $id, $_POST['items'] ?? []);
            flash_set('خدمت ذخیره شد.');
        }
        redirect('/admin/services.php');
    }

    if ($action === 'delete_service') {
        safe_delete('services', (int) ($_POST['id'] ?? 0));
        flash_set('خدمت حذف شد.');
        redirect('/admin/services.php');
    }

    if ($action === 'save_page_settings') {
        foreach (['services_title', 'services_intro', 'services_process_title', 'services_process_intro', 'process_note'] as $key) {
            $value = trim((string) ($_POST[$key] ?? ''));
            q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)', [$key, $value]);
        }
        flash_set('تنظیمات صفحهٔ خدمات ذخیره شد.');
        redirect('/admin/services.php');
    }

    if ($action === 'save_process') {
        $rows = $_POST['proc'] ?? [];
        q('DELETE FROM process_steps');
        $sort = 0;
        foreach ($rows as $r) {
            $t = trim((string) ($r['title'] ?? ''));
            if ($t === '') {
                continue;
            }
            $sort++;
            q('INSERT INTO process_steps (title, description, sort) VALUES (?, ?, ?)', [$t, trim((string) ($r['description'] ?? '')), $sort]);
        }
        flash_set('مراحل فرآیند ذخیره شد.');
        redirect('/admin/services.php');
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $editing = q_one('SELECT * FROM services WHERE id = ?', [(int) $_GET['edit']]);
}
$eItems = $editing ? q_all('SELECT * FROM service_items WHERE service_id = ? ORDER BY sort, id', [$editing['id']]) : [];

require __DIR__ . '/inc/header.php';
$services = q_all('SELECT s.*, (SELECT COUNT(*) FROM service_items i WHERE i.service_id = s.id) AS cnt FROM services s ORDER BY s.sort, s.id');
$process = q_all('SELECT * FROM process_steps ORDER BY sort, id');
?>

<div class="grid gap-8 xl:grid-cols-2">
  <!-- فهرست خدمات -->
  <section>
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-extrabold">خدمات استودیو</h2>
      <a href="/admin/services.php?new=1" class="btn btn-primary btn-sm">+ خدمت جدید</a>
    </div>
    <div class="space-y-3">
      <?php foreach ($services as $s): ?>
        <div class="flex items-center justify-between gap-4 border border-line bg-paper p-4">
          <div class="min-w-0">
            <p class="font-bold"><?= e($s['title']) ?> <span class="mono-label text-soft" dir="ltr"><?= e($s['title_en']) ?></span></p>
            <p class="text-sm text-soft"><?= (int) $s['cnt'] ?> زیرآیتم · ترتیب <?= (int) $s['sort'] ?></p>
          </div>
          <div class="flex shrink-0 gap-2">
            <a class="btn btn-sm" href="?edit=<?= (int) $s['id'] ?>">ویرایش</a>
            <form method="post" onsubmit="return confirm('حذف این خدمت؟')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete_service">
              <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
              <button class="btn btn-sm btn-danger">حذف</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <h2 class="mb-4 mt-10 text-lg font-extrabold">متن‌ها و تنظیمات صفحهٔ خدمات</h2>
    <form method="post" class="mb-8 border border-line bg-paper p-5">
      <?= csrf_field() ?><input type="hidden" name="action" value="save_page_settings">
      <?php foreach ([['services_title','عنوان اصلی'],['services_intro','مقدمه'],['services_process_title','عنوان فرآیند'],['services_process_intro','توضیح فرآیند'],['process_note','یادداشت زمان‌بندی']] as [$key,$label]): ?>
        <label class="mb-3 block text-sm font-bold"><?= e($label) ?><textarea class="input mt-1" name="<?= e($key) ?>" rows="2"><?= e(setting($key)) ?></textarea></label>
      <?php endforeach; ?>
      <button class="btn btn-primary btn-sm">ذخیرهٔ متن‌های صفحه</button>
    </form>

    <h2 class="mb-4 mt-10 text-lg font-extrabold">مراحل فرآیند</h2>
    <form method="post" class="border border-line bg-paper p-5">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save_process">
      <div data-repeater="proc">
        <?php $rows = $process !== [] ? $process : [['title' => '', 'description' => '']]; foreach ($rows as $i => $r): ?>
          <div class="rep-row mb-2 grid gap-2 md:grid-cols-[12rem_1fr_auto]">
            <input name="proc[<?= $i ?>][title]" class="input" value="<?= e($r['title']) ?>" placeholder="عنوان مرحله">
            <input name="proc[<?= $i ?>][description]" class="input" value="<?= e($r['description']) ?>" placeholder="توضیح کوتاه">
            <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="flex gap-2">
        <button type="button" class="btn btn-sm" data-rep-add="proc">+ افزودن مرحله</button>
        <button class="btn btn-primary btn-sm">ذخیرهٔ فرآیند</button>
      </div>
    </form>
  </section>

  <!-- فرم خدمت -->
  <form method="post" class="h-fit border border-line bg-paper p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_service">
    <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
    <h2 class="mb-4 text-lg font-extrabold"><?= $editing ? 'ویرایش خدمت' : (isset($_GET['new']) ? 'خدمت جدید' : 'خدمت جدید') ?></h2>
    <div class="grid gap-4 sm:grid-cols-2">
      <label class="block">
        <span class="mono-label mb-2 block text-soft">عنوان *</span>
        <input name="title" required class="input" value="<?= e($editing['title'] ?? '') ?>">
      </label>
      <label class="block">
        <span class="mono-label mb-2 block text-soft">عنوان لاتین</span>
        <input name="title_en" class="input" dir="ltr" value="<?= e($editing['title_en'] ?? '') ?>">
      </label>
    </div>
    <label class="mt-4 block">
      <span class="mono-label mb-2 block text-soft">توضیح</span>
      <textarea name="description" rows="3" class="input"><?= e($editing['description'] ?? '') ?></textarea>
    </label>
    <label class="mt-4 block">
      <span class="mono-label mb-2 block text-soft">ترتیب</span>
      <input name="sort" type="number" class="input" value="<?= (int) ($editing['sort'] ?? 0) ?>">
    </label>

    <p class="mono-label mb-2 mt-6 text-soft">زیرآیتم‌ها</p>
    <div data-repeater="items">
      <?php $rows = $eItems !== [] ? $eItems : [['label' => '']]; foreach ($rows as $i => $r): ?>
        <div class="rep-row mb-2 flex gap-2">
          <input name="items[<?= $i ?>][label]" class="input" value="<?= e($r['label']) ?>" placeholder="مثلاً طراحی نشان">
          <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-4 flex gap-3">
      <button type="button" class="btn btn-sm" data-rep-add="items">+ افزودن ردیف</button>
      <button class="btn btn-primary">ذخیرهٔ خدمت</button>
      <?php if ($editing): ?><a class="btn" href="/admin/services.php">انصراف</a><?php endif; ?>
    </div>
  </form>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>
