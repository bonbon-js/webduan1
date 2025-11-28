<?php
require_once 'configs/env.php';

$output = '';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get columns
    $output .= "=== COLUMNS IN ORDERS TABLE ===\n\n";
    $stmt = $pdo->query('SHOW COLUMNS FROM orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= sprintf("Field: %-20s Type: %-20s Key: %-5s Extra: %-20s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Key'], 
            $row['Extra']
        );
    }
    
    $output .= "\n\n=== AUTO_INCREMENT VALUE ===\n\n";
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'orders'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    $output .= "Auto_increment: " . ($status['Auto_increment'] ?? 'N/A') . "\n";
    
    $output .= "\n\n=== SAMPLE QUERY TO CHECK PRIMARY KEY ===\n\n";
    $stmt = $pdo->query("SHOW KEYS FROM orders WHERE Key_name = 'PRIMARY'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= "Primary Key Column: " . $row['Column_name'] . "\n";
    }
    
} catch (PDOException $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

echo $output;
file_put_contents('scripts/orders_table_info.txt', $output);
echo "\n\nOutput saved to scripts/orders_table_info.txt\n";
