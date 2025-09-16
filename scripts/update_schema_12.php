<?php
// Manually include only necessary files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

class SchemaUpdater {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function run() {
        try {
            // First, check if the column exists
            $this->db->query("SHOW COLUMNS FROM `users` LIKE 'avatar'");
            $exists = $this->db->rowCount() > 0;

            if (!$exists) {
                $this->db->query("ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT '/uploads/images/avatars/default.png' NOT NULL");
                $this->db->execute();
                echo "Schema updated successfully: 'avatar' column added to 'users' table." . PHP_EOL;
            } else {
                echo "Notice: 'avatar' column already exists in 'users' table. No changes made." . PHP_EOL;
            }
        } catch (PDOException $e) {
            echo "Error updating schema: " . $e->getMessage() . PHP_EOL;
        }
    }
}

$updater = new SchemaUpdater();
$updater->run();