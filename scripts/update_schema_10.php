<?php
// Add role column to users table
require_once dirname(__DIR__) . '/app/bootstrap.php';

class UpdateSchema10 {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function run(){
        try {
            $this->db->query("ALTER TABLE `users` ADD `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user' AFTER `status`");
            $this->db->execute();
            echo "Database schema updated successfully. Added 'role' to 'users' table.\n";
        } catch (PDOException $e) {
            die("Database update failed: " . $e->getMessage() . "\n");
        }
    }
}

$update = new UpdateSchema10();
$update->run();

