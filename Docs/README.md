# Docs — راهنمای مستندات پروژهٔ میر خلیلی

> نگارش جاری: **005.2.2.0** — تک‌نسخه‌ای CMS (ریشه = public_html)

این پوشه مرجع کامل مستندات فارسی پروژه است. هر سند یک جنبه را پوشش می‌دهد:

| # | سند | موضوع |
|---|---|---|
| 01 | [01-overview.md](01-overview.md) | توضیح کلی سایت، ساختار پوشه‌ها، پشتهٔ فنی و فهرست صفحه‌ها |
| 02 | [02-design-system.md](02-design-system.md) | پالت رنگ، تایپوگرافی، گرید سوئیسی، حرکت و RTL |
| 03 | [03-public-features.md](03-public-features.md) | قابلیت‌های سایت عمومی (قابلیت‌به‌قابلیت) |
| 04 | [04-admin-features.md](04-admin-features.md) | قابلیت‌های پنل ادمین (قابلیت‌به‌قابلیت) |
| 05 | [05-database.md](05-database.md) | اسکیمای دیتابیس، ارتباط‌ها و داده‌های اولیه |
| 06 | [06-install-deploy.md](06-install-deploy.md) | **نصب و دیپلوی بدون install.php** (phpMyAdmin + config.sample.php + کوئری ادمین) |
| 07 | [07-security.md](07-security.md) | لایه‌های امنیتی و توصیه‌های عملیاتی |
| 08 | [08-seo-performance.md](08-seo-performance.md) | سئو، دسترس‌پذیری و کارا |
| 09 | [09-content-guide.md](09-content-guide.md) | راهنمای تولید و ویرایش محتوا (پنل) |
| — | [QAVNS.md](QAVNS.md) | سند بالادستی طرح چهارگانهٔ شماره‌گذاری نگارش‌ها |
| — | [VERSIONING.md](VERSIONING.md) | تطبیق QAVNS بر این پروژه + تاریخچهٔ نگارش‌ها + آیین نشر |
| — | [CHANGELOG.md](CHANGELOG.md) | رویدادنگاری تغییرات هر نگارش |

## تغییرات نگارش 005.2.2.0

- حذف کامل نسخهٔ استاتیک Astro (`src/`, `public/`, `astro.config.mjs`, `tsconfig.json`)
- حذف `install.php` — نصب اکنون دستی و امن‌تر (ایمپورت `schema.sql` + `inc/config.sample.php`)
- ریشهٔ ریپو = `public_html` (بدون پوشهٔ واسط `cpanel-site/`)
- به‌روزرسانی همهٔ اسناد به ساختار جدید

## نگارش‌های پیشین

- **004.2.1.0**: افزودن پوشهٔ `Docs/` با ۱۰ سند + انتقال QAVNS به `Docs/` + انتشار رسمی روی `main`
- **003.2.0.0**: نگارش کلان دوم — CMS کامل PHP+MySQL با پنل ادمین و `schema.sql`
- **002.1.2.0**: زیرساخت دیپلوی cPanel برای نسخهٔ استاتیک (اکنون منسوخ)
- **001.1.1.0**: نخستین نسل — سایت استاتیک Astro + Tailwind

تاریخچهٔ کامل: [CHANGELOG.md](CHANGELOG.md) و [VERSIONING.md](VERSIONING.md)
