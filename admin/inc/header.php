<?php
/**
 * سربرگ پنل ادمین — متغیرها: $admin_title , $admin_active
 */
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
admin_require_login();

$admin_title = $admin_title ?? 'داشبورد';
$admin_active = $admin_active ?? '';
$unread = unread_messages_count();
$nav_items = [
    'index'      => ['/admin/', 'داشبورد'],
    'projects'   => ['/projects.php', 'پروژه‌ها'],
    'categories' => ['/categories.php', 'دسته‌بندی‌ها'],
    'services'   => ['/services.php', 'خدمات'],
    'resume'     => ['/resume.php', 'رزومه و جوایز'],
    'settings'   => ['/settings.php', 'تنظیمات سایت'],
    'messages'   => ['/messages.php', 'پیام‌ها'],
];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title><?= e($admin_title) ?> — پنل ادمین</title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="min-h-screen bg-paper-deep">
<div class="admin-shell">
  <aside class="admin-side">
    <a href="/" class="block border-b border-paper/10 px-5 py-5" target="_blank">
      <span class="block text-lg font-black"><?= e(setting('site_name')) ?></span>
      <span class="mono-label text-paper/50">مشاهدهٔ سایت ↗</span>
    </a>
    <nav class="flex-1 space-y-1 p-3">
      <?php foreach ($nav_items as $key => [$href, $label]): ?>
        <a href="<?= e($href) ?>" class="flex items-center justify-between px-4 py-2.5 text-sm font-bold transition-colors <?= $admin_active === $key ? 'bg-accent text-paper' : 'text-paper/70 hover:bg-paper/10 hover:text-paper' ?>">
          <?= e($label) ?>
          <?php if ($key === 'messages' && $unread > 0): ?><span class="rounded-full bg-paper px-2 py-0.5 text-xs font-black text-ink"><?= $unread ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="border-t border-paper/10 p-4 text-xs text-paper/50">
      <p class="font-bold text-paper/80"><?= e($_SESSION['admin_name'] ?? 'ادمین') ?></p>
      <form method="post" action="/admin/logout.php" class="mt-2">
        <?= csrf_field() ?>
        <button class="link-slide font-bold text-paper/70">خروج از پنل</button>
      </form>
    </div>
  </aside>

  <main class="admin-main">
    <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
      <h1 class="text-2xl font-black tracking-tight md:text-3xl"><?= e($admin_title) ?></h1>
      <?php $f = flash_get(); if ($f): ?>
        <p class="border px-4 py-2 text-sm font-bold <?= $f['type'] === 'ok' ? 'border-ink bg-paper text-ink' : 'border-accent bg-accent/10 text-accent' ?>"><?= e($f['msg']) ?></p>
      <?php endif; ?>
    </header>
