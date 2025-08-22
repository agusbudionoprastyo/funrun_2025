<?php
include 'helper/db.php';

echo "<h2>Database Setup Check</h2>";

// Check 1: Users table structure
echo "<h3>1. Users Table Structure</h3>";
$result = $conn->query("DESCRIBE users");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: Cannot describe users table<br>";
}

// Check 2: Referrer codes table
echo "<h3>2. Referrer Codes Table</h3>";
$result = $conn->query("SELECT * FROM referrer_codes");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Active</th><th>Created</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['code']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No referrer codes found<br>";
}

// Check 3: Test insert referrer code if none exist
echo "<h3>3. Test Insert Referrer Code</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM referrer_codes");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    echo "No referrer codes found. Inserting test data...<br>";
    
    $insertQuery = "INSERT INTO referrer_codes (code, name, is_active) VALUES 
        ('ag', 'Admin User', 1),
        ('admin', 'Administrator', 1),
        ('dafam', 'Dafam Team', 1),
        ('runner', 'Runner Community', 1)";
    
    if ($conn->query($insertQuery)) {
        echo "✓ Test referrer codes inserted successfully<br>";
    } else {
        echo "✗ Error inserting test referrer codes: " . $conn->error . "<br>";
    }
} else {
    echo "✓ Referrer codes already exist<br>";
}

// Check 4: Test referral processing
echo "<h3>4. Test Referral Processing</h3>";
$testTransactionId = 'TEST-' . time();
$testName = 'Test User';
$testReferrerCode = 'ag';

// Test insert into referrals table
$insertReferral = "INSERT INTO referrals (referrer_code, referred_transaction_id, referred_name, status) 
                   VALUES (?, ?, ?, 'pending')";
$stmt = $conn->prepare($insertReferral);
$stmt->bind_param("sss", $testReferrerCode, $testTransactionId, $testName);

if ($stmt->execute()) {
    echo "✓ Test referral inserted successfully<br>";
    
    // Clean up test data
    $conn->query("DELETE FROM referrals WHERE referred_transaction_id = '$testTransactionId'");
    echo "✓ Test data cleaned up<br>";
} else {
    echo "✗ Error inserting test referral: " . $stmt->error . "<br>";
}
$stmt->close();

// Check 5: Show recent users with referrals
echo "<h3>5. Recent Users with Referrals</h3>";
$result = $conn->query("
    SELECT u.transaction_id, u.name, u.referred_by, u.created_at 
    FROM users u 
    WHERE u.referred_by IS NOT NULL 
    ORDER BY u.created_at DESC 
    LIMIT 10
");

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Transaction ID</th><th>Name</th><th>Referred By</th><th>Created At</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['transaction_id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['referred_by']}</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users with referrals found<br>";
}

$conn->close();
echo "<br><strong>Database check completed!</strong>";
?>
