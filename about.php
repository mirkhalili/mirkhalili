<?php
declare(strict_types=1);
$page_title = 'درباره';
$page_desc = 'داستان، مسیر حرفه‌ای، ابزارها و جوایز.';
$page_og = 'assets/uploads/studio.jpg';
require __DIR__ . '/inc/header.php';

$stats = stats_all();
$timeline = timeline_all();
$awards = awards_all();
$skills = skills_all();
?>

<section class="mx-auto max-w-7xl px-5 pt-32 pb-16 md:px-10 md:pt-44 md:pb-24">
  <p class="mono-label mb-3 flex items-center gap-2 text-soft" data-reveal><span class="inline-block size-2 rotate-45 bg-accent"></span>درباره</p>
  <h1 class="max-w-4xl text-4xl leading-tight font-black tracking-tight md:text-6xl md:leading-[1.15]" data-reveal><?= e(setting('about_title')) ?></h1>

  <div class="mt-14 grid gap-12 md:grid-cols-12">
    <div class="md:col-span-5" data-reveal>
      <div class="zoom-img aspect-4/5 overflow-hidden bg-paper-deep">
        <img src="/assets/uploads/studio.jpg" alt="میز کار استودیو" class="size-full object-cover" loading="lazy">
      </div>
      <p class="mono-label mt-3 text-soft">استودیو — <?= e(setting('city')) ?></p>
    </div>
    <div class="space-y-6 leading-9 text-soft md:col-span-6 md:col-start-7 md:text-lg md:leading-10">
      <p class="text-xl font-medium text-ink md:text-2xl md:leading-[2.6rem]" data-reveal><?= e(setting('about_intro')) ?></p>
      <p data-reveal><?= e(setting('about_p1')) ?></p>
      <p data-reveal><?= e(setting('about_p2')) ?></p>
      <p data-reveal><?= e(setting('about_p3')) ?></p>
    </div>
  </div>
</section>

<section class="border-y border-line bg-paper-deep">
  <div class="mx-auto grid max-w-7xl grid-cols-2 gap-px bg-line md:grid-cols-4">
    <?php foreach ($stats as $i => $s): ?>
      <div class="bg-paper-deep p-8 text-center md:p-12" data-reveal style="--reveal-delay:<?= $i * 60 ?>ms">
        <p class="text-4xl font-black md:text-6xl"><?= e($s['value']) ?></p>
        <p class="mono-label mt-3 text-soft"><?= e($s['label']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-28">
  <div class="mb-10 md:mb-14" data-reveal>
    <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>مسیر حرفه‌ای</p>
    <h2 class="text-3xl font-black tracking-tight md:text-5xl">از آژانس تا استودیوی مستقل</h2>
  </div>
  <ol class="divide-y divide-line border-y border-line">
    <?php foreach ($timeline as $i => $t): ?>
      <li class="grid gap-3 py-7 md:grid-cols-12 md:gap-8" data-reveal style="--reveal-delay:<?= $i * 60 ?>ms">
        <p class="mono-label text-soft md:col-span-3 md:pt-1"><?= e($t['period']) ?></p>
        <div class="md:col-span-9">
          <h3 class="text-xl font-extrabold tracking-tight md:text-2xl"><?= e($t['title']) ?></h3>
          <p class="mt-2 text-soft"><?= e($t['description']) ?></p>
        </div>
      </li>
    <?php endforeach; ?>
  </ol>
</section>

<section class="border-t border-line bg-paper-deep">
  <div class="mx-auto grid max-w-7xl gap-14 px-5 py-20 md:grid-cols-2 md:gap-20 md:px-10 md:py-28">
    <div>
      <div class="mb-10" data-reveal>
        <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>ابزارها</p>
        <h2 class="text-3xl font-black tracking-tight md:text-4xl">جعبه‌ابزار روزانه</h2>
      </div>
      <ul class="flex flex-wrap gap-2" data-reveal>
        <?php foreach ($skills as $t): ?>
          <li class="border border-line bg-paper px-4 py-2 font-mono text-sm" dir="ltr"><?= e($t['name']) ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="mt-8 leading-8 text-soft" data-reveal>اما ابزار واقعی من این‌هاست: یک خط‌کش فلزی، کاغذ پوستی، مرکب چین و کلی کلیپ برداشتی از دیوار تهران.</p>
    </div>
    <div>
      <div class="mb-10" data-reveal>
        <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>جوایز</p>
        <h2 class="text-3xl font-black tracking-tight md:text-4xl">تأییدهای بیرونی</h2>
      </div>
      <ul class="divide-y divide-line border-y border-line">
        <?php foreach ($awards as $i => $a): ?>
          <li class="grid gap-2 py-5 md:grid-cols-[4rem_1fr]" data-reveal style="--reveal-delay:<?= $i * 60 ?>ms">
            <span class="mono-label pt-1 text-soft"><?= e($a['year_fa']) ?></span>
            <div>
              <p class="font-extrabold"><?= e($a['title']) ?></p>
              <p class="mt-1 text-sm text-soft"><?= e($a['project']) ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
