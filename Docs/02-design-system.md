# ۰۲ — سیستم طراحی

## پالت رنگ

| توکن | هگز | کاربرد |
|---|---|---|
| `paper` | `#F4F1EA` | زمینهٔ اصلی (کاغذ خام) |
| `paper-deep` | `#ECE8DE` | زمینهٔ بخش‌های متناوب |
| `ink` | `#191712` | متن اصلی، بلوک‌های تیره |
| `soft` | `#6F685C` | متن ثانویه/توضیح |
| `line` | `#D9D3C6` | خط‌های موگرید و جداول |
| `accent` | `#E4471C` | تأکیدها، hover، نقطهٔ دسترس‌پذیری |

تعریف در `assets/css/src/input.css` (بخش `@theme`) و خروجی کامپایل‌شده در `assets/css/style.css`.

## تایپوگرافی

- **فارسی**: وزیرمتن متغیر (وزن ۱۰۰–۹۰۰) خودمیزبان؛ تیترها `font-black`، متن `leading-8/9`
- **لاتین/اعداد**: Space Grotesk Variable برای اعداد (`num-lat`) و Space Mono برای برچسب‌های کوچک (`mono-label` با فاصلهٔ حرف ۰.۱۴em)
- `text-wrap: balance` برای تیترها و `pretty` برای پاراگراف‌ها

## گرید سوئیسی

- کانتینر `max-w-7xl` با گاتر `px-5 / md:px-10`
- **گرید تزئینی ثابت پس‌زمینه**: ۶ ستون در موبایل و ۱۲ ستون در دسکتاپ با خط‌های ۶٪ مرکب (`.grid-overlay`)
- بخش‌ها با `border-y border-line` از هم جدا می‌شوند؛ جدول‌های اطلاعات با `gap-px bg-line` ساخته می‌شوند

## حرکت (Motion)

| افکت | کلاس/مکانیزم | نکته |
|---|---|---|
| ورود تدریجی | `[data-reveal]` + IntersectionObserver | تأخیر پلکانی با `--reveal-delay` |
| پردهٔ تایپ هیرو | `.mask-line > span` | ترجمهٔ Y با تأخیر پلکانی |
| نوار متحرک | `.marquee` | حلقهٔ بدون درز با `translateX(50%)` در RTL |
| پیش‌نمایش شناور کارها | `#hover-preview` | فقط دسکتاپ (`hover:hover and pointer:fine`) |
| بزرگ‌نمایی تصویر | `.zoom-img` | scale 1.05 با منحنی `cubic-bezier(.22,1,.36,1)` |
| نقطهٔ دسترس‌پذیری | `.pulse-dot` | حلقهٔ ضربانی |

همهٔ انیمیشن‌ها با `@media (prefers-reduced-motion: reduce)` غیرفعال می‌شوند.

## RTL و منطقی‌سازی

- `html[dir=rtl][lang=fa]`
- فقط utilities منطقی Tailwind: `ps/pe/ms/me/start/end/text-start/border-s`
- جهت‌های دستی (ایمیل، اعداد، برچسب لاتین) با `dir="ltr"` محلی + `text-align:end`

## پنل ادمین

همان توکن‌ها با لایهٔ UI اختصاصی: سایدبار تیرهٔ چسبان، `.input`، `.btn/.btn-primary/.btn-danger`،
`.admin-table`، بج‌های `badge-ok/badge-draft` — تعریف‌شده در همان `input.css`.
