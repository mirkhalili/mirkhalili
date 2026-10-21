<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/data.php';

$cat = isset($_GET['cat']) ? (string) $_GET['cat'] : '';
$categories = categories_all();
$valid = array_column($categories, 'slug');
if ($cat !== '' && !in_array($cat, $valid, true)) {
    $cat = '';
}
$projects = projects_all($cat !== '' ? $cat : null);

$page_title = 'آرشیو کارها';
$page_desc = 'همهٔ پروژه‌ها: هویت بصری، لوگو، پوستر، کتاب، بسته‌بندی، کارت ویزیت، بنر و تایپوگرافی.';
require __DIR__ . '/inc/header.php';
?>

<section class="mx-auto max-w-7xl px-5 pt-32 pb-20 md:px-10 md:pt-44 md:pb-28">
  <div class="flex flex-wrap items-end justify-between gap-6 border-b border-line pb-8" data-reveal>
    <div>
      <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>آرشیو</p>
      <h1 class="text-5xl font-black tracking-tight md:text-7xl">کارها</h1>
    </div>
    <p class="num-lat text-sm text-soft"><?= count($projects) ?> پروژه</p>
  </div>

  <div class="sticky top-16 z-30 -mx-5 border-b border-line bg-paper/90 px-5 py-4 backdrop-blur-md md:top-20 md:-mx-10 md:px-10">
    <div class="flex flex-wrap gap-2" role="group" aria-label="فیلتر دسته‌بندی">
      <a href="/work" class="border px-4 py-2 text-sm font-bold transition-colors <?= $cat === '' ? 'border-ink bg-ink text-paper' : 'border-line text-soft hover:border-ink hover:text-ink' ?>">همه</a>
      <?php foreach ($categories as $c): ?>
        <a href="/work?cat=<?= e($c['slug']) ?>" class="border px-4 py-2 text-sm font-bold transition-colors <?= $cat === $c['slug'] ? 'border-ink bg-ink text-paper' : 'border-line text-soft hover:border-ink hover:text-ink' ?>"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($projects === []): ?>
    <p class="mt-12 border border-dashed border-line py-16 text-center text-soft">هنوز پروژه‌ای در این دسته منتشر نشده است.</p>
  <?php else: ?>
    <div class="mt-12 grid gap-x-8 gap-y-14 md:grid-cols-2">
      <?php foreach ($projects as $i => $p): ?>
        <a href="/work/<?= e($p['slug']) ?>" class="group block" data-reveal style="--reveal-delay:<?= ($i % 2) * 90 ?>ms">
          <div class="zoom-img relative aspect-4/3 overflow-hidden bg-paper-deep">
            <img src="<?= e($p['cover']) ?>" alt="<?= e($p['title']) ?>" class="size-full object-cover" loading="lazy">
            <span class="mono-label absolute top-3 start-3 bg-paper/90 px-2 py-1 text-ink"><?= fa_index($i + 1) ?></span>
          </div>
          <div class="mt-4 flex items-baseline justify-between gap-4 border-t border-line pt-4">
            <h3 class="text-lg font-extrabold tracking-tight transition-colors group-hover:text-accent md:text-xl"><?= e($p['title']) ?></h3>
            <span class="num-lat shrink-0 text-sm text-soft"><?= e($p['year_fa']) ?></span>
          </div>
          <p class="mt-1 text-sm text-soft"><?= e($p['category_name'] ?? '') ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
