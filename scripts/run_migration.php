<?php
require_once 'configs/env.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting migration to fix orders table...\n\n";
    
    // Read the SQL file
    $sql = file_get_contents('scripts/fix_orders_table.sql');
    
    // Remove comments and split by semicolon
    $statements = array_filter(
        array_map('trim', 
            preg_split('/;(\s*\n|$)/', $sql)
        ),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        echo "Executing: " . substr($statement, 0, 100) . "...\n";
        
        try {
            $pdo->exec($statement);
            echo "✓ Success\n\n";
        } catch (PDOException $e) {
            // Check if error is about duplicate column (which means it already exists)
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠ Column already exists, skipping\n\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n\n";
            }
        }
    }
    
    echo "\n=== Migration completed! ===\n\n";
    
    // Verify the changes
    echo "=== Verifying new table structure ===\n\n";
    $stmt = $pdo->query('DESCRIBE orders');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        printf("%-25s %-25s\n", $row['Field'], $row['Type']);
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
