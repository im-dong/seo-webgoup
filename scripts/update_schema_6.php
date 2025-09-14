<?php
// This script is for updating the database schema.
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "ALTER TABLE services MODIFY COLUMN description MEDIUMTEXT NOT NULL;";

    $dbh->exec($sql);

    echo "Database schema updated successfully. The 'description' column in the 'services' table has been changed to MEDIUMTEXT.\n";

} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
}

