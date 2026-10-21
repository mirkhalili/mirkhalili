<?php
declare(strict_types=1);
$admin_title = 'پیام‌های تماس';
$admin_active = 'messages';
require_once __DIR__ . '/inc/auth.php';
admin_require_module('messages', ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete') {
        safe_delete('messages', (int) ($_POST['id'] ?? 0));
        flash_set('پیام حذف شد.');
    }
    if ($action === 'read') {
        q('UPDATE messages SET is_read = 1 WHERE id = ?', [(int) ($_POST['id'] ?? 0)]);
        flash_set('پیام خوانده شد.');
    }
    if ($action === 'read_all') {
        q('UPDATE messages SET is_read = 1');
        flash_set('همهٔ پیام‌ها خوانده شدند.');
    }
    redirect('/admin/messages.php');
}

require __DIR__ . '/inc/header.php';
$rows = q_all('SELECT * FROM messages ORDER BY is_read, created_at DESC, id DESC');
?>

<div class="mb-6 flex items-center justify-between">
  <p class="text-sm text-soft"><?= count($rows) ?> پیام</p>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="read_all">
    <button class="btn btn-sm">علامت‌گذاری همه به‌عنوان خوانده‌شده</button>
  </form>
</div>

<?php if ($rows === []): ?>
  <p class="border border-dashed border-line bg-paper py-16 text-center text-soft">هنوز پیامی نیامده است.</p>
<?php else: ?>
  <div class="space-y-3">
    <?php foreach ($rows as $m): ?>
      <details class="border border-line bg-paper <?= (int) $m['is_read'] ? '' : 'border-s-4 border-s-accent' ?>">
        <summary class="flex cursor-pointer flex-wrap items-center justify-between gap-3 p-4">
          <div class="min-w-0">
            <p class="font-bold"><?= e($m['name']) ?> <span class="mono-label ms-2 text-soft"><?= e($m['project_type']) ?></span></p>
            <p class="mono-label mt-1 text-soft" dir="ltr"><?= e($m['email']) ?></p>
          </div>
          <span class="mono-label shrink-0 text-soft"><?= e($m['created_at']) ?></span>
        </summary>
        <div class="border-t border-line p-5">
          <p class="leading-8"><?= nl2br(e($m['body'])) ?></p>
          <div class="mt-5 flex flex-wrap gap-2">
            <a class="btn btn-sm" href="mailto:<?= e($m['email']) ?>">پاسخ با ایمیل</a>
            <?php if (!(int) $m['is_read']): ?>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="read"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="btn btn-sm">خوانده شد</button></form>
            <?php endif; ?>
            <form method="post" onsubmit="return confirm('حذف این پیام؟')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><button class="btn btn-sm btn-danger">حذف</button></form>
          </div>
        </div>
      </details>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/inc/footer.php'; ?>
