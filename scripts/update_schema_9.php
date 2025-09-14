<?php
// This script is for updating the database schema.
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `withdrawals` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT(11) NOT NULL,
        `amount` DECIMAL(10, 2) NOT NULL,
        `paypal_email` VARCHAR(255) NOT NULL,
        `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        `notes` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $dbh->exec($sql);

    echo "Database schema updated successfully. The 'withdrawals' table has been created.\n";

} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
}