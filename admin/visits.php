<?php
declare(strict_types=1);
$admin_title = 'آمار بازدیدها'; $admin_active = 'visits';
require_once __DIR__ . '/inc/auth.php'; admin_require_module('visits');
$daily = q_all('SELECT DATE(visited_at) AS day, COUNT(*) AS views FROM site_visits WHERE visited_at >= DATE_SUB(CURRENT_DATE, INTERVAL 29 DAY) GROUP BY DATE(visited_at) ORDER BY day DESC');
$pages = q_all('SELECT path, COUNT(*) AS views FROM site_visits WHERE visited_at >= DATE_SUB(CURRENT_DATE, INTERVAL 29 DAY) GROUP BY path ORDER BY views DESC LIMIT 20');
$total = (int) q_value('SELECT COUNT(*) FROM site_visits WHERE visited_at >= DATE_SUB(CURRENT_DATE, INTERVAL 29 DAY)', [], 0);
$today = (int) q_value('SELECT COUNT(*) FROM site_visits WHERE visited_at >= CURRENT_DATE', [], 0);
require __DIR__ . '/inc/header.php'; ?>
<div class="mb-6 grid gap-px border border-line bg-line sm:grid-cols-2"><div class="bg-paper p-5"><p class="text-3xl font-black"><?= number_format($today) ?></p><p class="text-soft">بازدید امروز</p></div><div class="bg-paper p-5"><p class="text-3xl font-black"><?= number_format($total) ?></p><p class="text-soft">بازدید ۳۰ روز اخیر</p></div></div>
<div class="grid gap-8 xl:grid-cols-2"><section class="border border-line bg-paper p-5"><h2 class="mb-3 font-extrabold">روزانه</h2><div class="space-y-2"><?php foreach($daily as $d): ?><div class="grid grid-cols-[7rem_1fr_4rem] items-center gap-3 text-sm"><span><?= e($d['day']) ?></span><div class="h-2 bg-paper-deep"><div class="h-full bg-accent" style="width:<?= $total ? max(2, (int)round(100*$d['views']/$total)) : 0 ?>%"></div></div><b><?= number_format((int)$d['views']) ?></b></div><?php endforeach; ?></div></section><section class="border border-line bg-paper p-5"><h2 class="mb-3 font-extrabold">صفحه‌های پربازدید (۳۰ روز)</h2><ol class="space-y-2"><?php foreach($pages as $i=>$p): ?><li class="flex justify-between gap-3 border-b border-line py-2"><span class="truncate"><span class="mono-label text-soft"><?= $i+1 ?>.</span> <code><?= e($p['path']) ?></code></span><b><?= number_format((int)$p['views']) ?></b></li><?php endforeach; ?></ol><p class="mt-4 text-xs text-soft">آمار تجمیعی است؛ IP و user-agent ذخیره نمی‌شوند.</p></section></div>
<?php require __DIR__ . '/inc/footer.php'; ?>
