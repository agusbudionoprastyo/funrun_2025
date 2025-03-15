<?php
include '../helper/db.php';

// SQL query to fetch users data
$sql = "SELECT 
    t.transaction_id, 
    t.total_amount, 
    t.payment_method, 
    t.payment_prooft, 
    t.transaction_date, 
    t.status,
    GROUP_CONCAT(u.name) AS name,
    GROUP_CONCAT(u.mantan) AS mantan,
    GROUP_CONCAT(u.email) AS email,
    GROUP_CONCAT(u.phone) AS phone,
    GROUP_CONCAT(u.username) AS username,
    GROUP_CONCAT(u.password) AS password
FROM 
    transactions t
JOIN 
    users u ON u.transaction_id = t.transaction_id
GROUP BY 
    t.transaction_id, t.total_amount, t.payment_method, t.payment_prooft, t.transaction_date, t.status";

$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    // Loop through all rows and push each to the array
    while($row = $result->fetch_assoc()) {
        // Dynamically set the badge class based on the transaction status
        switch ($row['status']) {
            case 'paid':
                $row['badge_class'] = 'bg-gradient-info';
                break;
            case 'pending':
                $row['badge_class'] = 'bg-gradient-warning';
                break;
            case 'verified':
                $row['badge_class'] = 'bg-gradient-success';
                break;
            default:
                $row['badge_class'] = 'bg-gradient-secondary';  // Fallback for any other status
                break;
        }
        
        $users[] = $row;
    }
} else {
    $users = [];
}

// Set the header to JSON and output the data
header('Content-Type: application/json');
echo json_encode($users);

$conn->close();
?>