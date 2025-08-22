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
    
    // Get commission rate and amount for this referrer
    $stmt = $conn->prepare("SELECT commission_rate, commission_amount FROM referrer_codes WHERE code = ? AND is_active = 1");
    $stmt->bind_param("s", $referrerCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $commissionRate = 0.00;
    $commissionAmount = 0.00;
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $commissionRate = $row['commission_rate'];
        $commissionAmount = $row['commission_amount'];
    }
    $stmt->close();
    
    // Insert referral with commission
    $stmt = $conn->prepare("INSERT IGNORE INTO referrals (referrer_code, referred_transaction_id, referred_name, commission_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $referrerCode, $transactionId, $referredName, $commissionAmount);
    $success = $stmt->execute();
    $stmt->close();
    
    if ($success) {
        // Get the referral ID
        $stmt = $conn->prepare("SELECT id FROM referrals WHERE referrer_code = ? AND referred_transaction_id = ?");
        $stmt->bind_param("ss", $referrerCode, $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $referralId = $row['id'];
            
            // Get transaction amount
            $stmt = $conn->prepare("SELECT total_amount FROM transactions WHERE transaction_id = ?");
            $stmt->bind_param("s", $transactionId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $baseAmount = $row['total_amount'];
                
                // Create commission transaction
                $stmt = $conn->prepare("INSERT INTO commission_transactions (referrer_code, referral_id, transaction_id, commission_amount, commission_rate, base_amount) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sisddd", $referrerCode, $referralId, $transactionId, $commissionAmount, $commissionRate, $baseAmount);
                $stmt->execute();
                $stmt->close();
                
                // Update total commission for referrer
                $stmt = $conn->prepare("UPDATE referrer_codes SET total_commission = total_commission + ? WHERE code = ?");
                $stmt->bind_param("ds", $commissionAmount, $referrerCode);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    return $success;
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

// Only process POST requests when called directly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['SCRIPT_NAME']) === 'process_referral.php') {
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
}
?>
