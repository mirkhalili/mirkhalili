<?php
declare(strict_types=1);
$admin_title = 'پروژه‌ها';
$admin_active = 'projects';
require_once __DIR__ . '/inc/auth.php';

/* ---------------- اکشن‌ها ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        safe_delete('projects', (int) ($_POST['id'] ?? 0));
        flash_set('پروژه حذف شد.');
        redirect('/admin/projects.php');
    }

    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        q('UPDATE projects SET published = 1 - published WHERE id = ?', [$id]);
        flash_set('وضعیت انتشار تغییر کرد.');
        redirect('/admin/projects.php');
    }

    if ($action === 'del_image') {
        $img = q_one('SELECT * FROM project_images WHERE id = ?', [(int) ($_POST['id'] ?? 0)]);
        if ($img) {
            $file = dirname(__DIR__, 2) . '/' . $img['path'];
            if (is_file($file)) {
                @unlink($file);
            }
            safe_delete('project_images', (int) $img['id']);
        }
        flash_set('تصویر گالری حذف شد.');
        redirect('/admin/projects.php?edit=' . (int) ($_POST['project_id'] ?? 0));
    }

    if ($action === 'save') {
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            if ($title === '') {
                throw new RuntimeException('عنوان پروژه الزامی است.');
            }
            $slugIn = trim((string) ($_POST['slug'] ?? ''));
            $base = $slugIn !== '' ? $slugIn : (string) ($_POST['title_en'] ?? '');
            $slug = unique_slug($base !== '' ? $base : $title, 'projects', $id ?: null);

            $coverPath = trim((string) ($_POST['cover_current'] ?? ''));
            $uploaded = handle_upload('cover');
            if ($uploaded !== null) {
                $coverPath = $uploaded;
            }

            $imgPath = handle_upload('gallery_image');

            $fields = [
                'title'          => $title,
                'title_en'       => trim((string) ($_POST['title_en'] ?? '')),
                'category_id'    => (int) ($_POST['category_id'] ?? 0) ?: null,
                'year_fa'        => trim((string) ($_POST['year_fa'] ?? '')),
                'year_en'        => trim((string) ($_POST['year_en'] ?? '')),
                'client'         => trim((string) ($_POST['client'] ?? '')),
                'role_fa'        => trim((string) ($_POST['role_fa'] ?? '')),
                'excerpt'        => trim((string) ($_POST['excerpt'] ?? '')),
                'description'    => trim((string) ($_POST['description'] ?? '')),
                'cover'          => $coverPath,
                'specimen_label' => trim((string) ($_POST['specimen_label'] ?? '')),
                'specimen_sample' => trim((string) ($_POST['specimen_sample'] ?? '')),
                'sort'           => (int) ($_POST['sort'] ?? 0),
                'published'      => isset($_POST['published']) ? 1 : 0,
            ];

            if ($id > 0) {
                $sets = implode(', ', array_map(fn ($c) => "{$c} = ?", array_keys($fields)));
                q("UPDATE projects SET {$sets} WHERE id = ?", array_merge(array_values($fields), [$id]));
            } else {
                $cols = implode(', ', array_keys($fields));
                $ph = implode(', ', array_fill(0, count($fields), '?'));
                q("INSERT INTO projects ({$cols}, slug) VALUES ({$ph}, ?)", array_merge(array_values($fields), [$slug]));
                $id = (int) db()->lastInsertId();
            }

            sync_children('project_services', 'project_id', $id, $_POST['svc'] ?? []);
            sync_children('project_palette', 'project_id', $id, $_POST['pal'] ?? []);
            sync_children('project_metrics', 'project_id', $id, $_POST['met'] ?? []);

            if ($imgPath !== null) {
                $cap = trim((string) ($_POST['gallery_caption'] ?? ''));
                $sort = (int) q_value('SELECT COALESCE(MAX(sort),0)+1 FROM project_images WHERE project_id = ?', [$id], 1);
                q('INSERT INTO project_images (project_id, path, caption, sort) VALUES (?, ?, ?, ?)', [$id, $imgPath, $cap, $sort]);
            }

            flash_set('پروژه ذخیره شد.');
            redirect('/admin/projects.php');
        } catch (Throwable $ex) {
            flash_set($ex->getMessage(), 'err');
            redirect('/admin/projects.php?edit=' . (int) ($_POST['id'] ?? 0) . '#form');
        }
    }
}

/* ---------------- حالت فرم یا فهرست ---------------- */
$editing = null;
$showForm = isset($_GET['new']);
if (isset($_GET['edit'])) {
    $editing = q_one('SELECT * FROM projects WHERE id = ?', [(int) $_GET['edit']]);
    $showForm = $editing !== null;
}

if ($showForm) {
    $categories = categories_all();
    $eServices = $editing ? q_all('SELECT * FROM project_services WHERE project_id = ? ORDER BY sort, id', [$editing['id']]) : [];
    $ePalette  = $editing ? q_all('SELECT * FROM project_palette WHERE project_id = ? ORDER BY sort, id', [$editing['id']]) : [];
    $eMetrics  = $editing ? q_all('SELECT * FROM project_metrics WHERE project_id = ? ORDER BY sort, id', [$editing['id']]) : [];
    $eImages   = $editing ? q_all('SELECT * FROM project_images WHERE project_id = ? ORDER BY sort, id', [$editing['id']]) : [];

    require __DIR__ . '/inc/header.php';
    ?>
    <form id="form" method="post" enctype="multipart/form-data" class="grid gap-8 xl:grid-cols-[1fr_20rem]">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
      <input type="hidden" name="cover_current" value="<?= e($editing['cover'] ?? '') ?>">

      <div class="space-y-8">
        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">اطلاعات اصلی</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <label class="block md:col-span-2">
              <span class="mono-label mb-2 block text-soft">عنوان پروژه *</span>
              <input name="title" required class="input" value="<?= e($editing['title'] ?? '') ?>">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">عنوان لاتین</span>
              <input name="title_en" class="input" dir="ltr" value="<?= e($editing['title_en'] ?? '') ?>">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">slug (اختیاری — خالی یعنی خودکار)</span>
              <input name="slug" class="input" dir="ltr" value="<?= e($editing['slug'] ?? '') ?>">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">دسته‌بندی</span>
              <select name="category_id" class="input">
                <option value="0">بدون دسته</option>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= (int) $c['id'] ?>"<?= (int) ($editing['category_id'] ?? 0) === (int) $c['id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">ترتیب نمایش</span>
              <input name="sort" type="number" class="input" value="<?= (int) ($editing['sort'] ?? 0) ?>">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">سال فارسی</span>
              <input name="year_fa" class="input" value="<?= e($editing['year_fa'] ?? '') ?>" placeholder="۱۴۰۴">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">سال میلادی</span>
              <input name="year_en" class="input" dir="ltr" value="<?= e($editing['year_en'] ?? '') ?>" placeholder="2025">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">مشتری</span>
              <input name="client" class="input" value="<?= e($editing['client'] ?? '') ?>">
            </label>
            <label class="block">
              <span class="mono-label mb-2 block text-soft">نقش شما</span>
              <input name="role_fa" class="input" value="<?= e($editing['role_fa'] ?? '') ?>">
            </label>
          </div>
          <label class="mt-4 block">
            <span class="mono-label mb-2 block text-soft">خلاصه (یک جمله)</span>
            <textarea name="excerpt" rows="2" class="input"><?= e($editing['excerpt'] ?? '') ?></textarea>
          </label>
          <label class="mt-4 block">
            <span class="mono-label mb-2 block text-soft">شرح پروژه (پاراگراف‌ها با یک خط خالی)</span>
            <textarea name="description" rows="8" class="input"><?= e($editing['description'] ?? '') ?></textarea>
          </label>
          <label class="mt-4 flex items-center gap-3">
            <input type="checkbox" name="published" value="1" class="size-4 accent-[#e4471c]"<?= (int) ($editing['published'] ?? 1) === 1 ? ' checked' : '' ?>>
            <span class="text-sm font-bold">منتشر شده باشد</span>
          </label>
        </section>

        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">خدمات پروژه</h2>
          <div data-repeater="svc">
            <?php $rows = $eServices !== [] ? $eServices : [['label' => '']]; foreach ($rows as $i => $r): ?>
              <div class="rep-row mb-2 flex gap-2">
                <input name="svc[<?= $i ?>][label]" class="input" value="<?= e($r['label']) ?>" placeholder="مثلاً طراحی نشان">
                <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm" data-rep-add="svc">+ افزودن ردیف</button>
        </section>

        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">پالت رنگ</h2>
          <div data-repeater="pal">
            <?php $rows = $ePalette !== [] ? $ePalette : [['hex' => '', 'name' => '']]; foreach ($rows as $i => $r): ?>
              <div class="rep-row mb-2 flex gap-2">
                <input name="pal[<?= $i ?>][hex]" class="input w-32" dir="ltr" value="<?= e($r['hex']) ?>" placeholder="#E4471C">
                <input name="pal[<?= $i ?>][name]" class="input" value="<?= e($r['name']) ?>" placeholder="نام رنگ">
                <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm" data-rep-add="pal">+ افزودن ردیف</button>
        </section>

        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">معیارها</h2>
          <div data-repeater="met">
            <?php $rows = $eMetrics !== [] ? $eMetrics : [['value' => '', 'label' => '']]; foreach ($rows as $i => $r): ?>
              <div class="rep-row mb-2 flex gap-2">
                <input name="met[<?= $i ?>][value]" class="input w-28" value="<?= e($r['value']) ?>" placeholder="۴۸">
                <input name="met[<?= $i ?>][label]" class="input" value="<?= e($r['label']) ?>" placeholder="صفحه راهنمای برند">
                <button type="button" class="btn btn-danger btn-sm rep-del" aria-label="حذف ردیف">✕</button>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-sm" data-rep-add="met">+ افزودن ردیف</button>
        </section>
      </div>

      <aside class="space-y-8">
        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">تصویر شاخص</h2>
          <?php if (!empty($editing['cover'])): ?>
            <img src="<?= e($editing['cover']) ?>" alt="" class="mb-3 aspect-4/3 w-full object-cover">
          <?php endif; ?>
          <input type="file" name="cover" accept="image/*" class="block w-full text-sm">
          <p class="mt-2 text-xs text-soft">JPG/PNG/WEBP تا ۸ مگابایت. اگر فایلی انتخاب نکنید، تصویر فعلی می‌ماند.</p>
        </section>

        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">گالری</h2>
          <?php if ($eImages !== []): ?>
            <ul class="mb-4 space-y-2">
              <?php foreach ($eImages as $img): ?>
                <li class="flex items-center gap-3">
                  <img src="<?= e($img['path']) ?>" alt="" class="size-12 object-cover">
                  <span class="min-w-0 flex-1 truncate text-sm"><?= e($img['caption']) ?></span>
                  <form method="post" onsubmit="return confirm('حذف این تصویر؟')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="del_image">
                    <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
                    <input type="hidden" name="project_id" value="<?= (int) $editing['id'] ?>">
                    <button class="btn btn-sm btn-danger">حذف</button>
                  </form>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <input type="file" name="gallery_image" accept="image/*" class="block w-full text-sm">
          <input name="gallery_caption" class="input mt-2" placeholder="توضیح تصویر جدید (اختیاری)">
        </section>

        <section class="border border-line bg-paper p-6">
          <h2 class="mb-4 text-lg font-extrabold">اسپسیمن</h2>
          <label class="block">
            <span class="mono-label mb-2 block text-soft">برچسب</span>
            <input name="specimen_label" class="input" value="<?= e($editing['specimen_label'] ?? '') ?>">
          </label>
          <label class="mt-3 block">
            <span class="mono-label mb-2 block text-soft">نمونهٔ نمایشی</span>
            <input name="specimen_sample" class="input" value="<?= e($editing['specimen_sample'] ?? '') ?>">
          </label>
        </section>

        <div class="sticky bottom-4 flex gap-3">
          <button class="btn btn-primary flex-1 py-3.5">ذخیرهٔ پروژه</button>
          <a class="btn py-3.5" href="/admin/projects.php">انصراف</a>
        </div>
      </aside>
    </form>
    <?php
    require __DIR__ . '/inc/footer.php';
    exit;
}

/* ---------------- فهرست ---------------- */
require __DIR__ . '/inc/header.php';
$rows = q_all('SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.sort, p.id');
?>

<div class="mb-6 flex items-center justify-between">
  <p class="text-sm text-soft"><?= count($rows) ?> پروژه</p>
  <a href="?new=1" class="btn btn-primary">+ پروژهٔ جدید</a>
</div>

<section class="border border-line bg-paper">
  <table class="admin-table">
    <thead><tr><th>تصویر</th><th>عنوان</th><th>دسته</th><th>سال</th><th>وضعیت</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><img src="<?= e($r['cover']) ?>" alt="" class="h-12 w-16 object-cover"></td>
          <td class="font-bold"><?= e($r['title']) ?><span class="mono-label block text-soft" dir="ltr">/work/<?= e($r['slug']) ?></span></td>
          <td><?= e($r['category_name'] ?? '—') ?></td>
          <td><?= e($r['year_fa']) ?></td>
          <td><?= (int) $r['published'] ? '<span class="badge-ok">منتشر</span>' : '<span class="badge-draft">پیش‌نویس</span>' ?></td>
          <td class="actions">
            <a class="btn btn-sm" href="?edit=<?= (int) $r['id'] ?>">ویرایش</a>
            <form method="post" class="inline">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="btn btn-sm"><?= (int) $r['published'] ? 'پیش‌نویس' : 'انتشار' ?></button>
            </form>
            <form method="post" class="inline" onsubmit="return confirm('حذف کامل این پروژه؟')">
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

<?php require __DIR__ . '/inc/footer.php'; ?>
