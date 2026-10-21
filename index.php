<?php
declare(strict_types=1);
$page_title = '';
$page_desc = null; // پیش‌فرض از تنظیمات
require __DIR__ . '/inc/header.php';

$projects = projects_all();
$services = services_all();
$stats = stats_all();
$areas = pipe_list(setting('hero_areas'));
$marquee = pipe_list(setting('marquee'));
?>

<!-- HERO -->
<section class="mx-auto flex min-h-svh max-w-7xl flex-col justify-end px-5 pt-28 pb-10 md:px-10 md:pt-36 md:pb-14">
  <div class="mb-8 grid grid-cols-2 gap-6 border-b border-line pb-6 text-sm md:grid-cols-4" data-reveal>
    <p class="mono-label text-soft">پورتفولیو — نسخهٔ ۱۴۰۴</p>
    <p class="mono-label text-soft hidden md:block"><?= e(setting('city')) ?></p>
    <p class="mono-label flex items-center gap-2 text-soft">
      <span class="pulse-dot inline-block size-1.5 rounded-full bg-accent text-accent"></span>
      <?= e(setting('availability')) ?>
    </p>
    <p class="mono-label text-soft text-end" dir="ltr"><span data-clock>--:--:--</span> TEH</p>
  </div>

  <h1 class="text-[13vw] leading-[1.05] font-black tracking-tight md:text-[7.5vw]">
    <span class="mask-line"><span style="--line-delay:100ms"><?= e(setting('hero_l1')) ?></span></span>
    <span class="mask-line"><span style="--line-delay:240ms"><?= e(setting('hero_l2')) ?> <span class="text-accent">مانده</span></span></span>
    <span class="mask-line"><span style="--line-delay:380ms"><?= e(setting('hero_l3')) ?></span></span>
  </h1>

  <div class="mt-10 grid gap-8 md:grid-cols-12" data-reveal>
    <p class="text-lg leading-9 font-medium md:col-span-5 md:text-xl md:leading-10">
      <?= e(setting('site_name')) ?> — <?= e(setting('role')) ?>. <?= e(setting('hero_intro')) ?>
    </p>
    <div class="md:col-span-4 md:col-start-8">
      <p class="mono-label mb-3 text-soft">حوزه‌ها</p>
      <ul class="space-y-1 text-sm font-medium">
        <?php foreach ($areas as $a): ?><li><?= e($a) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <div class="md:col-span-1 md:col-start-12 md:text-end">
      <a href="#work" class="mono-label inline-block text-soft transition-colors hover:text-accent">↓ اسکرول</a>
    </div>
  </div>
</section>

<!-- MARQUEE -->
<div class="marquee border-y border-line bg-paper-deep py-4 md:py-5" aria-hidden="true">
  <div class="marquee-track">
    <?php for ($k = 0; $k < 2; $k++): ?>
      <div class="flex shrink-0 items-center">
        <?php foreach ($marquee as $t): ?>
          <span class="flex items-center gap-6 px-6 text-xl font-extrabold tracking-tight whitespace-nowrap md:text-3xl">
            <?= e($t) ?><span class="inline-block size-2.5 rotate-45 bg-accent"></span>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endfor; ?>
  </div>
</div>

<!-- کارهای منتخب -->
<section id="work" class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-28">
  <div class="mb-10 flex flex-wrap items-end justify-between gap-6 md:mb-14" data-reveal>
    <div>
      <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>کارهای منتخب</p>
      <h2 class="text-3xl font-black tracking-tight md:text-5xl">پروژه‌ها، مسئله به مسئله</h2>
    </div>
    <p class="max-w-sm text-sm leading-7 text-soft md:text-base md:leading-8">گزیده‌ای از پروژه‌های اخیر؛ از هویت بصری و لوگو تا پوستر، کتاب، بسته‌بندی و تایپوگرافی.</p>
  </div>

  <div class="relative">
    <div id="hover-preview" class="hidden lg:block" aria-hidden="true"><img alt="" src="<?= e($projects[0]['cover'] ?? '') ?>"></div>
    <ul class="divide-y divide-line border-y border-line">
      <?php foreach ($projects as $i => $p): ?>
        <li>
          <a href="/work/<?= e($p['slug']) ?>" class="work-row group grid grid-cols-[auto_1fr_auto] items-baseline gap-4 py-6 md:grid-cols-[4rem_1fr_auto_auto] md:gap-8 md:py-8" data-cover="<?= e($p['cover']) ?>" data-reveal style="--reveal-delay:<?= $i * 70 ?>ms">
            <span class="mono-label pt-2 text-soft"><?= fa_index($i + 1) ?></span>
            <span class="min-w-0">
              <span class="work-title block text-2xl font-extrabold tracking-tight md:text-4xl lg:text-5xl"><?= e($p['title']) ?></span>
              <span class="mt-2 block max-w-xl text-sm leading-6 text-soft md:text-base md:leading-7"><?= e($p['excerpt']) ?></span>
              <span class="zoom-img mt-4 block aspect-4/3 overflow-hidden lg:hidden">
                <img src="<?= e($p['cover']) ?>" alt="<?= e($p['title']) ?>" class="size-full object-cover" loading="lazy">
              </span>
            </span>
            <span class="hidden text-sm font-medium text-soft md:block md:pt-2"><?= e($p['category_name'] ?? '') ?></span>
            <span class="flex items-center gap-4 pt-1 md:pt-2">
              <span class="num-lat text-sm text-soft"><?= e($p['year_fa']) ?></span>
              <span class="row-arrow text-xl md:text-2xl" aria-hidden="true">↖</span>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="mt-10 text-end" data-reveal>
    <a href="/work" class="inline-block border border-ink px-6 py-3 text-sm font-bold transition-colors hover:bg-ink hover:text-paper">دیدن همهٔ کارها ←</a>
  </div>
</section>

<!-- استودیو -->
<section class="border-t border-line bg-paper-deep">
  <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 md:grid-cols-2 md:gap-16 md:px-10 md:py-28">
    <div class="zoom-img aspect-4/3 overflow-hidden" data-reveal>
      <img src="/assets/uploads/studio.jpg" alt="میز کار استودیو" class="size-full object-cover" loading="lazy">
    </div>
    <div class="flex flex-col justify-center">
      <div class="mb-10" data-reveal>
        <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>استودیو</p>
        <h2 class="text-3xl font-black tracking-tight md:text-5xl"><?= e(setting('studio_title')) ?></h2>
      </div>
      <p class="leading-8 text-soft md:leading-9" data-reveal><?= e(setting('studio_text')) ?></p>
      <div class="mt-10 grid grid-cols-2 gap-px border border-line bg-line md:grid-cols-4" data-reveal>
        <?php foreach ($stats as $s): ?>
          <div class="bg-paper-deep p-5 text-center">
            <p class="text-3xl font-black md:text-4xl"><?= e($s['value']) ?></p>
            <p class="mono-label mt-2 text-soft"><?= e($s['label']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-8" data-reveal><a href="/about" class="link-slide text-sm font-bold">بیشتر دربارهٔ من ←</a></div>
    </div>
  </div>
</section>

<!-- خدمات -->
<section class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-28">
  <div class="mb-10 flex flex-wrap items-end justify-between gap-6 md:mb-14" data-reveal>
    <div>
      <p class="mono-label mb-3 flex items-center gap-2 text-soft"><span class="inline-block size-2 rotate-45 bg-accent"></span>خدمات</p>
      <h2 class="text-3xl font-black tracking-tight md:text-5xl">چه کاری از دستم برمی‌آید</h2>
    </div>
  </div>
  <ul class="divide-y divide-line border-y border-line">
    <?php foreach ($services as $i => $s): ?>
      <li data-reveal style="--reveal-delay:<?= $i * 60 ?>ms">
        <a href="/services" class="group grid grid-cols-[auto_1fr] items-baseline gap-5 py-6 md:grid-cols-[4rem_1fr_auto] md:gap-8">
          <span class="mono-label text-soft"><?= fa_index($i + 1) ?></span>
          <span class="text-xl font-extrabold tracking-tight transition-colors group-hover:text-accent md:text-3xl"><?= e($s['title']) ?></span>
          <span class="mono-label col-start-2 text-soft md:col-start-3" dir="ltr"><?= e($s['title_en']) ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</section>

<!-- CTA -->
<section class="border-t border-line bg-ink text-paper">
  <div class="mx-auto max-w-7xl px-5 py-20 text-center md:px-10 md:py-28">
    <p class="mono-label text-paper/50" data-reveal>قدم بعدی</p>
    <h2 class="mt-4 text-4xl font-black tracking-tight md:text-6xl" data-reveal>
      پروژه‌ای در ذهن دارید؟<br><span class="text-accent">بیایید جدی حرف بزنیم.</span>
    </h2>
    <div class="mt-10 flex flex-wrap justify-center gap-4" data-reveal>
      <a href="mailto:<?= e(setting('email')) ?>" class="bg-paper px-8 py-4 text-sm font-black text-ink transition-colors hover:bg-accent hover:text-paper"><?= e(setting('email')) ?></a>
      <a href="/contact" class="border border-paper/40 px-8 py-4 text-sm font-bold transition-colors hover:border-paper hover:bg-paper hover:text-ink">فرم تماس</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
