<?php
include 'helper/db.php';

echo "<h2>Update Existing Referrals with Commission</h2>";

// Step 1: Get all existing referrals without commission
echo "<h3>Step 1: Finding Existing Referrals</h3>";

$result = $conn->query("
    SELECT r.*, rc.commission_amount, rc.commission_rate, t.total_amount
    FROM referrals r
    LEFT JOIN referrer_codes rc ON r.referrer_code = rc.code
    LEFT JOIN transactions t ON r.referred_transaction_id = t.transaction_id
    WHERE r.commission_amount = 0 OR r.commission_amount IS NULL
");

$referralsToUpdate = [];
while ($row = $result->fetch_assoc()) {
    $referralsToUpdate[] = $row;
}

echo "Found " . count($referralsToUpdate) . " referrals to update<br>";

// Step 2: Update referrals with commission data
echo "<h3>Step 2: Updating Referrals with Commission</h3>";

$updatedCount = 0;
foreach ($referralsToUpdate as $referral) {
    $referralId = $referral['id'];
    $commissionAmount = $referral['commission_amount'] ?? 0;
    $transactionId = $referral['referred_transaction_id'];
    $referrerCode = $referral['referrer_code'];
    $baseAmount = $referral['total_amount'] ?? 0;
    $commissionRate = $referral['commission_rate'] ?? 0;
    
    // Update referral with commission amount
    $updateReferral = "UPDATE referrals SET commission_amount = ? WHERE id = ?";
    $stmt = $conn->prepare($updateReferral);
    $stmt->bind_param("di", $commissionAmount, $referralId);
    
    if ($stmt->execute()) {
        // Create commission transaction if it doesn't exist
        $checkCommission = "SELECT id FROM commission_transactions WHERE transaction_id = ?";
        $stmt2 = $conn->prepare($checkCommission);
        $stmt2->bind_param("s", $transactionId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        if ($result2->num_rows == 0) {
            $insertCommission = "INSERT INTO commission_transactions (referrer_code, referral_id, transaction_id, commission_amount, commission_rate, base_amount) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt3 = $conn->prepare($insertCommission);
            $stmt3->bind_param("sisddd", $referrerCode, $referralId, $transactionId, $commissionAmount, $commissionRate, $baseAmount);
            $stmt3->execute();
            $stmt3->close();
        }
        $stmt2->close();
        
        $updatedCount++;
        echo "✓ Updated referral ID: $referralId - Commission: Rp " . number_format($commissionAmount, 0, ',', '.') . "<br>";
    } else {
        echo "✗ Failed to update referral ID: $referralId<br>";
    }
    $stmt->close();
}

// Step 3: Update total commission for referrers
echo "<h3>Step 3: Updating Total Commission for Referrers</h3>";

$updateTotalCommission = "
    UPDATE referrer_codes rc 
    SET total_commission = (
        SELECT COALESCE(SUM(commission_amount), 0) 
        FROM referrals r 
        WHERE r.referrer_code = rc.code
    )
";

if ($conn->query($updateTotalCommission)) {
    echo "✓ Total commission updated for all referrers<br>";
} else {
    echo "✗ Error updating total commission: " . $conn->error . "<br>";
}

// Step 4: Show updated statistics
echo "<h3>Step 4: Updated Statistics</h3>";

$result = $conn->query("
    SELECT 
        rc.code,
        rc.name,
        rc.commission_rate,
        rc.commission_amount,
        rc.total_commission,
        COUNT(r.id) as total_referrals,
        SUM(r.commission_amount) as total_commission_earned
    FROM referrer_codes rc
    LEFT JOIN referrals r ON rc.code = r.referrer_code
    WHERE rc.is_active = 1
    GROUP BY rc.code, rc.name, rc.commission_rate, rc.commission_amount, rc.total_commission
    ORDER BY total_commission_earned DESC
");

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Code</th><th>Name</th><th>Commission Rate</th><th>Commission Amount</th><th>Total Referrals</th><th>Total Commission Earned</th><th>Total Commission Stored</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['code']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['commission_rate']}%</td>";
        echo "<td>Rp " . number_format($row['commission_amount'], 0, ',', '.') . "</td>";
        echo "<td>{$row['total_referrals']}</td>";
        echo "<td>Rp " . number_format($row['total_commission_earned'] ?? 0, 0, ',', '.') . "</td>";
        echo "<td>Rp " . number_format($row['total_commission'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No referrer data found<br>";
}

// Step 5: Show commission transactions
echo "<h3>Step 5: Commission Transactions</h3>";

$result = $conn->query("
    SELECT 
        ct.*,
        rc.name as referrer_name,
        r.referred_name
    FROM commission_transactions ct
    LEFT JOIN referrer_codes rc ON ct.referrer_code = rc.code
    LEFT JOIN referrals r ON ct.referral_id = r.id
    ORDER BY ct.created_at DESC
    LIMIT 10
");

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Referrer</th><th>Referred Name</th><th>Transaction ID</th><th>Commission Amount</th><th>Commission Rate</th><th>Base Amount</th><th>Status</th><th>Created</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['referrer_name']}</td>";
        echo "<td>{$row['referred_name']}</td>";
        echo "<td>{$row['transaction_id']}</td>";
        echo "<td>Rp " . number_format($row['commission_amount'], 0, ',', '.') . "</td>";
        echo "<td>{$row['commission_rate']}%</td>";
        echo "<td>Rp " . number_format($row['base_amount'], 0, ',', '.') . "</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>" . date('d M Y H:i', strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No commission transactions found<br>";
}

$conn->close();
echo "<br><strong>Commission update completed! Updated $updatedCount referrals.</strong>";
echo "<br><br><strong>Next Steps:</strong>";
echo "<br>1. <a href='admin/referral_management.php' target='_blank'>View Updated Referral Management</a>";
echo "<br>2. <a href='admin/referrer_details.php?code=bluehouse' target='_blank'>View Bluehouse Details</a>";
echo "<br>3. <a href='admin/referrer_details.php?code=ag' target='_blank'>View AG Details</a>";
?>
