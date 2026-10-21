<?php
declare(strict_types=1);
$admin_title = 'داشبورد';
$admin_active = 'index';
require __DIR__ . '/inc/header.php';

$counts = [
    'پروژهٔ منتشرشده' => (int) q_value('SELECT COUNT(*) FROM projects WHERE published = 1', [], 0),
    'پیش‌نویس'        => (int) q_value('SELECT COUNT(*) FROM projects WHERE published = 0', [], 0),
    'دسته‌بندی'       => (int) q_value('SELECT COUNT(*) FROM categories', [], 0),
    'پیام خوانده‌نشده' => unread_messages_count(),

];
if (admin_can('visits')) {
    $counts['بازدید امروز'] = (int) q_value('SELECT COUNT(*) FROM site_visits WHERE visited_at >= CURRENT_DATE', [], 0);
}
$latest = q_all('SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT 5');
$recent = q_all('SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.updated_at DESC LIMIT 5');
?>

<div class="mb-6 flex flex-wrap gap-3">
  <a class="btn btn-primary" href="/admin/settings.php#homepage-content">ویرایش محتوای صفحهٔ نخست</a>
  <a class="btn" href="/admin/projects.php">مدیریت نمونه‌کارها</a>
  <?php if (admin_can('users')): ?><a class="btn" href="/admin/users.php">کاربران</a><?php endif; ?>
  <?php if (admin_can('visits')): ?><a class="btn" href="/admin/visits.php">گزارش بازدید</a><?php endif; ?>
</div>

<div class="grid gap-px border border-line bg-line sm:grid-cols-2 lg:grid-cols-5">
  <?php foreach ($counts as $label => $value): ?>
    <div class="bg-paper p-6">
      <p class="text-3xl font-black"><?= $value ?></p>
      <p class="mono-label mt-2 text-soft"><?= e($label) ?></p>
    </div>
  <?php endforeach; ?>
</div>

<div class="mt-8 grid gap-8 lg:grid-cols-2">
  <section class="border border-line bg-paper p-6">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-extrabold">آخرین پیام‌ها</h2>
      <a href="/admin/messages.php" class="link-slide text-sm font-bold">همهٔ پیام‌ها ←</a>
    </div>
    <?php if ($latest === []): ?>
      <p class="py-8 text-center text-soft">هنوز پیامی نیامده است.</p>
    <?php else: ?>
      <ul class="divide-y divide-line">
        <?php foreach ($latest as $m): ?>
          <li class="flex items-baseline justify-between gap-4 py-3">
            <div class="min-w-0">
              <p class="truncate font-bold"><?= e($m['name']) ?> <span class="mono-label text-soft"><?= e($m['project_type']) ?></span></p>
              <p class="truncate text-sm text-soft"><?= e($m['body']) ?></p>
            </div>
            <?php if (!(int) $m['is_read']): ?><span class="shrink-0 rounded-full bg-accent px-2 py-0.5 text-xs font-black text-paper">نو</span><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section class="border border-line bg-paper p-6">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-extrabold">آخرین ویرایش‌های پروژه</h2>
      <a href="/admin/projects.php?new=1" class="border border-ink px-3 py-1.5 text-sm font-bold transition-colors hover:bg-ink hover:text-paper">+ پروژهٔ جدید</a>
    </div>
    <ul class="divide-y divide-line">
      <?php foreach ($recent as $p): ?>
        <li class="flex items-center justify-between gap-4 py-3">
          <div class="flex min-w-0 items-center gap-3">
            <img src="<?= e($p['cover']) ?>" alt="" class="size-10 shrink-0 object-cover">
            <div class="min-w-0">
              <p class="truncate font-bold"><?= e($p['title']) ?></p>
              <p class="text-sm text-soft"><?= e($p['category_name'] ?? 'بدون دسته') ?> · <?= (int) $p['published'] ? 'منتشرشده' : 'پیش‌نویس' ?></p>
            </div>
          </div>
          <a href="/admin/projects.php?edit=<?= (int) $p['id'] ?>" class="shrink-0 text-sm font-bold text-accent">ویرایش</a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>
