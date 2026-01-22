-- docker/sql/create_tables.sql
-- SQL script to create the `users` table used by the application.
-- Run this in your MariaDB/MySQL instance (inside the docker 'db' container if applicable).

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NOTE: convenience admin user for development/testing
-- WARNING: this inserts the password in plaintext ("xd") solely for quick local testing.
-- Do NOT use plaintext passwords in production. Replace the value below with a hash
-- created by PHP's password_hash() before going to production.
-- To generate a hash run:
--   php -r "echo password_hash('xd', PASSWORD_DEFAULT) . PHP_EOL;"
-- then copy the generated hash into the INSERT (replace 'xd').
INSERT INTO `users` (`username`, `password`, `email`) VALUES ('admin', 'adminadmin', 'admin@example.com');

-- Example: insert a user.
-- IMPORTANT: passwords must be stored as PHP password_hash() values (bcrypt/argon2) for the authenticator
-- to verify them with password_verify(). Do NOT store plaintext passwords in production.
-- Use the PHP helper script in the project to insert a user with a proper hash:
--   php App/tools/create_test_user.php username password "Display Name"
-- Example:
--   php App/tools/create_test_user.php admin secret "Administrator"

-- If you need to insert via SQL, generate a hash first in PHP and copy it into the INSERT below.
-- Example (generate bcrypt hash in CLI):
--   php -r "echo password_hash('secret', PASSWORD_DEFAULT) . PHP_EOL;"
-- Then paste the generated hash into the INSERT statement below (replace <BCRYPT_HASH>):
-- INSERT INTO `users` (`username`, `password`, `email`) VALUES ('admin', '<BCRYPT_HASH>', 'admin@example.com');

-- Optional: sample insert using a placeholder hash (replace before running):
-- INSERT INTO `users` (`username`, `password`, `email`) VALUES ('testuser', '<PASTE_HASH_HERE>', 'test@example.com');

-- Create table for menu items used by the application
DROP TABLE IF EXISTS `menuitems`;

CREATE TABLE `menuitems` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `text` TEXT DEFAULT NULL,
  `picture` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example insert into menu_items
-- Note: adjust file paths for pictures to match your public/ directory usage (e.g. 'images/filename.jpg')
INSERT INTO `menuitems` (`title`, `text`, `picture`) VALUES
  ('Cappuccino', 'Our signature cappuccino made with freshly roasted beans and perfectly frothed milk.', 'images/cappuccino.jpg'),
  ('Tiramisu', 'Classic Italian tiramisu layered with mascarpone cream and espresso-soaked ladyfingers.', 'images/tiramisu.jpg');

-- Create table for reviews used by the application
DROP TABLE IF EXISTS `reviews`;

CREATE TABLE `reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `text` TEXT DEFAULT NULL,
  `rating` TINYINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reviews_user_id` (`user_id`),
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: `user_id` references `users(id)`. Existing data that used `username` will need migration.

-- New table: drinks (beverages offered by the cafe)
-- Columns:
--  - id: primary key
--  - name: drink name
--  - description: optional textual description
--  - price: decimal price (currency), stored as DECIMAL(7,2)
--  - picture: optional path to an image under public/
--  - created_at: timestamp when the row was created
DROP TABLE IF EXISTS `drinks`;

CREATE TABLE `drinks` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `text` TEXT DEFAULT NULL,
    `picture` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- End of file
