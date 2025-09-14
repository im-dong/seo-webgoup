<?php
// This script is for updating the database schema.
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "ALTER TABLE messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER message_text;";

    $dbh->exec($sql);

    echo "Database schema updated successfully. The 'is_read' column has been added to the 'messages' table.\n";

} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
}

