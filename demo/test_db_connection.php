<?php
require_once __DIR__ . '/../src/core/DB.php';

/**
 * 测试数据库连接功能
 */
try {
    $db = new DB();
    $result = $db->query("SELECT 1");
    echo "Database connection test passed!\n";
} catch (Exception $e) {
    echo "Database connection test failed: " . $e->getMessage() . "\n";
}