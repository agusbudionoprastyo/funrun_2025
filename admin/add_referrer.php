<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include '../helper/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    
    if (empty($code) || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Code and name are required']);
        exit();
    }
    
    $code = strtolower($code);
    
    $stmt = $conn->prepare("SELECT id FROM referrer_codes WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Referrer code already exists']);
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO referrer_codes (code, name) VALUES (?, ?)");
    $stmt->bind_param("ss", $code, $name);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Referrer added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add referrer']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
