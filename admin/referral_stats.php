<?php
include '../helper/db.php';

$sql = "SELECT 
    rc.code,
    rc.name as referrer_name,
    rc.commission_rate,
    rc.commission_amount,
    rc.total_commission,
    rc.referral_link,
    COUNT(r.id) as total_referrals,
    COUNT(CASE WHEN r.status = 'completed' THEN 1 END) as completed_referrals,
    COUNT(CASE WHEN r.status = 'pending' THEN 1 END) as pending_referrals,
    SUM(r.commission_amount) as total_commission_earned,
    COUNT(CASE WHEN r.commission_paid = 1 THEN 1 END) as paid_commissions,
    COUNT(CASE WHEN r.commission_paid = 0 THEN 1 END) as unpaid_commissions
FROM referrer_codes rc
LEFT JOIN referrals r ON rc.code = r.referrer_code
WHERE rc.is_active = 1
GROUP BY rc.code, rc.name, rc.commission_rate, rc.commission_amount, rc.total_commission, rc.referral_link
ORDER BY total_referrals DESC";

$result = $conn->query($sql);

$referralStats = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $referralStats[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($referralStats);
?>
