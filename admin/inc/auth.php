<?php
/**
 * هستهٔ احراز هویت پنل ادمین
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/inc/db.php';
require_once dirname(__DIR__, 2) . '/inc/helpers.php';
require_once dirname(__DIR__, 2) . '/inc/data.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('MKSESSID');
    session_start();
}

function admin_logged_in(): bool
{
    return isset($_SESSION['admin_id']) && (int) $_SESSION['admin_id'] > 0;
}

function admin_require_login(): void
{
    if (!admin_logged_in()) {
        redirect('/admin/login.php');
    }
}

function admin_login(string $username, string $password): bool
{
    $user = q_one('SELECT * FROM users WHERE username = ?', [$username]);
    if ($user === null || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $user['id'];
    $_SESSION['admin_name'] = $user['display_name'] !== '' ? $user['display_name'] : $user['username'];
    return true;
}

function admin_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** حذف امن یک ردیف بر اساس id از جدول مجاز */
function safe_delete(string $table, int $id): void
{
    $allowed = ['projects', 'categories', 'services', 'service_items', 'process_steps', 'stats', 'timeline', 'awards', 'skills', 'socials', 'messages', 'project_images'];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('جدول نامجاز: ' . $table);
    }
    q("DELETE FROM {$table} WHERE id = ?", [$id]);
}

/** درج/به‌روزرسانی ردیف‌های تکرارشوندهٔ فرزند یک پروژه یا خدمت */
function sync_children($table, $fkCol, $parentId, $rows)
{
    $allowed = [
        'project_services' => ['label'],
        'project_palette'  => ['hex', 'name'],
        'project_metrics'  => ['value', 'label'],
        'service_items'    => ['label'],
    ];
    if (!isset($allowed[$table])) {
        throw new InvalidArgumentException('جدول نامجاز: ' . $table);
    }
    $cols = $allowed[$table];
    q("DELETE FROM {$table} WHERE {$fkCol} = ?", [$parentId]);
    $sort = 0;
    foreach ($rows as $row) {
        $values = [];
        $empty = true;
        foreach ($cols as $c) {
            $v = trim((string) ($row[$c] ?? ''));
            if ($v !== '') {
                $empty = false;
            }
            $values[] = $v;
        }
        if ($empty) {
            continue;
        }
        $sort++;
        $placeholders = implode(', ', array_fill(0, count($cols) + 2, '?'));
        $sql = "INSERT INTO {$table} ({$fkCol}, " . implode(', ', $cols) . ", sort) VALUES ({$placeholders})";
        q($sql, array_merge([$parentId], $values, [$sort]));
    }
}
