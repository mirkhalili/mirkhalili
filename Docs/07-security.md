# ۰۷ — امنیت

| لایه | پیاده‌سازی |
|---|---|
| رمز عبور | `password_hash()` / `password_verify()` (bcrypt) — هرگز متن خام ذخیره نمی‌شود |
| SQL Injection | همهٔ کوئری‌ها PDO prepared statement با `ATTR_EMULATE_PREPARES=false` |
| XSS | همهٔ خروجی‌ها با `e()` = `htmlspecialchars(ENT_QUOTES, UTF-8)`؛ فرم‌ها مقدار قبلی را escape می‌کنند |
| CSRF | توکن نشست‌محور روی همهٔ فرم‌های POST (سایت و پنل) با بررسی `hash_equals` |
| نشست | `session_regenerate_id(true)` پس از ورود؛ خروج، کوکی را منقضی می‌کند |
| آپلود | بررسی واقعی با `getimagesize`، فقط JPG/PNG/WEBP/GIF، سقف ۸MB، نام تصادفی ۱۶ هگز، و `.htaccess` ضد اجرا در `uploads/` |
| دسترسی فایل | `inc/` و `admin/inc/` با `.htaccess` و `Require all denied`؛ فایل‌های `.sql`/`.md`/`config.sample.php` از بیرون مسدود |
| فهرست پوشه | `Options -Indexes` در ریشه و `uploads/` |
| حذف‌ها | whitelist جدول‌ها در `safe_delete()` و `sync_children()` — نام جدول از ورودی کاربر هرگز مستقیم در کوئری نمی‌آید |
| هدرها | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` + اجبار HTTPS |
| فرم تماس | اعتبارسنجی ایمیل و طول، فهرست نوع پروژه، honeypot + CSRF + ذخیرهٔ پارامتری |
| ساخت اولین مدیر | کلید ۶۴ رقمی در config، فقط در نبود کاربر، قفل MySQL و غیرفعال‌سازی پس از ایجاد |

## توصیه‌های عملیاتی پس از نصب (010.2.5.2)

1. اطمینان حاصل کنید هیچ فایل `install.php` قدیمی روی سرور نمانده است (از نگارش 007.2.3.0 حذف شده؛ اگر از نگارش‌های قبل ارتقا می‌دهید، دستی حذف کنید).
2. رمز ادمین قوی (۱۲+ کاراکتر) و متفاوت از رمز cPanel — تغییر دوره‌ای از پنل → تنظیمات.
3. در cPanel، AutoSSL را فعال نگه دارید؛ `.htaccess` همهٔ ترافیک را به HTTPS می‌برد.
4. پشتیبان‌گیری هفتگی دیتابیس از cPanel → Backup Wizard و نگهداری خارج از هاست.
5. دسترسی File Manager را فقط به افراد مجاز بدهید؛ `inc/config.php` حاوی رمز دیتابیس است.


پس از ایجاد نخستین مدیر با `/admin/setup.php` کلید `admin_setup_key` را از `inc/config.php` حذف کنید. اگر دیتابیس از قبل کاربر دارد، فرم ساخت اولیه غیرفعال است.


فایل جاری `database/010.2.5.2-create-admin.sql` هیچ رمز پیش‌فرضی ندارد؛ placeholder هش را با مقدار تولیدشدهٔ PHP جایگزین کنید و فایل اصلاح‌شده را در phpMyAdmin اجرا کنید.


اسکریپت `database/010.2.5.2-create-admin.sql` فقط هش تولیدشده توسط PHP را می‌پذیرد؛ رمز خام یا اعتبارنامهٔ پیش‌فرض در مخزن قرار ندارد.
