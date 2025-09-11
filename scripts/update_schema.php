<?php
// This script is for updating the database schema.
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "ALTER TABLE users 
            ADD COLUMN profile_image_url VARCHAR(255) DEFAULT 'https://i.pravatar.cc/150' AFTER bio, 
            ADD COLUMN website_url VARCHAR(255) NULL AFTER profile_image_url, 
            ADD COLUMN country VARCHAR(100) NULL AFTER website_url;";

    $dbh->exec($sql);

    echo "Database schema updated successfully. New fields (profile_image_url, website_url, country) were added to the users table.\n";

} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
}

