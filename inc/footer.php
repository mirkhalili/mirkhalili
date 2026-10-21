<?php
/** پابرگ سایت عمومی */
declare(strict_types=1);
$footer_socials = socials_all();
?>
</main>

<footer class="relative z-10 border-t border-line bg-ink text-paper">
  <div class="mx-auto max-w-7xl px-5 py-16 md:px-10 md:py-24">
    <p class="mono-label text-paper/50" data-reveal>پروژه‌ای در ذهن دارید؟</p>
    <a href="mailto:<?= e(setting('email')) ?>" class="mt-4 block text-4xl font-black tracking-tight break-words transition-colors hover:text-accent md:text-7xl" dir="ltr" style="text-align:end" data-reveal>
      <?= e(setting('email')) ?>
    </a>

    <div class="mt-14 grid gap-10 border-t border-paper/15 pt-10 md:grid-cols-4" data-reveal>
      <div>
        <p class="mono-label mb-4 text-paper/50">شبکه‌ها</p>
        <ul class="space-y-2">
          <?php foreach ($footer_socials as $s): ?>
            <li><a href="<?= e($s['url']) ?>" class="link-slide text-sm font-medium" target="_blank" rel="noopener"><?= e($s['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <p class="mono-label mb-4 text-paper/50">ناوبری</p>
        <ul class="space-y-2 text-sm font-medium">
          <li><a class="link-slide" href="/work">آرشیو کارها</a></li>
          <li><a class="link-slide" href="/about">دربارهٔ من</a></li>
          <li><a class="link-slide" href="/services">خدمات</a></li>
          <li><a class="link-slide" href="/contact">تماس</a></li>
        </ul>
      </div>
      <div>
        <p class="mono-label mb-4 text-paper/50">استودیو</p>
        <p class="text-sm leading-7 text-paper/80">
          <?= e(setting('city')) ?><br>
          <span dir="ltr" class="num-lat"><?= e(setting('phone')) ?></span>
        </p>
      </div>
      <div>
        <p class="mono-label mb-4 text-paper/50">ساعت محلی</p>
        <p class="num-lat text-3xl font-bold" data-clock>--:--:--</p>
        <p class="mt-1 text-xs text-paper/60">تهران، ایران</p>
      </div>
    </div>

    <div class="mt-12 flex flex-col gap-4 border-t border-paper/15 pt-6 text-xs text-paper/50 md:flex-row md:items-center md:justify-between">
      <p>© <?= e(function_exists('intlDateFormatter_create') ? (new IntlDateFormatter('fa_IR', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Asia/Tehran', null, 'yyyy'))->format(new DateTime()) : date('Y')) ?> <?= e(setting('site_name')) ?> — همهٔ حقوق محفوظ است.</p>
      <p class="mono-label" dir="ltr">Designed &amp; built with a strict grid</p>
      <a href="#main" class="link-slide w-fit font-bold text-paper">بازگشت به بالا ↑</a>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>
