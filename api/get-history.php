<?php
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM test_history ORDER BY created_at DESC LIMIT 50");
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $history]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
