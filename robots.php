<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/helpers.php';

header('Content-Type: text/plain; charset=utf-8');
$cfg = app_config();
$base = rtrim((string) ($cfg['site_url'] ?? 'https://example.com'), '/');
echo "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /inc\n\nSitemap: {$base}/sitemap.xml\n";
