<?php
// Add service_category to services table
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the column already exists
    $stmt = $dbh->query("SHOW COLUMNS FROM `services` LIKE 'service_category'");
    $exists = $stmt->rowCount() > 0;

    if (!$exists) {
        $sql = "ALTER TABLE services ADD COLUMN service_category ENUM('guest_post', 'backlink') NOT NULL DEFAULT 'backlink' AFTER `duration`";
        $dbh->exec($sql);
        echo "Table 'services' updated successfully. Column 'service_category' added.\n";
    } else {
        echo "Column 'service_category' already exists in 'services' table. No action taken.\n";
    }

} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}

