<?php
/**
 * فایل نمونهٔ پیکربندی دیتابیس و سایت — میر خلیلی CMS
 *
 * این فایل «نمونه» است؛ نسخهٔ واقعی باید با نام inc/config.php ساخته شود
 * و هرگز Commit نشود (در .gitignore نادیده گرفته می‌شود).
 *
 * ── راهنمای نصب بدون install.php ──────────────────────────────────
 * ۱) یک کپی از همین فایل بسازید:
 *      inc/config.sample.php  →  inc/config.php
 *    (در File Manager کپی کنید یا در ترمینال: cp inc/config.sample.php inc/config.php)
 *
 * ۲) مقادیر زیر را با اطلاعات دیتابیس ساخته‌شده در cPanel پر کنید:
 *      host     : معمولاً localhost
 *      name     : نام دیتابیس (مثلاً mirkhalili_portfolio)
 *      user     : نام کاربر دیتابیس
 *      pass     : رمز کاربر دیتابیس
 *      site_url : آدرس کامل سایت بدون اسلش انتهایی (مثلاً https://mirkhalili.com)
 *
 * ۳) فایل schema.sql را از طریق phpMyAdmin → تب Import درون دیتابیس ایمپورت کنید
 *    (تمام جداول و داده‌های اولیه ساخته می‌شوند).
 *
 * ۴) کاربر ادمین را با کوئری SQL بسازید (راهنمای کامل در Docs/06-install-deploy.md):
 *      برای تولید هش رمز، در سیستم لوکال اجرا کنید:
 *        php -r "echo password_hash('رمز_قوی_اینجا', PASSWORD_DEFAULT);"
 *      سپس خروجی را در کوئری زیر جایگزین کنید:
 *        INSERT INTO users (username, password_hash, display_name)
 *        VALUES ('admin', '$2y$10$...هش_تولیدشده...', 'مدیر سایت');
 *
 * ۵) وارد پنل شوید: https://دامنه/admin/login.php
 *
 * نکتهٔ امنیتی: فایل inc/config.php حاوی رمز دیتابیس است — آن را عمومی نکنید
 * و دسترسی File Manager را محدود نگه دارید. فایل‌های .sql و .sample.php
 * به‌صورت پیش‌فرض توسط .htaccess از دسترسی عمومی مسدود هستند.
 *
 * نگارش: 005.2.2.0 — مستندات نصب: Docs/06-install-deploy.md
 */
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'USERNAME_portfolio',   // نام دیتابیس ساخته‌شده در cPanel → MySQL Databases
        'user' => 'USERNAME_dbuser',      // کاربر دیتابیس با دسترسی ALL PRIVILEGES به دیتابیس
        'pass' => 'PASSWORD',             // رمز کاربر دیتابیس
    ],
    // آدرس کامل سایت (بدون اسلش انتهایی) — برای canonical، sitemap و لینک‌ها
    'site_url' => 'https://yourdomain.com',
];
