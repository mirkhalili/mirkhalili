-- QAVNS: 010.2.5.2
-- Create the first admin user in the existing users table.
-- Fixes MySQL #1267 by explicitly using the table's utf8mb4_unicode_ci collation.
-- Replace the bcrypt placeholder with a PHP password_hash(..., PASSWORD_DEFAULT) result.
-- No default password/hash is included.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @admin_username = CONVERT('admin' USING utf8mb4) COLLATE utf8mb4_unicode_ci;
SET @admin_display_name = CONVERT('مدیر سایت' USING utf8mb4) COLLATE utf8mb4_unicode_ci;
SET @admin_password_hash = 'REPLACE_WITH_60_CHARACTER_PHP_BCRYPT_HASH';

INSERT INTO users (username, password_hash, display_name)
SELECT @admin_username, @admin_password_hash, @admin_display_name
WHERE CHAR_LENGTH(@admin_password_hash) = 60
  AND LEFT(@admin_password_hash, 4) COLLATE utf8mb4_unicode_ci
      = CONVERT('$2y$' USING utf8mb4) COLLATE utf8mb4_unicode_ci
  AND NOT EXISTS (
      SELECT 1
      FROM users
      WHERE username COLLATE utf8mb4_unicode_ci
          = CONVERT(@admin_username USING utf8mb4) COLLATE utf8mb4_unicode_ci
  );

SELECT ROW_COUNT() AS created_rows;
