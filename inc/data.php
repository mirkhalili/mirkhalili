<?php
/**
 * لایهٔ دسترسی به داده‌ها برای سایت عمومی
 */
declare(strict_types=1);

function settings_all(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (q_all('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings_all();
    return (string) ($all[$key] ?? $default);
}

function socials_all(): array
{
    return q_all('SELECT * FROM socials ORDER BY sort, id');
}

function categories_all(): array
{
    return q_all('SELECT * FROM categories ORDER BY sort, id');
}

/** فهرست پروژه‌های منتشرشده (+ نام دسته) */
function projects_all(?string $categorySlug = null): array
{
    $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM projects p LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.published = 1';
    $params = [];
    if ($categorySlug !== null) {
        $sql .= ' AND c.slug = ?';
        $params[] = $categorySlug;
    }
    $sql .= ' ORDER BY p.sort, p.id';
    return q_all($sql, $params);
}

function project_by_slug(string $slug): ?array
{
    return q_one(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM projects p LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.slug = ? AND p.published = 1',
        [$slug]
    );
}

function project_services(int $id): array
{
    return q_all('SELECT label FROM project_services WHERE project_id = ? ORDER BY sort, id', [$id]);
}

function project_palette(int $id): array
{
    return q_all('SELECT hex, name FROM project_palette WHERE project_id = ? ORDER BY sort, id', [$id]);
}

function project_metrics(int $id): array
{
    return q_all('SELECT value, label FROM project_metrics WHERE project_id = ? ORDER BY sort, id', [$id]);
}

function project_images(int $id): array
{
    return q_all('SELECT path, caption FROM project_images WHERE project_id = ? ORDER BY sort, id', [$id]);
}

function services_all(): array
{
    $rows = q_all('SELECT * FROM services ORDER BY sort, id');
    foreach ($rows as &$s) {
        $s['items'] = q_all('SELECT label FROM service_items WHERE service_id = ? ORDER BY sort, id', [$s['id']]);
    }
    return $rows;
}

function process_all(): array
{
    return q_all('SELECT * FROM process_steps ORDER BY sort, id');
}

function stats_all(): array
{
    return q_all('SELECT * FROM stats ORDER BY sort, id');
}

function timeline_all(): array
{
    return q_all('SELECT * FROM timeline ORDER BY sort, id');
}

function awards_all(): array
{
    return q_all('SELECT * FROM awards ORDER BY sort, id');
}

function skills_all(): array
{
    return q_all('SELECT * FROM skills ORDER BY sort, id');
}

function unread_messages_count(): int
{
    return (int) q_value('SELECT COUNT(*) FROM messages WHERE is_read = 0', [], 0);
}
