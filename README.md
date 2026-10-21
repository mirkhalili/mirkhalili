# میر خلیلی — پورتفولیوی طراح گرافیک (CMS)

وب‌سایت شخصی و پورتفولیوی **میر خلیلی**، طراح گرافیک و هویت بصری — سبک **مینیمال تایپوگرافیک سوئیسی**، کاملاً **فارسی و راست‌به‌چپ (RTL)**.

> **نگارش جاری: 005.2.2.0** (طرح QAVNS) — تاریخچه: [`Docs/CHANGELOG.md`](Docs/CHANGELOG.md) · قواعد نگارش‌گذاری: [`Docs/VERSIONING.md`](Docs/VERSIONING.md) · مستندات کامل: [`Docs/README.md`](Docs/README.md)
>
> **این ریپو اکنون تک‌نسخه‌ای CMS است.** ریشهٔ ریپو دقیقاً محتوای `public_html` هاست است — بدون پوشهٔ واسط `cpanel-site/` و بدون نسخهٔ استاتیک Astro.

## پشتهٔ فنی

- **PHP 8.1+** با PDO + **MySQL/MariaDB** (`utf8mb4`) — بدون نیاز به Node روی سرور
- **Apache** با `.htaccess` (مسیرهای تمیز، اجبار HTTPS، کش و هدرهای امنیتی)
- **Tailwind CSS v4** کامپایل‌شده در `assets/css/style.css` (سورس: `assets/css/src/input.css`)
- فونت فارسی **وزیرمتن متغیر** خودمیزبان + **Space Grotesk / Space Mono** برای اعداد و برچسب‌های لاتین
- جاوااسکریپت خالص — بدون فریمورک (IntersectionObserver، ساعت تهران، پیش‌نمایش شناور)

## ساختار (ریشه = public_html)

```
.
├── index.php / work.php / project.php / about.php / services.php / contact.php / 404.php
├── sitemap.php / robots.php / schema.sql / .htaccess / favicon.svg
├── inc/               ← هسته: db, helpers, data, header, footer (با .htaccess داخلی مسدود)
│   └── config.sample.php  ← نمونهٔ پیکربندی (کپی به config.php)
├── admin/             ← پنل ادمین (login, index, projects, categories, services, resume, settings, messages)
├── assets/
│   ├── css/style.css  ← CSS کامپایل‌شده (بدون بیلد روی سرور)
│   ├── css/src/input.css ← سورس Tailwind (برای بازسازی اختیاری)
│   ├── js/            ← main.js و admin.js
│   ├── fonts/         ← وزیرمتن + Space Grotesk/Mono خودمیزبان
│   └── uploads/       ← تصویرهای نمونه‌کار + آپلودهای پنل (اجرای اسکریپت مسدود)
├── Docs/              ← ۱۰ سند فارسی + QAVNS + CHANGELOG
├── VERSION            ← شناسهٔ نگارش جاری
└── CHANGELOG.md       ← رویدادنگاری نگارش‌ها
```

## نصب سریع روی cPanel (بدون install.php)

> **پیش‌نیاز سرور:** PHP 8.1+ با افزونه‌های `pdo_mysql` + MySQL/MariaDB. Node.js لازم نیست.

1. **دیتابیس:** cPanel → `MySQL® Databases` → ساخت دیتابیس + کاربر + اتصال با `ALL PRIVILEGES`
2. **آپلود:** تمام فایل‌های ریشهٔ ریپو را داخل `public_html` آپلود کنید (File Manager → Zip & Extract یا FTP/FileZilla)
3. **ایمپورت اسکیما:** cPanel → `phpMyAdmin` → انتخاب دیتابیس → تب `Import` → فایل `schema.sql` → Go
   - ۱۷ جدول با کلید خارجی + دادهٔ اولیه (۸ پروژهٔ نمونه، دسته‌ها، خدمات، رزومه…) ساخته می‌شود
4. **پیکربندی:** فایل `inc/config.sample.php` را کپی کنید به `inc/config.php` و مقادیر `host/name/user/pass/site_url` را پر کنید
   ```bash
   cp inc/config.sample.php inc/config.php
   ```
5. **ساخت ادمین:** در phpMyAdmin تب `SQL` کوئری زیر را اجرا کنید (هش را از قبل تولید کنید):
   ```sql
   -- تولید هش در لوکال:
   -- php -r "echo password_hash('رمز_قوی_اینجا', PASSWORD_DEFAULT);"
   INSERT INTO users (username, password_hash, display_name)
   VALUES ('admin', '$2y$10$...هش_تولیدشده...', 'مدیر سایت');
   ```
6. **ورود:** `https://yourdomain.com/admin/login.php` → با نام کاربری/رمز ساخته‌شده وارد شوید

راهنمای کامل با اسکرین‌شات ذهنی و نکات `.htaccess` در [`Docs/06-install-deploy.md`](Docs/06-install-deploy.md) آمده است.

## پنل ادمین

| بخش | مدیریت |
|---|---|
| داشبورد | شمارنده‌ها، آخرین پیام‌ها و ویرایش‌ها |
| پروژه‌ها | CRUD کامل + تصویر شاخص/گالری/پالت/معیارها/خدمات/انتشار |
| دسته‌بندی‌ها | ۷ دستهٔ پیش‌فرض + CRUD با slug یکتا |
| خدمات و فرآیند | خدمات استودیو + زیرآیتم‌ها + ۴ مرحلهٔ فرآیند |
| رزومه و جوایز | مسیر حرفه‌ای، جوایز، آمار، مهارت‌ها |
| تنظیمات | همهٔ متن‌های سایت + شبکه‌ها + تغییر رمز |
| پیام‌ها | صندوق پیام‌های فرم تماس (خوانده/نخوانده) |

جزئیات هر بخش: [`Docs/04-admin-features.md`](Docs/04-admin-features.md)

## به‌روزرسانی CSS (اختیاری)

فقط اگر کلاس Tailwind جدیدی اضافه کردید — روی سیستم خودتان:

```bash
npm install    # فقط برای ابزار بیلد محلی (Tailwind CLI)
npm run build:cms-css
```

سپس `assets/css/style.css` را دوباره آپلود کنید. روی سرور Node لازم نیست.

## امنیت

- رمز ادمین با `password_hash` (bcrypt) — هرگز متن خام ذخیره نمی‌شود
- همهٔ کوئری‌ها PDO prepared (ضد SQL injection) + `htmlspecialchars` (ضد XSS) + CSRF
- آپلود فقط تصویر واقعی (`getimagesize`)، نام تصادفی، و `.htaccess` ضد اجرا در `uploads/`
- `inc/` و فایل‌های `.sql/.md/.sample.php` از بیرون مسدودند (`Require all denied`)
- فشرده‌سازی و هدرهای `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` + اجبار HTTPS

توصیه‌های پس از نصب: رمز ۱۲+ کاراکتری، AutoSSL فعال، پشتیبان هفتگی. جزئیات: [`Docs/07-security.md`](Docs/07-security.md)

## سئو و کارا

- `title/description` یکتا، canonical بر پایهٔ `site_url`، Open Graph + sitemap/robots پویا
- مسیرهای تمیز: `/work`, `/work/{slug}`, `/about|services|contact`, `/sitemap.xml`
- فونت متغیر تک‌فایل با `preload` + `font-display:swap`، CSS minify، JS خالص، تصاویر `lazy`
- جزئیات: [`Docs/08-seo-performance.md`](Docs/08-seo-performance.md)

## نگارش‌گذاری (QAVNS)

شناسه: `e0RSN.G.FF.CCC` — سه رقم با پیش‌صفر برای RSN. تاریخچه و آیین نشر در [`Docs/VERSIONING.md`](Docs/VERSIONING.md).

- نگارش جاری `005.2.2.0` — فقط-قابلیت (حذف `install.php` و تثبیت CMS به‌عنوان تنها نسخه)
- پس از هر نشر، محتوای آن نگارش هرگز تغییر نمی‌کند؛ هر تغییری در نگارش بعدی.

## مجوز و تماس

طراحی و کدنویسی: استودیو میر خلیلی — `studio@mirkhalili.com` — تهران، ایران
