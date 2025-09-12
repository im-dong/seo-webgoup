<?php
// Create industries table and add industry_id to services
require_once dirname(__DIR__) . '/config/config.php';

try {
    $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create industries table if it doesn't exist
    $sql_create_table = "CREATE TABLE IF NOT EXISTS `industries` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $dbh->exec($sql_create_table);
    echo "Table 'industries' is ready.\n";

    // 2. Populate industries table if it's empty
    $stmt = $dbh->query("SELECT COUNT(*) FROM `industries`");
    if ($stmt->fetchColumn() == 0) {
        $industries = [
            'Technology & IT', 'Health & Wellness', 'Finance & Insurance', 'Real Estate', 'Education', 
            'Retail & E-commerce', 'Food & Beverage', 'Travel & Hospitality', 'Entertainment & Media', 'Automotive',
            'Home & Garden', 'Fashion & Apparel', 'Business Services', 'Legal', 'Sports & Recreation',
            'Pets & Animals', 'Arts & Crafts', 'Non-profit', 'Manufacturing', 'Construction'
        ];
        
        $sql_insert = "INSERT INTO `industries` (name) VALUES (:name)";
        $stmt_insert = $dbh->prepare($sql_insert);

        foreach ($industries as $industry) {
            $stmt_insert->execute(['name' => $industry]);
        }
        echo "Populated 'industries' table with initial data.\n";
    } else {
        echo "Table 'industries' already contains data. No action taken.\n";
    }

    // 3. Add industry_id column to services table if it doesn't exist
    $stmt_check_col = $dbh->query("SHOW COLUMNS FROM `services` LIKE 'industry_id'");
    if ($stmt_check_col->rowCount() == 0) {
        $sql_add_col = "ALTER TABLE `services` ADD COLUMN `industry_id` INT(11) UNSIGNED NULL AFTER `service_category`, ADD FOREIGN KEY (`industry_id`) REFERENCES `industries`(`id`) ON DELETE SET NULL";
        $dbh->exec($sql_add_col);
        echo "Column 'industry_id' added to 'services' table with foreign key constraint.\n";
    } else {
        echo "Column 'industry_id' already exists in 'services' table. No action taken.\n";
    }

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
