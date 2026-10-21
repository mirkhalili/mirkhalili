# نسخهٔ CMS (PHP + MySQL) — راهنمای نصب روی cPanel

> **نگارش 005.2.2.0 به بعد:** ریشهٔ ریپو دقیقاً محتوای `public_html` است.
> این سند خلاصهٔ نصب است؛ راهنمای کامل با جزئیات فنی در [`06-install-deploy.md`](06-install-deploy.md) آمده است.

این ریپو **یک سایت کامل و مستقل** است: همان طراحی سوئیسی مینیمال، اما با
**پنل ادمین و دیتابیس MySQL** تا همهٔ بخش‌ها (پروژه‌ها، دسته‌بندی‌ها، خدمات، رزومه، جوایز،
تنظیمات، پیام‌های تماس) را از مرورگر اضافه/ویرایش/حذف کنید.

> پیش‌نیاز سرور: **PHP 8.1 یا بالاتر** + MySQL/MariaDB (هر دو در هاست اشتراکی cPanel هستند).
> Node.js لازم نیست. CSS از قبل کامپایل‌شده داخل `assets/css/style.css` است.

---

## ۱) ساخت دیتابیس در cPanel

1. **MySQL® Databases** → یک دیتابیس بسازید (مثلاً `user_portfolio`)
2. یک کاربر بسازید (مثلاً `user_dbuser`) با رمز قوی
3. در **Add User To Database** کاربر را به دیتابیس وصل کنید و تیک **ALL PRIVILEGES** بزنید

## ۲) آپلود فایل‌ها (ریشه = public_html)

تمام فایل‌های **ریشهٔ ریپو** را داخل `public_html` آپلود کنید
(File Manager → Zip و Extract، یا FTP با FileZilla).
ساختار نهایی باید این‌گونه باشد:

```
public_html/
├── index.php  work.php  project.php  about.php  services.php  contact.php  404.php
├── sitemap.php  robots.php  schema.sql  .htaccess  favicon.svg
├── inc/  (شامل config.sample.php → کپی به config.php)
├── admin/                 ← پنل ادمین
└── assets/
    ├── css/style.css      ← CSS کامپایل‌شدهٔ Tailwind
    ├── js/                ← main.js و admin.js
    ├── fonts/             ← وزیرمتن + Space Grotesk/Mono (خودمیزبان)
    └── uploads/           ← تصویرهای نمونه‌کار + آپلودهای بعدی پنل
```

## ۳) ایمپورت دیتابیس و پیکربندی

1. **phpMyAdmin** → انتخاب دیتابیس → **Import** → فایل `schema.sql` → Go (۱۷ جدول + دادهٔ اولیه)
2. **File Manager** → `inc/config.sample.php` را کپی کنید به `inc/config.php` و مقادیر `host/name/user/pass/site_url` را پر کنید
3. **ساخت ادمین** — هش رمز را تولید کنید:
   ```bash
   php -r "echo password_hash('رمز_قوی', PASSWORD_DEFAULT);"
   ```
   سپس در phpMyAdmin تب **SQL**:
   ```sql
   INSERT INTO users (username, password_hash, display_name)
   VALUES ('admin', '$2y$10$...هش...', 'مدیر سایت');
   ```
4. ورود به پنل: `https://yourdomain.com/admin/login.php`

راهنمای کامل با عیب‌یابی: [`06-install-deploy.md`](06-install-deploy.md)

## ۴) نقشهٔ پنل ادمین

| بخش | چه چیزی را مدیریت می‌کند |
|---|---|
| داشبورد | شمارنده‌ها، آخرین پیام‌ها و ویرایش‌ها |
| پروژه‌ها | افزودن/ویرایش/حذف نمونه‌کار + تصویر شاخص + گالری + پالت رنگ + معیارها + خدمات پروژه + انتشار/پیش‌نویس |
| دسته‌بندی‌ها | پوستر، کتاب، کارت ویزیت، بنر، لوگو… (با slug برای فیلتر آرشیو) |
| خدمات | خدمات استودیو + زیرآیتم‌ها + مراحل فرآیند |
| رزومه و جوایز | مسیر حرفه‌ای، جوایز، آمار، مهارت‌ها |
| تنظیمات | نام/ایمیل/تلفن، متن هیرو، درباره، نوار متحرک، شبکه‌های اجتماعی + تغییر رمز |
| پیام‌ها | پیام‌های فرم تماس سایت (خوانده/نخوانده، حذف، پاسخ با ایمیل) |

## ۵) نکات امنیتی که از قبل اعمال شده

- رمز ادمین با `password_hash` (bcrypt) — هرگز متن خام در دیتابیس نیست
- همهٔ کوئری‌ها PDO prepared statement (ضد SQL injection)
- همهٔ خروجی‌ها `htmlspecialchars` (ضد XSS) + توکن CSRF روی همهٔ فرم‌ها
- آپلود فقط تصویر (بررسی واقعی با `getimagesize`)، نام تصادفی، و `.htaccess` داخل `uploads/`
  اجرای هر فایل اسکریپتی را مسدود می‌کند
- `inc/` و فایل‌های `.sql` از دسترسی عمومی مسدودند
- نشست ادمین با `session_regenerate_id` پس از ورود

## ۶) به‌روزرسانی CSS (اختیاری)

اگر کلاس‌های Tailwind جدیدی در قالب‌ها استفاده کردید، روی سیستم خودتان:

```bash
npm install
npm run build:cms-css
```

و فایل `assets/css/style.css` را دوباره آپلود کنید.

## ۷) اسکیمای دیتابیس (مرجع سریع)

`users`, `settings`, `socials`, `categories`, `projects`,
`project_services`, `project_palette`, `project_metrics`, `project_images`,
`services`, `service_items`, `process_steps`, `stats`, `timeline`, `awards`,
`skills`, `messages` — تعریف کامل با کلیدهای خارجی در **`schema.sql`**.
