<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

$db = new Database();

$sql = "ALTER TABLE users ADD COLUMN contact_method VARCHAR(255) NULL AFTER country";

$db->query($sql);

if ($db->execute()) {
    echo "Table 'users' altered successfully. Added 'contact_method' column.\n";
} else {
    echo "Error altering table 'users'.\n";
}