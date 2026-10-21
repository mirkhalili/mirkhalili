<?php
declare(strict_types=1);
$admin_title = 'رویدادنگاری پنل'; $admin_active = 'activity';
require_once __DIR__ . '/inc/auth.php'; admin_require_module('activity');
$rows = q_all('SELECT a.*, u.username, u.display_name FROM admin_activity a LEFT JOIN users u ON u.id = a.actor_id ORDER BY a.id DESC LIMIT 250');
require __DIR__ . '/inc/header.php'; ?>
<div class="mb-4 text-sm text-soft">۲۵۰ رویداد اخیر؛ اطلاعات فرم‌ها، IP و داده‌های حساس ثبت نمی‌شوند.</div><div class="overflow-x-auto border border-line bg-paper"><table class="w-full text-right text-sm"><thead><tr class="border-b border-line"><th class="p-3">زمان</th><th class="p-3">کاربر</th><th class="p-3">عملیات</th><th class="p-3">بخش</th><th class="p-3">توضیح</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr class="border-b border-line"><td class="p-3 whitespace-nowrap"><?= e($r['created_at']) ?></td><td class="p-3"><?= e($r['display_name'] ?: $r['username'] ?: 'کاربر حذف‌شده') ?></td><td class="p-3"><?= e($r['action']) ?></td><td class="p-3"><?= e($r['entity_type']) ?></td><td class="p-3"><?= e($r['description']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/inc/footer.php'; ?>
