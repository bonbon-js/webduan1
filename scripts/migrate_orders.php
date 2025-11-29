<?php
require_once 'configs/env.php';

$output = '';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $output .= "Starting migration to fix orders table...\n\n";
    
    // Add columns one by one
    $alterStatements = [
        "ALTER TABLE `orders` ADD COLUMN `user_id` INT(11) NULL AFTER `order_id`",
        "ALTER TABLE `orders` ADD COLUMN `fullname` VARCHAR(255) NOT NULL DEFAULT '' AFTER `user_id`",
        "ALTER TABLE `orders` ADD COLUMN `email` VARCHAR(255) NOT NULL DEFAULT '' AFTER `fullname`",
        "ALTER TABLE `orders` ADD COLUMN `phone` VARCHAR(20) NOT NULL DEFAULT '' AFTER `email`",
        "ALTER TABLE `orders` ADD COLUMN `address` TEXT NOT NULL AFTER `phone`",
        "ALTER TABLE `orders` ADD COLUMN `city` VARCHAR(100) NULL AFTER `address`",
        "ALTER TABLE `orders` ADD COLUMN `district` VARCHAR(100) NULL AFTER `city`",
        "ALTER TABLE `orders` ADD COLUMN `ward` VARCHAR(100) NULL AFTER `district`",
        "ALTER TABLE `orders` ADD COLUMN `note` TEXT NULL AFTER `ward`",
        "ALTER TABLE `orders` ADD COLUMN `order_code` VARCHAR(50) NULL AFTER `note`",
        "ALTER TABLE `orders` ADD COLUMN `discount_amount` DECIMAL(10,2) DEFAULT 0 AFTER `total_amount`",
        "ALTER TABLE `orders` ADD COLUMN `coupon_code` VARCHAR(50) NULL AFTER `coupon_id`",
        "ALTER TABLE `orders` ADD COLUMN `coupon_name` VARCHAR(255) NULL AFTER `coupon_code`",
        "ALTER TABLE `orders` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `coupon_name`",
        "ALTER TABLE `orders` ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`",
        "ALTER TABLE `orders` ADD COLUMN `cancel_reason` TEXT NULL AFTER `updated_at`",
        "ALTER TABLE `orders` ADD INDEX `idx_user_id` (`user_id`)",
        "ALTER TABLE `orders` ADD UNIQUE INDEX `idx_order_code` (`order_code`)",
    ];
    
    foreach ($alterStatements as $i => $statement) {
        $output .= ($i + 1) . ". Executing: " . substr($statement, 0, 80) . "...\n";
        
        try {
            $pdo->exec($statement);
            $output .= "   ✓ Success\n\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false || 
                strpos($e->getMessage(), 'Duplicate key name') !== false) {
                $output .= "   ⚠ Already exists, skipping\n\n";
            } else {
                $output .= "   ✗ Error: " . $e->getMessage() . "\n\n";
            }
        }
    }
    
    $output .= "\n=== Migration completed! ===\n\n";
    
    // Verify the changes
    $output .= "=== New table structure ===\n\n";
    $stmt = $pdo->query('DESCRIBE orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= sprintf("%-25s %-25s %-10s\n", $row['Field'], $row['Type'], $row['Key']);
    }
    
} catch (PDOException $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

echo $output;
file_put_contents('scripts/migration_result.txt', $output);
echo "\n\nFull output saved to scripts/migration_result.txt\n";
