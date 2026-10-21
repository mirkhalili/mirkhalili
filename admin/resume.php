<?php
declare(strict_types=1);
$admin_title = 'رزومه، جوایز، آمار و مهارت‌ها';
$admin_active = 'resume';
require_once __DIR__ . '/inc/auth.php';

/** بخش‌های «حذف همه + درج مجدد» */
$sections = [
    'timeline' => ['title' => 'مسیر حرفه‌ای', 'cols' => ['period' => 'بازهٔ زمانی', 'title' => 'عنوان', 'description' => 'توضیح']],
    'awards'   => ['title' => 'جوایز', 'cols' => ['year_fa' => 'سال', 'title' => 'عنوان جایزه', 'project' => 'پروژه']],
    'stats'    => ['title' => 'آمار', 'cols' => ['value' => 'عدد', 'label' => 'برچسب']],
    'skills'   => ['title' => 'مهارت‌ها / ابزارها', 'cols' => ['name' => 'نام ابزار']],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $sec = (string) ($_POST['section'] ?? '');
    if (isset($sections[$sec])) {
        $cols = array_keys($sections[$sec]['cols']);
        q("DELETE FROM {$sec}");
        $sort = 0;
        foreach ((array) ($_POST['rows'] ?? []) as $r) {
            $values = [];
            $empty = true;
            foreach ($cols as $c) {
                $v = trim((string) ($r[$c] ?? ''));
                if ($v !== '') {
                    $empty = false;
                }
                $values[] = $v;
            }
            if ($empty) {
                continue;
            }
            $sort++;
            $ph = implode(', ', array_fill(0, count($cols) + 1, '?'));
            q("INSERT INTO {$sec} (" . implode(', ', $cols) . ", sort) VALUES ({$ph})", array_merge($values, [$sort]));
        }
        flash_set('بخش «' . $sections[$sec]['title'] . '» ذخیره شد.');
        redirect('/admin/resume.php');
    }
}

require __DIR__ . '/inc/header.php';
?>

<div class="grid gap-8 xl:grid-cols-2">
  <?php foreach ($sections as $key => $meta): $rows = q_all("SELECT * FROM {$key} ORDER BY sort, id"); if ($rows === []) { $rows = [array_fill_keys(array_keys($meta['cols']), '')]; } ?>
    <form method="post" class="h-fit border border-line bg-paper p-6">
      <?= csrf_field() ?>
      <input type="hidden" name="section" value="<?= e($key) ?>">
      <h2 class="mb-4 text-lg font-extrabold"><?= e($meta['title']) ?></h2>
      <div data-repeater="rows-<?= e($key) ?>">
        <?php foreach ($rows as $i => $r): ?>
          <div class="rep-row mb-2 grid gap-2 <?= count($meta['cols']) > 2 ? 'md:grid-cols-[8rem_1fr_1fr_auto]' : 'md:grid-cols-[10rem_1fr_auto]' ?>">
            <?php foreach ($meta['cols'] as $c => $label): ?>
              <input name="rows[<?= $i ?>][<?= e($c) ?>]" class="input" value="<?= e((string) ($r[$c] ?? '')) ?>" placeholder="<?= e($label) ?>">
            <?php endforeach; ?>
            <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-3 flex gap-2">
        <button type="button" class="btn btn-sm" data-rep-add="rows-<?= e($key) ?>">+ افزودن ردیف</button>
        <button class="btn btn-primary btn-sm">ذخیره</button>
      </div>
    </form>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>
