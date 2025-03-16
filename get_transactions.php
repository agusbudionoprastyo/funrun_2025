<?php
include 'helper/db.php';

// SQL query to fetch users data
$sql = "WITH RankedUsers AS (
    SELECT 
        t.transaction_id, 
        t.total_amount, 
        t.payment_method, 
        t.payment_prooft, 
        t.transaction_date, 
        t.status,
        u.name,
        u.mantan,
        u.email,
        u.phone,
        u.username,
        u.password,
        ROW_NUMBER() OVER (PARTITION BY t.transaction_id ORDER BY u.name) AS user_rank
    FROM 
        transactions t
    JOIN 
        users u ON u.transaction_id = t.transaction_id
)
SELECT 
    transaction_id,
    total_amount,
    payment_method,
    payment_prooft,
    transaction_date,
    status,
    MAX(CASE WHEN user_rank = 1 THEN name END) AS name_1,
    MAX(CASE WHEN user_rank = 1 THEN mantan END) AS mantan_1,
    MAX(CASE WHEN user_rank = 1 THEN email END) AS email_1,
    MAX(CASE WHEN user_rank = 1 THEN phone END) AS phone_1,
    MAX(CASE WHEN user_rank = 1 THEN username END) AS username_1,
    MAX(CASE WHEN user_rank = 1 THEN password END) AS password_1,
    MAX(CASE WHEN user_rank = 2 THEN name END) AS name_2,
    MAX(CASE WHEN user_rank = 2 THEN mantan END) AS mantan_2,
    MAX(CASE WHEN user_rank = 2 THEN email END) AS email_2,
    MAX(CASE WHEN user_rank = 2 THEN phone END) AS phone_2,
    MAX(CASE WHEN user_rank = 2 THEN username END) AS username_2,
    MAX(CASE WHEN user_rank = 2 THEN password END) AS password_2
FROM 
    RankedUsers
GROUP BY 
    transaction_id, 
    total_amount, 
    payment_method, 
    payment_prooft, 
    transaction_date, 
    status";

// Execute query
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    // Loop through all rows and push each to the array
    while($row = $result->fetch_assoc()) {
        // Dynamically set the badge class based on the transaction status
        switch ($row['status']) {
            case 'paid':
                $row['badge_class'] = 'paid';
                $row['status_text'] = 'Paid';
                break;
            case 'pending':
                $row['badge_class'] = 'waiting';
                $row['status_text'] = 'Waiting';
                break;
            case 'verified':
                $row['badge_class'] = 'active';
                $row['status_text'] = 'Verified';
                break;
            default:
                $row['badge_class'] = 'bg-secondary';  // Fallback for any other status
                $row['status_text'] = 'Inactive';
                break;
        }
        
        // Add row data to the users array
        $users[] = $row;
    }
} else {
    $users = [];
}

$conn->close();

// Set the header to JSON and output the data
header('Content-Type: application/json');
echo json_encode($users);
?>