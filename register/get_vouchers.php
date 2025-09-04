<?php
header('Content-Type: application/json');
include('../helper/db.php');

try {
    $query = "SELECT code, discount_amount, max_usage, current_usage, is_active, expires_at 
              FROM vouchers 
              WHERE is_active = 1 
              AND (expires_at IS NULL OR expires_at > NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $vouchers = [];
    while ($row = $result->fetch_assoc()) {
        $vouchers[] = [
            'code' => $row['code'],
            'discount_amount' => (float)$row['discount_amount'],
            'max_usage' => (int)$row['max_usage'],
            'current_usage' => (int)$row['current_usage'],
            'is_active' => (bool)$row['is_active'],
            'expires_at' => $row['expires_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'vouchers' => $vouchers
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
