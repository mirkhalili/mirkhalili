<?php
/**
 * سربرگ سایت عمومی — قبل از این فایل متغیرهای زیر را ست کنید:
 * $page_title , $page_desc , $page_og (اختیاری)
 */
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/data.php';

$site_name = setting('site_name', 'میر خلیلی');
$page_title = isset($page_title) && $page_title !== '' ? $page_title . ' — ' . $site_name : $site_name . ' — ' . setting('role', 'طراح گرافیک');
$page_desc = $page_desc ?? setting('hero_intro', 'پورتفولیوی طراح گرافیک و هویت بصری');
$page_og = $page_og ?? '/assets/uploads/studio.jpg';
$path = current_path();
$app_cfg = app_config();
$base_url = rtrim((string) ($app_cfg['site_url'] ?? ''), '/');
$canonical = $base_url . ($path === '/' ? '/' : $path);

$nav = [
    '/'        => 'خانه',
    '/work'    => 'کارها',
    '/about'   => 'درباره',
    '/services' => 'خدمات',
    '/contact' => 'تماس',
];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_desc) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#f4f1ea">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="preload" as="font" type="font/woff2" href="/assets/fonts/vazirmatn-var.woff2" crossorigin>
<meta property="og:type" content="website">
<meta property="og:locale" content="fa_IR">
<meta property="og:site_name" content="<?= e($site_name) ?>">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_desc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($base_url . '/' . ltrim($page_og, '/')) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="relative min-h-screen">

<div class="grid-overlay" aria-hidden="true"><div></div><div></div><div></div><div></div><div></div><div></div><div class="hidden md:block"></div><div class="hidden md:block"></div><div class="hidden md:block"></div><div class="hidden md:block"></div><div class="hidden md:block"></div><div class="hidden md:block"></div></div>

<header class="fixed inset-x-0 top-0 z-50 border-b border-line bg-paper/85 backdrop-blur-md">
  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-5 md:h-20 md:px-10">
    <a href="/" class="group flex items-center gap-3" aria-label="<?= e($site_name) ?> — خانه">
      <span class="grid size-8 place-items-center bg-ink text-paper transition-colors group-hover:bg-accent md:size-9">
        <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 20V6l8 8 8-8v14"/></svg>
      </span>
      <span class="leading-tight">
        <span class="block text-sm font-extrabold tracking-tight md:text-base"><?= e($site_name) ?></span>
        <span class="mono-label block text-soft" dir="ltr">Graphic Designer</span>
      </span>
    </a>

    <nav class="hidden items-center gap-8 md:flex" aria-label="ناوبری اصلی">
      <?php foreach ($nav as $href => $label): $active = ($path === $href) || ($href !== '/' && str_starts_with($path, $href)); ?>
        <a href="<?= e($href) ?>" class="link-slide text-sm font-medium transition-colors <?= $active ? 'text-accent' : 'text-ink hover:text-accent' ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="hidden items-center gap-5 md:flex">
      <span class="mono-label flex items-center gap-2 text-soft" dir="ltr">
        <span class="pulse-dot inline-block size-1.5 rounded-full bg-accent text-accent"></span>
        <span data-clock>--:--:--</span> TEH
      </span>
      <a href="/contact" class="border border-ink px-4 py-2 text-sm font-bold transition-colors hover:bg-ink hover:text-paper">شروع پروژه</a>
    </div>

    <button id="menu-btn" class="flex size-10 flex-col items-center justify-center gap-1.5 md:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="باز و بسته کردن منو">
      <span class="menu-bar h-px w-6 bg-ink transition-transform"></span>
      <span class="menu-bar h-px w-6 bg-ink transition-transform"></span>
    </button>
  </div>

  <div id="mobile-menu" class="fixed inset-0 top-16 z-40 hidden flex-col bg-paper px-5 pt-8 pb-10 md:hidden">
    <nav class="flex flex-col divide-y divide-line" aria-label="ناوبری موبایل">
      <?php $i = 0; foreach ($nav as $href => $label): $i++; ?>
        <a href="<?= e($href) ?>" class="flex items-baseline justify-between py-5 text-3xl font-extrabold tracking-tight">
          <?= e($label) ?><span class="mono-label text-soft"><?= fa_index($i) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="mt-auto space-y-2">
      <p class="mono-label text-soft">ساعت تهران</p>
      <p class="num-lat text-2xl font-bold" data-clock>--:--:--</p>
      <a href="mailto:<?= e(setting('email')) ?>" class="link-slide block w-fit text-lg font-bold"><?= e(setting('email')) ?></a>
    </div>
  </div>
</header>

<main id="main" class="relative z-10">
