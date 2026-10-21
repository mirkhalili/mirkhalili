<?php
declare(strict_types=1);
$admin_title = 'دسته‌بندی‌ها';
$admin_active = 'categories';
require_once __DIR__ . '/inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $sort = (int) ($_POST['sort'] ?? 0);
        if ($name === '') {
            flash_set('نام دسته الزامی است.', 'err');
        } else {
            if ($slug === '') {
                $slug = uniqid('cat-');
            }
            $slug = unique_slug($slug, 'categories', $id ?: null);
            if ($id > 0) {
                q('UPDATE categories SET name = ?, slug = ?, sort = ? WHERE id = ?', [$name, $slug, $sort, $id]);
            } else {
                q('INSERT INTO categories (name, slug, sort) VALUES (?, ?, ?)', [$name, $slug, $sort]);
            }
            flash_set('دسته ذخیره شد.');
        }
        redirect('/admin/categories.php');
    }
    if ($action === 'delete') {
        safe_delete('categories', (int) ($_POST['id'] ?? 0));
        flash_set('دسته حذف شد؛ پروژه‌های آن بدون دسته ماندند.');
        redirect('/admin/categories.php');
    }
}

require __DIR__ . '/inc/header.php';
$rows = q_all('SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id = c.id) AS cnt FROM categories c ORDER BY c.sort, c.id');
$edit = null;
if (isset($_GET['edit'])) {
    $edit = q_one('SELECT * FROM categories WHERE id = ?', [(int) $_GET['edit']]);
}
?>

<div class="grid gap-8 lg:grid-cols-[1fr_22rem]">
  <section class="border border-line bg-paper">
    <table class="admin-table">
      <thead><tr><th>نام</th><th>slug</th><th>ترتیب</th><th>پروژه‌ها</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="font-bold"><?= e($r['name']) ?></td>
            <td class="mono-label text-soft" dir="ltr"><?= e($r['slug']) ?></td>
            <td><?= (int) $r['sort'] ?></td>
            <td><?= (int) $r['cnt'] ?></td>
            <td class="actions">
              <a class="btn btn-sm" href="?edit=<?= (int) $r['id'] ?>">ویرایش</a>
              <form method="post" class="inline" onsubmit="return confirm('حذف این دسته؟')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <button class="btn btn-sm btn-danger">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <form method="post" class="h-fit border border-line bg-paper p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
    <h2 class="mb-4 text-lg font-extrabold"><?= $edit ? 'ویرایش دسته' : 'دستهٔ جدید' ?></h2>
    <label class="block">
      <span class="mono-label mb-2 block text-soft">نام دسته *</span>
      <input name="name" required class="input" value="<?= e($edit['name'] ?? '') ?>" placeholder="مثلاً پوستر">
    </label>
    <label class="mt-4 block">
      <span class="mono-label mb-2 block text-soft">slug (لاتین، اختیاری)</span>
      <input name="slug" class="input" dir="ltr" value="<?= e($edit['slug'] ?? '') ?>" placeholder="poster">
    </label>
    <label class="mt-4 block">
      <span class="mono-label mb-2 block text-soft">ترتیب</span>
      <input name="sort" type="number" class="input" value="<?= (int) ($edit['sort'] ?? 0) ?>">
    </label>
    <div class="mt-6 flex gap-3">
      <button class="btn btn-primary">ذخیره</button>
      <?php if ($edit): ?><a class="btn" href="/admin/categories.php">انصراف</a><?php endif; ?>
    </div>
  </form>
</div>

<?php require __DIR__ . '/inc/footer.php'; ?>
