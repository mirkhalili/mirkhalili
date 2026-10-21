<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/data.php';

// Start the session before header.php emits HTML, so the CSRF cookie is sent with headers.
csrf_token();

$ok = false;
$error = '';
$types = ['هویت بصری', 'لوگو', 'پوستر و بنر', 'کتاب و نشر', 'کارت ویزیت', 'بسته‌بندی', 'تایپوگرافی', 'سایر'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $type = isset($_POST['type']) && is_string($_POST['type']) ? trim($_POST['type']) : '';
    $body = isset($_POST['message']) && is_string($_POST['message']) ? trim($_POST['message']) : '';
    $type = in_array($type, $types, true) ? $type : '';
    $honeypot = isset($_POST['website']) && is_string($_POST['website']) ? trim($_POST['website']) : '';
    if ($honeypot !== '') {
        $ok = true; // Ignore automated submissions without revealing the honeypot.
    } elseif ($name === '' || !preg_match('/^.{1,120}$/us', $name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 180 || !preg_match('/^.{10,6000}$/us', $body)) {
        $error = 'نام حداکثر ۱۲۰ نویسه، ایمیل معتبر و توضیح پروژه بین ۱۰ تا ۶۰۰۰ نویسه لازم است.';
    } else {
        q('INSERT INTO messages (name, email, project_type, body) VALUES (?, ?, ?, ?)', [$name, $email, $type, $body]);
        $ok = true;
    }
}

$page_title = 'تماس';
$page_desc = 'برای شروع پروژه، همکاری یا یک گفت‌وگوی دوستانه دربارهٔ تایپوگرافی فارسی، بنویسید.';
require __DIR__ . '/inc/header.php';
$socials = socials_all();
?>

<section class="mx-auto max-w-7xl px-5 pt-32 pb-20 md:px-10 md:pt-44 md:pb-28">
  <p class="mono-label mb-3 flex items-center gap-2 text-soft" data-reveal><span class="inline-block size-2 rotate-45 bg-accent"></span>تماس</p>
  <h1 class="text-5xl font-black tracking-tight md:text-8xl" data-reveal><?= e(setting('contact_title', 'بیایید حرف بزنیم.')) ?></h1>

  <?php if ($ok): ?>
    <p class="mt-8 border border-accent bg-accent/10 px-6 py-4 font-bold text-accent" role="status">
      <?= e(setting('contact_success', 'پیام شما ثبت شد؛ معمولاً کمتر از ۲۴ ساعت جواب می‌دهم. ✳')) ?>
    </p>
  <?php elseif ($error !== ''): ?>
    <p class="mt-8 border border-accent bg-accent/10 px-6 py-4 font-bold text-accent" role="alert"><?= e($error) ?></p>
  <?php endif; ?>

  <div class="mt-14 grid gap-14 md:grid-cols-12 md:gap-16">
    <form method="post" action="/contact" class="md:col-span-7">
      <div class="absolute -left-[10000px]" aria-hidden="true"><label>وب‌سایت <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <?= csrf_field() ?>
      <div class="grid gap-8 sm:grid-cols-2">
        <label class="block">
          <span class="mono-label mb-2 block text-soft">نام و نام خانوادگی *</span>
          <input name="name" required maxlength="120" autocomplete="name" class="w-full border-b border-line bg-transparent py-3 text-lg font-medium outline-none transition-colors focus:border-accent" placeholder="مثلاً سارا احمدی" value="<?= e($_POST['name'] ?? '') ?>">
        </label>
        <label class="block">
          <span class="mono-label mb-2 block text-soft">ایمیل *</span>
          <input name="email" type="email" required maxlength="180" autocomplete="email" dir="ltr" style="text-align:end" class="w-full border-b border-line bg-transparent py-3 text-lg font-medium outline-none transition-colors focus:border-accent" placeholder="you@example.com" value="<?= e($_POST['email'] ?? '') ?>">
        </label>
      </div>

      <fieldset class="mt-10">
        <legend class="mono-label mb-3 text-soft">نوع پروژه</legend>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($types as $i => $t): ?>
            <label class="cursor-pointer">
              <input type="radio" name="type" value="<?= e($t) ?>" class="peer sr-only"<?= (($_POST['type'] ?? '') !== '' ? $_POST['type'] === $t : $i === 0) ? ' checked' : '' ?>>
              <span class="inline-block border border-line px-4 py-2 text-sm font-bold text-soft transition-colors peer-checked:border-ink peer-checked:bg-ink peer-checked:text-paper"><?= e($t) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <label class="mt-10 block">
        <span class="mono-label mb-2 block text-soft">دربارهٔ پروژه *</span>
        <textarea name="message" required minlength="10" maxlength="6000" rows="5" class="w-full resize-y border-b border-line bg-transparent py-3 leading-8 outline-none transition-colors focus:border-accent" placeholder="مسئله، بازهٔ زمانی و هر چیزی که دانستنش کمک می‌کند…"><?= e($_POST['message'] ?? '') ?></textarea>
      </label>

      <div class="mt-10">
        <button type="submit" class="bg-ink px-10 py-4 text-sm font-black text-paper transition-colors hover:bg-accent">ارسال پیام</button>
      </div>
    </form>

    <aside class="space-y-10 md:col-span-4 md:col-start-9">
      <div data-reveal>
        <p class="mono-label mb-3 text-soft">ایمیل مستقیم</p>
        <a href="mailto:<?= e(setting('email')) ?>" class="link-slide block text-xl font-black break-all" dir="ltr" style="text-align:end"><?= e(setting('email')) ?></a>
      </div>
      <div data-reveal>
        <p class="mono-label mb-3 text-soft">تلفن استودیو</p>
        <p class="num-lat text-xl font-bold" dir="ltr" style="text-align:end"><?= e(setting('phone')) ?></p>
      </div>
      <div data-reveal>
        <p class="mono-label mb-3 text-soft"><?= e(setting('contact_response', 'ساعت پاسخ‌گویی')) ?></p>
        <p class="leading-8 text-soft"><?= e(setting('contact_note')) ?></p>
        <p class="num-lat mt-3 text-2xl font-bold" data-clock>--:--:--</p>
      </div>
      <div data-reveal>
        <p class="mono-label mb-3 text-soft">شبکه‌ها</p>
        <ul class="flex flex-wrap gap-x-5 gap-y-2">
          <?php foreach ($socials as $s): ?>
            <li><a href="<?= e($s['url']) ?>" class="link-slide font-bold" target="_blank" rel="noopener" dir="ltr"><?= e($s['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/inc/footer.php'; ?>
