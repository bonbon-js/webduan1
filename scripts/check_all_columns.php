<?php
require_once 'configs/env.php';

$output = '';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get ALL columns
    $output .= "=== ALL COLUMNS IN ORDERS TABLE ===\n\n";
    $stmt = $pdo->query('DESCRIBE orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= sprintf("%-25s %-25s %-10s %-10s %-20s %-20s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Null'], 
            $row['Key'], 
            $row['Default'] ?? 'NULL',
            $row['Extra']
        );
    }
    
} catch (PDOException $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

echo $output;
file_put_contents('scripts/orders_all_columns.txt', $output);
echo "\n\nOutput saved to scripts/orders_all_columns.txt\n";
