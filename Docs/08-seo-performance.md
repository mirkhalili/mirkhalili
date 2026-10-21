# ۰۸ — سئو، دسترس‌پذیری و کارا

## سئو

- `title` و `description` یکتا برای هر صفحه؛ canonical بر پایهٔ `site_url` تنظیم‌شده در `inc/config.php`
- Open Graph کامل (`og:locale=fa_IR`، تصویر هر پروژه به‌عنوان `og:image` در کیس‌استادی‌ها) + Twitter Card
- **JSON-LD** نوع `Person` (نام، شغل، ایمیل، sameAs) در قالب‌های PHP
- `sitemap.xml` پویا (صفحه‌های ثابت + پروژه‌های منتشرشده) و `robots.txt` پویا با مسدودسازی `/admin`
- مسیرهای تمیز و معنادار: `/work/{slug}` با slug لاتین خوانا
- محتوای واقعی فارسی در تیترها/توضیح‌ها (نه placeholder)

## دسترس‌پذیری

- `lang="fa" dir="rtl"`؛ پرش به محتوا (`skip link`)
- `aria-current` برای ناوبری، `aria-expanded/controls` برای منوی موبایل، `role=status/alert` برای پیام‌های فرم
- `:focus-visible` با حلقهٔ آجری؛ کنتراست متن `soft` روی `paper` در محدودهٔ AA
- احترام کامل به `prefers-reduced-motion`
- `alt` معنادار برای تصویرهای نمونه‌کار و `alt=""` برای تصویرهای تزئینی

## کارا

- فونت فارسی متغیر تک‌فایل (~۱۱۲KB) با `preload` + `font-display: swap`
- CSS تک‌فایل کامپایل‌شده و minify (~۳۷KB)؛ JS خالص بدون فریمورک (دو فایل کوچک)
- تصویرها `loading="lazy"` به‌جز تصویر شاخص؛ ابعاد ثابت با `aspect-ratio` برای پایداری CLS
- کش بلندمدت برای دارایی‌های هش‌دار/تصویرها و بدون‌کش برای HTML
- کوئری‌های سبک بدون ORM؛ امتیاز کارا نزدیک به کامل روی هاست اشتراکی
