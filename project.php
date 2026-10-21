<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/data.php';

$slug = (string) ($_GET['slug'] ?? '');
$p = project_by_slug($slug);
if ($p === null) {
    require __DIR__ . '/404.php';
    exit;
}

$services = project_services((int) $p['id']);
$palette = project_palette((int) $p['id']);
$metrics = project_metrics((int) $p['id']);
$images = project_images((int) $p['id']);

$all = projects_all();
$idx = 0;
foreach ($all as $k => $row) {
    if ((int) $row['id'] === (int) $p['id']) {
        $idx = $k;
        break;
    }
}
$next = $all[($idx + 1) % count($all)];

$page_title = $p['title'];
$page_desc = $p['excerpt'];
$page_og = $p['cover'];
require __DIR__ . '/inc/header.php';
?>

<article>
  <header class="mx-auto max-w-7xl px-5 pt-32 pb-10 md:px-10 md:pt-44 md:pb-14">
    <nav class="mono-label mb-8 flex items-center gap-2 text-soft" aria-label="مسیر صفحه">
      <a href="/work" class="transition-colors hover:text-accent">کارها</a>
      <span aria-hidden="true">/</span>
      <span class="text-ink"><?= e($p['category_name'] ?? 'بدون دسته') ?></span>
    </nav>
    <div class="grid gap-8 md:grid-cols-12">
      <h1 class="text-4xl leading-tight font-black tracking-tight md:col-span-8 md:text-6xl" data-reveal><?= e($p['title']) ?></h1>
      <p class="text-lg leading-8 text-soft md:col-span-4 md:pt-2 md:leading-9" data-reveal><?= e($p['excerpt']) ?></p>
    </div>
  </header>

  <figure class="mx-auto max-w-7xl px-5 md:px-10" data-reveal>
    <div class="zoom-img aspect-4/3 overflow-hidden bg-paper-deep md:aspect-16/9">
      <img src="<?= e($p['cover']) ?>" alt="<?= e($p['title']) ?>" class="size-full object-cover">
    </div>
    <figcaption class="mono-label mt-3 text-soft"><?= e($p['title_en']) ?> — <?= e($p['year_en']) ?></figcaption>
  </figure>

  <section class="mx-auto max-w-7xl px-5 py-14 md:px-10 md:py-20">
    <div class="grid gap-10 border-y border-line py-10 md:grid-cols-4">
      <div data-reveal><p class="mono-label mb-2 text-soft">مشتری</p><p class="font-bold"><?= e($p['client']) ?></p></div>
      <div data-reveal style="--reveal-delay:60ms"><p class="mono-label mb-2 text-soft">سال</p><p class="font-bold"><?= e($p['year_fa']) ?> <span class="num-lat text-soft">/ <?= e($p['year_en']) ?></span></p></div>
      <div data-reveal style="--reveal-delay:120ms"><p class="mono-label mb-2 text-soft">نقش</p><p class="font-bold"><?= e($p['role_fa']) ?></p></div>
      <div data-reveal style="--reveal-delay:180ms">
        <p class="mono-label mb-2 text-soft">خدمات</p>
        <ul class="space-y-1 text-sm font-medium"><?php foreach ($services as $s): ?><li><?= e($s['label']) ?></li><?php endforeach; ?></ul>
      </div>
    </div>

    <div class="mt-14 grid gap-10 md:grid-cols-12">
      <div class="md:col-span-7 md:col-start-4 space-y-6">
        <?php $first = true; foreach (preg_split('/\n{2,}/', trim((string) $p['description'])) as $para): $para = trim((string) $para); if ($para === '') continue; ?>
          <p class="<?= $first ? 'text-xl font-medium leading-9 md:text-2xl md:leading-[2.6rem]' : 'leading-9 text-soft md:leading-10' ?>" data-reveal><?= e($para) ?></p>
          <?php $first = false; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if ($images !== []): ?>
  <section class="mx-auto max-w-7xl px-5 pb-14 md:px-10 md:pb-20">
    <div class="grid gap-6 md:grid-cols-2">
      <?php foreach ($images as $img): ?>
        <figure data-reveal>
          <div class="zoom-img aspect-4/3 overflow-hidden bg-paper-deep"><img src="<?= e($img['path']) ?>" alt="<?= e($img['caption']) ?>" class="size-full object-cover" loading="lazy"></div>
          <?php if ($img['caption'] !== ''): ?><figcaption class="mono-label mt-2 text-soft"><?= e($img['caption']) ?></figcaption><?php endif; ?>
        </figure>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($palette !== []): ?>
  <section class="border-t border-line bg-paper-deep">
    <div class="mx-auto max-w-7xl px-5 py-14 md:px-10 md:py-20">
      <p class="mono-label mb-8 text-soft" data-reveal>پالت رنگ پروژه</p>
      <div class="grid gap-px border border-line bg-line sm:grid-cols-2 lg:grid-cols-4">
        <?php foreach ($palette as $i => $c): ?>
          <div class="bg-paper-deep" data-reveal style="--reveal-delay:<?= $i * 60 ?>ms">
            <div class="h-28 w-full md:h-36" style="background:<?= e($c['hex']) ?>"></div>
            <div class="flex items-center justify-between p-4">
              <span class="text-sm font-bold"><?= e($c['name']) ?></span>
              <span class="mono-label text-soft" dir="ltr"><?= e($c['hex']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="mx-auto max-w-7xl px-5 py-14 md:px-10 md:py-20">
    <div class="grid gap-10 md:grid-cols-12">
      <div class="border border-line p-8 md:col-span-7 md:p-12" data-reveal>
        <p class="mono-label mb-6 text-soft"><?= e($p['specimen_label']) ?></p>
        <p class="text-5xl font-black tracking-tight md:text-7xl"><?= e($p['specimen_sample']) ?></p>
        <p class="mono-label mt-6 text-soft">Vazirmatn — Black / Regular</p>
      </div>
      <?php if ($metrics !== []): ?>
      <div class="grid grid-cols-3 gap-px border border-line bg-line md:col-span-5" data-reveal>
        <?php foreach ($metrics as $m): ?>
          <div class="flex flex-col justify-center bg-paper p-5 text-center">
            <p class="text-3xl font-black md:text-4xl"><?= e($m['value']) ?></p>
            <p class="mt-2 text-xs leading-5 text-soft"><?= e($m['label']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <a href="/work/<?= e($next['slug']) ?>" class="group block border-t border-line bg-ink text-paper">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-6 px-5 py-16 md:px-10 md:py-24">
      <div>
        <p class="mono-label text-paper/50">پروژهٔ بعدی</p>
        <p class="mt-3 text-3xl font-black tracking-tight transition-colors group-hover:text-accent md:text-5xl"><?= e($next['title']) ?></p>
      </div>
      <span class="text-4xl transition-transform group-hover:-translate-x-3 md:text-6xl" aria-hidden="true">←</span>
    </div>
  </a>
</article>

<?php require __DIR__ . '/inc/footer.php'; ?>
