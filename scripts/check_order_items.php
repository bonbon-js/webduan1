<?php
require_once 'configs/env.php';

$output = '';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check order_items table
    $output .= "=== ORDER_ITEMS TABLE STRUCTURE ===\n\n";
    $stmt = $pdo->query('DESCRIBE order_items');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= sprintf("%-25s %-25s %-10s %-10s %-20s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'],
            $row['Key'], 
            $row['Extra']
        );
    }
    
    $output .= "\n\n=== AUTO_INCREMENT VALUE ===\n\n";
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'order_items'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    $output .= "Auto_increment: " . ($status['Auto_increment'] ?? 'N/A') . "\n";
    
    $output .= "\n\n=== PRIMARY KEY ===\n\n";
    $stmt = $pdo->query("SHOW KEYS FROM order_items WHERE Key_name = 'PRIMARY'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "Primary Key Column: " . $row['Column_name'] . "\n";
    }
    
} catch (PDOException $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

echo $output;
file_put_contents('scripts/order_items_info.txt', $output);
echo "\n\nOutput saved to scripts/order_items_info.txt\n";
