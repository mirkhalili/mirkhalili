# ۰۶ — نصب و دیپلوی (CMS تک‌نسخه‌ای)

> **نگارش جاری 007.2.3.0:** ریشهٔ ریپو دقیقاً محتوای `public_html` است.
> فایل `install.php` حذف شده و نصب **بدون اسکریپت نصب** انجام می‌شود:
> ایمپورت `schema.sql` در phpMyAdmin + کپی `inc/config.sample.php` به `inc/config.php` + کوئری ساخت ادمین.

---

## پیش‌نیاز سرور

- **PHP 8.1 یا بالاتر** با افزونهٔ `pdo_mysql` (در هاست اشتراکی cPanel پیش‌فرض فعال است)
- **MySQL / MariaDB** با پشتیبانی `utf8mb4`
- **Apache** با `mod_rewrite` و `mod_headers` (برای `.htaccess`)
- **Node.js لازم نیست**؛ CSS کامپایل‌شده همراه ریپو است (`assets/css/style.css`)

---

## گام ۱) ساخت دیتابیس در cPanel

1. cPanel → **MySQL® Databases**
2. یک دیتابیس بسازید (مثلاً `mirkhalili_portfolio` — cPanel پیشوند نام کاربری را اضافه می‌کند: `user_portfolio`)
3. یک کاربر بسازید (مثلاً `user_dbuser`) با رمز قوی
4. در بخش **Add User To Database** کاربر را به دیتابیس وصل کنید و تیک **ALL PRIVILEGES** بزنید
5. نام کامل دیتابیس و کاربر را یادداشت کنید (همان‌ها در `inc/config.php` لازم می‌شوند)

---

## گام ۲) آپلود فایل‌ها به public_html

ریشهٔ ریپو = `public_html`. دو روش:

### الف) File Manager (ساده‌ترین)
1. در سیستم خودتان کل ریپو را Zip کنید **یا** از صفحهٔ Release فایل `mirkhalili-007.2.3.0.zip` را دانلود کنید
2. cPanel → **File Manager** → وارد `public_html` شوید
3. اگر فایل‌های پیش‌فرض هاست (مثل `index.php` نمونه) هست، حذف کنید
4. فایل Zip را **Upload** کنید → راست‌کلیک → **Extract** → مسیر `public_html`
5. Zip را حذف کنید
6. بررسی: **Settings → Show Hidden Files** را بزنید و مطمئن شوید `.htaccess` و `inc/` آپلود شده‌اند

### ب) FTP / FileZilla
1. cPanel → **FTP Accounts** → اکانت FTP بسازید (یا اکانت اصلی cPanel)
2. FileZilla → Host: `ftp.yourdomain.com`، Username/Password اکانت FTP
3. سمت سرور وارد `public_html` شوید و **محتویات ریشهٔ ریپو** را Drag & Drop کنید

ساختار نهایی روی سرور:
```
public_html/
├── index.php  work.php  project.php  about.php  services.php  contact.php  404.php
├── sitemap.php  robots.php  schema.sql  .htaccess  favicon.svg
├── inc/  (شامل config.sample.php → باید config.php بسازید)
├── admin/
└── assets/ (css, js, fonts, uploads)
```

---

## گام ۳) ایمپورت schema.sql در phpMyAdmin

1. cPanel → **phpMyAdmin** → از ستون چپ دیتابیس ساخته‌شده در گام ۱ را انتخاب کنید
2. تب **Import** → **Choose File** → فایل `schema.sql` (از `public_html/schema.sql` روی سرور یا از سیستم خودتان)
3. Charset را `utf8mb4` بگذارید (پیش‌فرض) → **Go**
4. باید ۱۷ جدول ساخته شود: `users`, `settings`, `socials`, `categories`, `projects`, `project_services`, `project_palette`, `project_metrics`, `project_images`, `services`, `service_items`, `process_steps`, `stats`, `timeline`, `awards`, `skills`, `messages`
   + دادهٔ اولیه: ۸ پروژهٔ نمونه، ۷ دسته، ۷ خدمت، رزومه، جوایز و…

> نکته: فایل `schema.sql` توسط `.htaccess` از دسترسی عمومی مسدود است (`Require all denied`) — پس از نصب می‌توانید آن را روی سرور نگه دارید یا حذف کنید؛ هر دو امن است.

---

## گام ۴) ساخت inc/config.php از روی نمونه

فایل `install.php` دیگر وجود ندارد. به‌جایش:

1. در File Manager وارد `public_html/inc` شوید
2. فایل `config.sample.php` را **Copy** کنید به `config.php` (هم‌نام پوشه)
3. `config.php` را **Edit** کنید و مقادیر را پر کنید:

```php
return [
    'db' => [
        'host' => 'localhost',              // معمولاً localhost
        'name' => 'user_portfolio',         // نام کامل دیتابیس از گام ۱
        'user' => 'user_dbuser',            // کاربر دیتابیس از گام ۱
        'pass' => 'رمز_قوی_اینجا',          // رمز کاربر دیتابیس
    ],
    'site_url' => 'https://yourdomain.com', // بدون اسلش انتهایی
];
```

- `host` معمولاً `localhost` است؛ اگر هاست شما هاست جدا برای دیتابیس می‌دهد، همان را بگذارید
- `site_url` برای canonical و `sitemap.xml` استفاده می‌شود

> فایل `config.php` حاوی رمز است — توسط `.gitignore` نادیده گرفته می‌شود و هرگز Commit نکنید.

---

## گام ۵) ساخت کاربر ادمین با کوئری SQL

به‌جای فرم نصب، ادمین را مستقیماً در دیتابیس بسازید:

### ۵-۱) تولید هش رمز (bcrypt)

در **سیستم لوکال** خود (یا هر سرور با PHP) اجرا کنید:

```bash
php -r "echo password_hash('رمز_قوی_اینجا', PASSWORD_DEFAULT);"
```

خروجی چیزی شبیه `$2y$10$abcdefghijk...` است — آن را کپی کنید.

> هش هر بار متفاوت است؛ همین امن است. رمز را حداقل ۱۲ کاراکتر و ترکیبی بگذارید.

### ۵-۲) اجرای INSERT در phpMyAdmin

در phpMyAdmin → دیتابیس خود → تب **SQL** → کوئری زیر را با هش تولیدشده اجرا کنید:

```sql
INSERT INTO users (username, password_hash, display_name)
VALUES ('admin', '$2y$10$...هش_تولیدشده_را_اینجا_بگذارید...', 'مدیر سایت');
```

- برای ادمین دوم، همین کوئری را با `username` متفاوت تکرار کنید
- برای تغییر رمز موجود:
  ```sql
  UPDATE users SET password_hash = '$2y$10$...هش_جدید...' WHERE username = 'admin';
  ```

---

## گام ۶) ورود به پنل و بررسی

1. مرورگر → `https://yourdomain.com/admin/login.php`
2. با نام کاربری/رمز ساخته‌شده وارد شوید
3. **تنظیمات** → اطلاعات سایت، ایمیل، شبکه‌ها را تکمیل کنید
4. یک پروژهٔ آزمایشی بسازید و صفحهٔ عمومی آن را (`/work/{slug}`) بررسی کنید
5. فرم تماس را تست کنید و پیام را در **پیام‌ها** ببینید

اگر خطای `پیکربندی یافت نشد` دیدید → `inc/config.php` ساخته نشده یا مقادیرش اشتباه است (راهنمای `inc/config.sample.php` را ببینید).

---

## رفتار .htaccess نسخهٔ CMS

- اجبار HTTPS (ریدایرکت ۳۰۱ از HTTP)
- `ErrorDocument 404 → 404.php`
- مسیرهای تمیز: `/work`، `/work/{slug}`، `/about|services|contact`، `/sitemap.xml`، `/robots.txt`
- مسدودسازی عمومی: `*.sql`, `*.md`, `*.log`, `config.sample.php` + پوشهٔ `inc/` با `.htaccess` داخلی
- کش: CSS/JS immutable یک‌ساله، تصویر/فونت یک‌هفته‌ای، HTML بدون کش؛ فشرده‌سازی و هدرهای امنیتی
- `assets/uploads/.htaccess` اجرای هر فایل اسکریپتی را در پوشهٔ آپلودها مسدود می‌کند
- نیازمند Apache 2.4؛ در صورت استفاده از پراکسی معکوس، سرور باید `X-Forwarded-Proto` را درست تنظیم کند

---

## به‌روزرسانی CSS (اختیاری)

فقط اگر کلاس Tailwind جدیدی در قالب‌های PHP اضافه کردید — روی سیستم خودتان:

```bash
npm install
npm run build:cms-css
```

سپس `assets/css/style.css` را دوباره آپلود کنید. روی سرور Node لازم نیست.

---

## عیب‌یابی سریع

| علامت | علت محتمل |
|---|---|
| `پیکربندی یافت نشد` | `inc/config.php` وجود ندارد یا `db` ناقص است |
| صفحه سفید/خطای PDO | نام/رمز دیتابیس اشتباه یا کاربر به دیتابیس وصل نیست (ALL PRIVILEGES) |
| 404 روی `/work/xxx` | `.htaccess` آپلود نشده یا `mod_rewrite` غیرفعال |
| تصاویر آپلود نمی‌شوند | پوشهٔ `assets/uploads` باید قابل‌نوشتن باشد (755/775) |
| فونت لود نمی‌شود | بررسی `assets/fonts/*.woff2` و کش مرورگر |
