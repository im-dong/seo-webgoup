<?php
// This script is for updating the database schema.
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS `wallet_transactions` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT(11) NOT NULL,
        `order_id` INT(11) NULL,
        `type` ENUM('credit', 'debit', 'withdrawal_request', 'withdrawal_complete', 'fee') NOT NULL,
        `amount` DECIMAL(10, 2) NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $dbh->exec($sql);

    echo "Database schema updated successfully. The 'wallet_transactions' table has been created.\n";

} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
}

