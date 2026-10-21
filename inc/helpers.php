<?php
/**
 * توابع کمکی عمومی (خروجی امن، CSRF، آپلود،slug و…)
 */
declare(strict_types=1);

/** خروجی امن HTML */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** پاراگراف‌های متن (جدا شده با دو اینتر) به HTML امن */
function paras(?string $text): string
{
    $out = [];
    foreach (preg_split('/\n{2,}/', trim((string) $text)) as $p) {
        $p = trim($p);
        if ($p !== '') {
            $out[] = '<p>' . e($p) . '</p>';
        }
    }
    return implode('', $out);
}

/** لیست عمودی (جدا شده با |) به آرایه */
function pipe_list(?string $s): array
{
    return array_values(array_filter(array_map('trim', explode('|', (string) $s)), 'strlen'));
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------------- CSRF ---------------- */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $t = $_POST['csrf'] ?? '';
    if (!is_string($t) || !hash_equals(csrf_token(), $t)) {
        http_response_code(419);
        exit('نشست منقضی یا نامعتبر است؛ صفحه را تازه‌سازی کنید.');
    }
}

/* ---------------- فلش پیام ---------------- */
function flash_set(string $msg, string $type = 'ok'): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function flash_get(): ?array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/* ---------------- slug ---------------- */
function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-');
}

function unique_slug(string $base, string $table, ?int $ignoreId = null): string
{
    $base = slugify($base) ?: 'item';
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = ?" . ($ignoreId ? ' AND id != ?' : '');
        $params = $ignoreId ? [$slug, $ignoreId] : [$slug];
        if (q_one($sql, $params) === null) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

/* ---------------- آپلود تصویر ---------------- */
function handle_upload(string $field, string $subdir = 'uploads'): ?string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('خطا در بارگذاری فایل.');
    }
    if ($f['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('حجم تصویر نباید بیش از ۸ مگابایت باشد.');
    }
    $info = @getimagesize($f['tmp_name']);
    if ($info === false) {
        throw new RuntimeException('فایل آپلودشده تصویر معتبر نیست.');
    }
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!isset($allowed[$info[2]])) {
        throw new RuntimeException('فقط تصویرهای JPG، PNG، WEBP یا GIF مجازند.');
    }
    $dir = dirname(__DIR__) . '/assets/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(8)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('ذخیرهٔ فایل روی سرور ممکن نشد.');
    }
    return '/assets/' . $subdir . '/' . $name;
}

/* ---------------- متفرقه ---------------- */
function current_path(): string
{
    $p = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return rtrim($p, '/') ?: '/';
}

/** شمارهٔ فارسی دو رقمی برای ایندکس‌ها */
function fa_index(int $n): string
{
    $fa = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    return str_replace(range(0, 9), $fa, str_pad((string) $n, 2, '0', STR_PAD_LEFT));
}

function asset(string $path): string
{
    return '/' . ltrim($path, '/');
}
