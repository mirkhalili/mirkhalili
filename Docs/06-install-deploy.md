# ۰۶ — نصب و دیپلوی (CMS تک‌نسخه‌ای)

> **نگارش جاری 012.2.6.1:** ریشهٔ ریپو دقیقاً محتوای `public_html` است.
> فایل نصب قدیمی حذف شده؛ ابتدا `schema.sql` را ایمپورت و `inc/config.php` را بسازید، سپس با کلید یک‌بارمصرف از `/admin/setup.php` اولین مدیر را ایجاد کنید.

---

## پیش‌نیاز سرور

- **PHP 8.1 یا بالاتر** با افزونهٔ `pdo_mysql` (در هاست اشتراکی cPanel پیش‌فرض فعال است)
- **MySQL / MariaDB** با پشتیبانی `utf8mb4`
- **Apache** با `mod_rewrite` و `mod_headers` (برای `.htaccess`; مسیرهای اصلی دارای fallback هستند، اما جزئیات پروژه نیازمند mod_rewrite است)
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
1. در سیستم خودتان کل ریپو را Zip کنید **یا** از صفحهٔ Release فایل `mirkhalili-010.2.5.2.zip` را دانلود کنید
2. cPanel → **File Manager** → وارد `public_html` شوید
3. اگر فایل‌های پیش‌فرض هاست (مثل `index.php` نمونه) هست، حذف کنید
4. فایل را **Upload** و **Extract** کنید. اگر ZIP از «Source code» گیت‌هاب است، ابتدا پوشهٔ نسخه را باز کنید و **محتویات داخلش** را به `public_html` منتقل کنید؛ خود پوشه را درون `public_html` نگه ندارید. ZIP بستهٔ آماده، فایل‌ها را مستقیم در ریشه دارد.
5. فایل ZIP و پوشهٔ خالی نسخه را حذف کنید
6. بررسی: **Settings → Show Hidden Files** را بزنید و مطمئن شوید `.htaccess`، پوشه‌های `inc/`, `admin/`, `assets/` و فایل `index.php` همگی مستقیماً داخل `public_html` هستند

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
├── about/ work/ services/ contact/ (fallback route index.php)
├── admin/  (login.php + setup.php + سایر صفحه‌ها)
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
    'admin_setup_key' => '۶۴ رقم هگز تصادفی که تولید کرده‌اید',
];
```

- `host` معمولاً `localhost` است؛ اگر هاست شما هاست جدا برای دیتابیس می‌دهد، همان را بگذارید
- `site_url` برای canonical و `sitemap.xml` استفاده می‌شود

> فایل `config.php` حاوی رمز است — توسط `.gitignore` نادیده گرفته می‌شود و هرگز Commit نکنید.

---

## گام ۵) ساخت اولین کاربر مدیر (ایمن و یک‌بارمصرف)

در نسخهٔ جاری پنل فرم راه‌اندازی دارد؛ هیچ نام کاربری/رمز پیش‌فرض یا اکانت از پیش‌ساخته‌ای در کد قرار نگرفته است.

1. در `inc/config.php` کلید `admin_setup_key` را اضافه کنید؛ یک کلید ۶۴ رقمی hex تصادفی بسازید (در cPanel Terminal یا سیستم خودتان):
   ```bash
   php -r "echo bin2hex(random_bytes(32));"
   ```
2. مقدار تولیدشده را در آرایهٔ config قرار دهید، مثلاً: `'admin_setup_key' => 'کلید۶۴رقمی',`
3. صفحهٔ `https://دامنه/admin/setup.php` را باز کنید؛ کلید را وارد و نام کاربری، نام نمایشی و رمز قوی ۱۲ نویسه‌ای را ثبت کنید.
4. پس از ساخت نخستین مدیر، راه‌اندازی با قفل دیتابیس بسته می‌شود؛ کلید را از `inc/config.php` حذف کنید.

اگر PHP CLI در دسترس نیست، از phpMyAdmin طبق روش SQL پایین استفاده کنید. این مسیر فرم setup را لازم ندارد.

### روش جایگزین: ساخت با فایل SQL نسخه‌دار (پیشنهادی برای این خطا)

1. در کامپیوتر خود PHP را اجرا کنید و به‌جای متن داخل کوتیشن رمز دلخواه ۱۲ نویسه‌ای بگذارید:
   ```bash
   php -r "echo password_hash('YOUR-STRONG-PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"
   ```
2. خروجی `$2y$...` را کپی کنید. فایل `database/010.2.5.2-create-admin.sql` را باز کنید و مقدار `REPLACE_WITH_60_CHARACTER_PHP_BCRYPT_HASH` را با خروجی جایگزین کنید. فقط خود هش را بگذارید؛ آن را در کوتیشن‌های فایل نگه دارید.
3. در phpMyAdmin دیتابیس سایت → تب **SQL**، کل محتوای فایل اصلاح‌شده را paste و اجرا کنید.
4. نتیجه باید `created_rows = 1` باشد. اگر `0` است، نام کاربری `admin` از قبل وجود دارد یا هش PHP در فایل جایگزین نشده/۶۰ نویسه‌ای نیست.
5. وارد `/admin/login.php` شوید؛ نام کاربری پیش‌فرض این فایل `admin` است و رمز همان رمزی است که خودتان برای هش انتخاب کردید. بلافاصله بعد از ورود، رمز را از تنظیمات عوض کنید.

این فایل فقط در جدول موجود `users` یک ردیف ایجاد می‌کند؛ جدول جدید یا تغییر ساختاری دیتابیس ندارد. فایل SQL را با placeholder اجرا نکنید.

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


## بررسی خطای ۴۰۴ صفحه‌ها

در ریشهٔ `public_html` باید `index.php`, `.htaccess` و پوشه‌های `about/`, `work/`, `services/`, `contact/` وجود داشته باشند. بستهٔ Source گیت‌هاب پوشهٔ والد دارد؛ محتویات داخل آن را به ریشهٔ هاست منتقل کنید. پوشه‌های fallback صفحه‌های اصلی را حتی بدون mod_rewrite باز می‌کنند؛ لینک‌های `/work/slug` برای صفحهٔ پروژه نیازمند `mod_rewrite` فعال است. در صورت ۴۰۴، از پشتیبانی هاست بخواهید `AllowOverride All` و `mod_rewrite` را برای `public_html` فعال کنند و نمایش فایل‌های مخفی را در File Manager روشن کنید.

فونت‌ها در `assets/fonts/` داخل همین بسته هستند و از همان هاست بارگذاری می‌شوند؛ درخواست فونت به سرویس بیرونی وجود ندارد.


### ارتقا به QAVNS 011.2.6.0

1. از فایل‌ها و دیتابیس بکاپ بگیرید.
2. فایل `database/011.2.6.0-admin-expansion.sql` را یک‌بار در phpMyAdmin روی همان دیتابیس اجرا کنید.
3. فایل‌های بستهٔ نسخهٔ جدید را جایگزین کنید (فایل تنظیمات محرمانهٔ `inc/config.php` را حفظ کنید).
4. با حساب مدیر قبلی وارد شوید و در «کاربران و دسترسی‌ها» حساب‌های موردنیاز و نقش‌ها را تنظیم کنید.

کد این نسخه به ستون‌ها و جدول‌های migration نیاز دارد؛ قبل از اجرای migration کد را deploy نکنید.
