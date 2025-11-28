<?php
require_once 'configs/env.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get columns
    echo "=== COLUMNS IN ORDERS TABLE ===\n\n";
    $stmt = $pdo->query('SHOW COLUMNS FROM orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        printf("%-20s %-20s %-10s %-20s\n", 
            $row['Field'], 
            $row['Type'], 
            $row['Key'], 
            $row['Extra']
        );
    }
    
    echo "\n\n=== AUTO_INCREMENT VALUE ===\n\n";
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'orders'");
    $status = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Auto_increment: " . ($status['Auto_increment'] ?? 'N/A') . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
