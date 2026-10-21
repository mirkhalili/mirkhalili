<?php
/**
 * اتصال PDO به دیتابیس + بارگذاری تنظیمات
 */
declare(strict_types=1);

function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $file = __DIR__ . '/config.php';
        if (!is_file($file)) {
            return [];
        }
        $config = require $file;
    }
    return is_array($config) ? $config : [];
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $cfg = app_config();
    if (!$cfg || !isset($cfg['db'])) {
        http_response_code(503);
        exit('پیکربندی یافت نشد. لطفاً فایل inc/config.php را از روی inc/config.sample.php بسازید و مقادیر دیتابیس را پر کنید. راهنما: Docs/06-install-deploy.md');
    }
    $d = $cfg['db'];
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $d['host'], $d['name']);
    $pdo = new PDO($dsn, $d['user'], $d['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/** اجرای کوئری آماده‌شده و برگرداندن statement */
function q(string $sql, array $params = []): PDOStatement
{
    $pdo = db();
    $st = $pdo->prepare($sql);
    $st->execute($params);
    // Capture successful admin-side mutations without retaining submitted values or secrets.
    if ($st->rowCount() > 0 && session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['admin_id'])
        && (str_starts_with((string) ($_SERVER['SCRIPT_NAME'] ?? ''), '/admin/')
            || str_contains(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_FILENAME'] ?? '')), '/admin/'))
        && preg_match('/^\s*(INSERT\s+INTO|UPDATE|DELETE\s+FROM)\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $match)
        && !in_array(strtolower($match[2]), ['admin_activity', 'site_visits'], true)) {
        try {
            $op = strtoupper($match[1]);
            $action = str_starts_with($op, 'INSERT') ? 'insert' : (str_starts_with($op, 'UPDATE') ? 'update' : 'delete');
            $log = $pdo->prepare('INSERT INTO admin_activity (actor_id, action, entity_type, description) VALUES (?, ?, ?, ?)');
            $log->execute([(int) $_SESSION['admin_id'], $action, strtolower($match[2]), 'عملیات ' . $action . ' روی ' . strtolower($match[2])]);
        } catch (Throwable $exception) {
            error_log('Admin activity log failed: ' . $exception->getMessage());
        }
    }
    return $st;
}

function q_all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

function q_one(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row === false ? null : $row;
}

function q_value(string $sql, array $params = [], $default = null)
{
    $row = q_one($sql, $params);
    if ($row === null) {
        return $default;
    }
    return reset($row);
}
