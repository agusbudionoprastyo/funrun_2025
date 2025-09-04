<?php
include('db.php');

function validateVoucherFromDatabase($voucherCode) {
    global $conn;
    
    if (empty($voucherCode)) {
        return ['valid' => false, 'message' => 'Voucher code is required'];
    }
    
    $voucherCode = strtoupper(trim($voucherCode));
    
    $query = "SELECT code, discount_amount, max_usage, current_usage, is_active, expires_at 
              FROM vouchers 
              WHERE code = ? 
              AND is_active = 1 
              AND (expires_at IS NULL OR expires_at > NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $voucherCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return ['valid' => false, 'message' => 'Voucher code tidak valid atau sudah expired'];
    }
    
    $voucher = $result->fetch_assoc();
    $stmt->close();
    
    if ($voucher['current_usage'] >= $voucher['max_usage']) {
        return ['valid' => false, 'message' => 'Voucher sudah mencapai batas penggunaan'];
    }
    
    return [
        'valid' => true,
        'code' => $voucher['code'],
        'discount_amount' => (float)$voucher['discount_amount'],
        'max_usage' => (int)$voucher['max_usage'],
        'current_usage' => (int)$voucher['current_usage']
    ];
}

function incrementVoucherUsage($voucherCode) {
    global $conn;
    
    $voucherCode = strtoupper(trim($voucherCode));
    
    $query = "UPDATE vouchers 
              SET current_usage = current_usage + 1 
              WHERE code = ? 
              AND is_active = 1 
              AND (expires_at IS NULL OR expires_at > NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $voucherCode);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

function getAllActiveVouchers() {
    global $conn;
    
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
    
    $stmt->close();
    return $vouchers;
}
?>
