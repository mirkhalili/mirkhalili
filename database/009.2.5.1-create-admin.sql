-- QAVNS: 009.2.5.1
-- Create the first admin user in the existing users table.
-- 1) Replace the password hash below with a PHP password_hash(..., PASSWORD_DEFAULT) result.
-- 2) Run the entire file in phpMyAdmin > SQL against the portfolio database.
-- 3) Confirm created_rows = 1. If it is 0, the hash is invalid or username already exists.
-- This template intentionally contains no default password or valid password hash.

SET NAMES utf8mb4;

SET @admin_username = 'admin';
SET @admin_display_name = 'مدیر سایت';
SET @admin_password_hash = 'REPLACE_WITH_60_CHARACTER_PHP_BCRYPT_HASH';

INSERT INTO users (username, password_hash, display_name)
SELECT @admin_username, @admin_password_hash, @admin_display_name
WHERE CHAR_LENGTH(@admin_password_hash) = 60
  AND LEFT(@admin_password_hash, 4) = '$2y$'
  AND NOT EXISTS (
      SELECT 1 FROM users WHERE username = @admin_username
  );

SELECT ROW_COUNT() AS created_rows;
