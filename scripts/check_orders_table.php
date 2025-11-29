<?php
require_once 'configs/env.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ORDERS TABLE STRUCTURE ===\n\n";
    
    // Get table structure
    $stmt = $pdo->query('SHOW CREATE TABLE orders');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $result['Create Table'] . "\n\n";
    
    // Get columns
    echo "=== COLUMNS ===\n";
    $stmt = $pdo->query('SHOW COLUMNS FROM orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Field: {$row['Field']}, Type: {$row['Type']}, Key: {$row['Key']}, Extra: {$row['Extra']}\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
