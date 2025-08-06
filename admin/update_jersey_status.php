<?php
include '../helper/db.php';

// Set header to JSON
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit();
}

$transaction_id = $input['transaction_id'] ?? null;
$field = $input['field'] ?? null;
$value = $input['value'] ?? null;

if (!$transaction_id || !$field || $value === null) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

// Validate field
$allowed_fields = ['status', 'jersey_color_1', 'jersey_color_2', 'phone_1'];
if (!in_array($field, $allowed_fields)) {
    echo json_encode(['success' => false, 'message' => 'Invalid field']);
    exit();
}

try {
    if ($field === 'status') {
        // Update transaction status
        $sql = "UPDATE transactions SET status = ? WHERE transaction_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $value, $transaction_id);
    } elseif ($field === 'phone_1') {
        // Update phone number for the first user
        $get_user_sql = "SELECT u.id FROM users u 
                        WHERE u.transaction_id = ? 
                        ORDER BY u.name 
                        LIMIT 1";
        $get_user_stmt = $conn->prepare($get_user_sql);
        $get_user_stmt->bind_param("s", $transaction_id);
        $get_user_stmt->execute();
        $user_result = $get_user_stmt->get_result();
        
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $user_id = $user_row['id'];
            
            // Update phone number
            $sql = "UPDATE users SET phone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $value, $user_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found for transaction: ' . $transaction_id]);
            exit();
        }
    } else {
        // Update jersey color for specific user
        $user_rank = ($field === 'jersey_color_1') ? 1 : 2;
        
        // Get the user's ID first using a more reliable query
        $get_user_sql = "SELECT u.id FROM users u 
                        WHERE u.transaction_id = ? 
                        ORDER BY u.name 
                        LIMIT 1 OFFSET ?";
        $get_user_stmt = $conn->prepare($get_user_sql);
        $offset = $user_rank - 1;
        $get_user_stmt->bind_param("si", $transaction_id, $offset);
        $get_user_stmt->execute();
        $user_result = $get_user_stmt->get_result();
        
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $user_id = $user_row['id'];
            
            // Update jersey color
            $sql = "UPDATE users SET jersey_color = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $value, $user_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found for transaction: ' . $transaction_id]);
            exit();
        }
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update: ' . $stmt->error]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?> 