<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/data.php';

$page_title = setting('services_title', 'خدمات');
$page_desc = setting('services_intro', 'هویت بصری، لوگو، پوستر و بنر، کتاب و نشر، کارت ویزیت، بسته‌بندی، تایپوگرافی.');
require __DIR__ . '/inc/header.php';

$services = services_all();
$process = process_all();
?>

<section class="mx-auto max-w-7xl px-5 pt-32 pb-16 md:px-10 md:pt-44 md:pb-20">
  <p class="mono-label mb-3 flex items-center gap-2 text-soft" data-reveal><span class="inline-block size-2 rotate-45 bg-accent"></span>خدمات</p>
  <h1 class="max-w-4xl text-4xl leading-tight font-black tracking-tight md:text-6xl md:leading-[1.15]" data-reveal><?= e(setting('services_title', 'راه‌هایی برای اینکه با هم کار کنیم.')) ?></h1>
  <div class="mt-6 max-w-2xl text-lg leading-9 text-soft" data-reveal><?= rich_text(setting('services_intro')) ?></div>
</section>

<section class="mx-auto max-w-7xl px-5 pb-20 md:px-10 md:pb-28">
  <ul class="grid gap-px border border-line bg-line md:grid-cols-2">
    <?php foreach ($services as $i => $s): ?>
      <li class="group bg-paper p-8 transition-colors hover:bg-ink hover:text-paper md:p-12 <?= $i === 0 ? 'md:col-span-2' : '' ?>" data-reveal style="--reveal-delay:<?= $i * 60 ?>ms">
        <div class="flex items-baseline justify-between gap-4">
          <span class="mono-label text-soft group-hover:text-paper/60"><?= fa_index($i + 1) ?></span>
          <span class="mono-label text-soft group-hover:text-paper/60" dir="ltr"><?= e($s['title_en']) ?></span>
        </div>
        <h2 class="mt-6 text-2xl font-extrabold tracking-tight md:text-4xl"><?= e($s['title']) ?></h2>
        <p class="mt-4 leading-8 text-soft group-hover:text-paper/70"><?= e($s['description']) ?></p>
        <ul class="mt-6 grid gap-2 border-t border-line pt-6 text-sm font-medium group-hover:border-paper/20 sm:grid-cols-2">
          <?php foreach ($s['items'] as $it): ?>
            <li class="flex items-center gap-2"><span class="inline-block size-1.5 rotate-45 bg-accent"></span><?= e($it['label']) ?></li>
          <?php endforeach; ?>
        </ul>
      </li>
    <?php endforeach; ?>
  </ul>
</section>

<section class="border-t border-line bg-paper-deep">
  <div class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-28">
    <div class="mb-10 flex flex-wrap items-end justify-between gap-6 md:mb-14" data-reveal>
      <div>
        <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>فرآیند</p>
        <h2 class="text-3xl font-black tracking-tight md:text-5xl"><?= e(setting('services_process_title', 'چهار قدم، بدون سورپرایز')) ?></h2>
      </div>
      <div class="max-w-sm text-sm leading-7 text-soft md:text-base md:leading-8"><?= rich_text(setting('services_process_intro', 'در هر مرحله دقیقاً می‌دانید کجاییم و قدم بعدی چیست.')) ?></div>
    </div>
    <ol class="grid gap-px border border-line bg-line md:grid-cols-4">
      <?php foreach ($process as $i => $st): ?>
        <li class="bg-paper-deep p-8" data-reveal style="--reveal-delay:<?= $i * 70 ?>ms">
          <span class="num-lat text-5xl font-black text-line" aria-hidden="true"><?= $i + 1 ?></span>
          <h3 class="mt-4 text-xl font-extrabold"><?= e($st['title']) ?></h3>
          <p class="mt-3 text-sm leading-7 text-soft"><?= e($st['description']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>

    <div class="mt-16 flex flex-wrap items-center justify-between gap-6 border-t border-line pt-10" data-reveal>
      <p class="max-w-xl leading-8 text-soft"><?= e(setting('process_note')) ?></p>
      <a href="/contact" class="border border-ink px-8 py-4 text-sm font-black transition-colors hover:bg-ink hover:text-paper">درخواست برآورد زمان و هزینه</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
