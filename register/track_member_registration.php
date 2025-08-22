<?php
header('Content-Type: application/json');
include('../helper/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $memberCode = $data['member_code'] ?? '';
    $transactionId = $data['transaction_id'] ?? '';
    $registrationType = $data['registration_type'] ?? '';
    $userCount = $data['user_count'] ?? 1;
    
    if (empty($memberCode) || empty($transactionId)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
        exit;
    }
    
    try {
        $conn->begin_transaction();
        
        $query = "INSERT INTO member_registrations (member_code, transaction_id, registration_type, user_count, registered_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $memberCode, $transactionId, $registrationType, $userCount);
        $stmt->execute();
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Member registration tracked successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $memberCode = $_GET['member_code'] ?? '';
    
    if (empty($memberCode)) {
        echo json_encode(['status' => 'error', 'message' => 'Member code required']);
        exit;
    }
    
    try {
        $query = "SELECT member_code, 
                         COUNT(id) as total_registrations,
                         SUM(user_count) as total_users,
                         MIN(registered_at) as first_registration,
                         MAX(registered_at) as last_registration
                  FROM member_registrations 
                  WHERE member_code = ? 
                  GROUP BY member_code";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $memberCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Member code not found']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
