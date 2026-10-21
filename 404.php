<?php
declare(strict_types=1);
http_response_code(404);
$page_title = 'صفحه پیدا نشد — ۴۰۴';
$page_desc = 'این صفحه در گرید ما نیست.';
require __DIR__ . '/inc/header.php';
?>
<section class="mx-auto flex min-h-svh max-w-7xl flex-col items-center justify-center px-5 pt-24 text-center">
  <p class="num-lat text-[22vw] leading-none font-black text-line md:text-[16rem]" aria-hidden="true">404</p>
  <h1 class="mt-4 text-3xl font-black tracking-tight md:text-5xl">این صفحه از گرید بیرون زد.</h1>
  <p class="mt-4 max-w-md leading-8 text-soft">نشانی‌ای که دنبال می‌کردید وجود ندارد یا جابه‌جا شده. بهترین کار: برگشتن به خانه و دیدن کارها.</p>
  <div class="mt-8 flex gap-4">
    <a href="/" class="bg-ink px-8 py-4 text-sm font-black text-paper transition-colors hover:bg-accent">بازگشت به خانه</a>
    <a href="/work" class="border border-ink px-8 py-4 text-sm font-bold transition-colors hover:bg-ink hover:text-paper">دیدن کارها</a>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php'; ?>
