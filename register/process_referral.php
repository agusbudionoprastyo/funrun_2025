<?php
include('../helper/db.php');

function validateReferrerCode($code) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT code, name FROM referrer_codes WHERE code = ? AND is_active = 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return false;
}

function saveReferral($referrerCode, $transactionId, $referredName) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT IGNORE INTO referrals (referrer_code, referred_transaction_id, referred_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $referrerCode, $transactionId, $referredName);
    return $stmt->execute();
}

function updateUserReferral($transactionId, $referrerCode) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE users SET referred_by = ? WHERE transaction_id = ?");
    $stmt->bind_param("ss", $referrerCode, $transactionId);
    return $stmt->execute();
}

function processReferral($referrerCode, $transactionId, $referredName) {
    global $conn;
    
    if (!empty($referrerCode) && !empty($transactionId) && !empty($referredName)) {
        $validReferrer = validateReferrerCode($referrerCode);
        
        if ($validReferrer) {
            $referralSaved = saveReferral($referrerCode, $transactionId, $referredName);
            $userUpdated = updateUserReferral($transactionId, $referrerCode);
            
            if ($referralSaved && $userUpdated) {
                error_log("Referral processed successfully for: $referrerCode -> $transactionId");
                return true;
            } else {
                error_log("Failed to save referral for: $referrerCode -> $transactionId");
                return false;
            }
        } else {
            error_log("Invalid referrer code: $referrerCode");
            return false;
        }
    } else {
        error_log("Missing required parameters for referral");
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $referrerCode = $_POST['referrer_code'] ?? '';
    $transactionId = $_POST['transaction_id'] ?? '';
    $referredName = $_POST['referred_name'] ?? '';
    
    if (!empty($referrerCode) && !empty($transactionId) && !empty($referredName)) {
        $validReferrer = validateReferrerCode($referrerCode);
        
        if ($validReferrer) {
            $referralSaved = saveReferral($referrerCode, $transactionId, $referredName);
            $userUpdated = updateUserReferral($transactionId, $referrerCode);
            
            if ($referralSaved && $userUpdated) {
                echo json_encode(['success' => true, 'message' => 'Referral processed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save referral']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid referrer code']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
