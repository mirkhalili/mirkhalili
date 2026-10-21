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
| فرم تماس | اعتبارسنجی `filter_var(..., FILTER_VALIDATE_EMAIL)` + CSRF + ذخیرهٔ پارامتری |

## توصیه‌های عملیاتی پس از نصب (007.2.3.0)

1. اطمینان حاصل کنید هیچ فایل `install.php` قدیمی روی سرور نمانده است (از نگارش 007.2.3.0 حذف شده؛ اگر از نگارش‌های قبل ارتقا می‌دهید، دستی حذف کنید).
2. رمز ادمین قوی (۱۲+ کاراکتر) و متفاوت از رمز cPanel — تغییر دوره‌ای از پنل → تنظیمات.
3. در cPanel، AutoSSL را فعال نگه دارید؛ `.htaccess` همهٔ ترافیک را به HTTPS می‌برد.
4. پشتیبان‌گیری هفتگی دیتابیس از cPanel → Backup Wizard و نگهداری خارج از هاست.
5. دسترسی File Manager را فقط به افراد مجاز بدهید؛ `inc/config.php` حاوی رمز دیتابیس است.
