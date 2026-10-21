<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/data.php';

header('Content-Type: text/xml; charset=utf-8');
$cfg = app_config();
$base = rtrim((string) ($cfg['site_url'] ?? 'https://example.com'), '/');
$urls = [
    ['loc' => $base . '/', 'prio' => '1.0'],
    ['loc' => $base . '/work', 'prio' => '0.9'],
    ['loc' => $base . '/about', 'prio' => '0.7'],
    ['loc' => $base . '/services', 'prio' => '0.7'],
    ['loc' => $base . '/contact', 'prio' => '0.6'],
];
foreach (projects_all() as $p) {
    $urls[] = ['loc' => $base . '/work/' . $p['slug'], 'prio' => '0.8'];
}
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url><loc><?= e($u['loc']) ?></loc><priority><?= $u['prio'] ?></priority></url>
<?php endforeach; ?>
</urlset>
