<?php
include '../helper/db.php';

$sql = "SELECT 
    rc.code,
    rc.name as referrer_name,
    COUNT(r.id) as total_referrals,
    COUNT(CASE WHEN r.status = 'completed' THEN 1 END) as completed_referrals,
    COUNT(CASE WHEN r.status = 'pending' THEN 1 END) as pending_referrals
FROM referrer_codes rc
LEFT JOIN referrals r ON rc.code = r.referrer_code
WHERE rc.is_active = 1
GROUP BY rc.code, rc.name
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
