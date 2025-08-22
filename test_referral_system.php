<?php
include 'helper/db.php';

echo "<h2>Testing Referral System</h2>";

// Test 1: Check if tables exist
echo "<h3>1. Checking Database Tables</h3>";
$tables = ['users', 'referrals', 'referrer_codes'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' does not exist<br>";
    }
}

// Test 2: Check if referred_by column exists in users table
echo "<h3>2. Checking Users Table Structure</h3>";
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'referred_by'");
if ($result->num_rows > 0) {
    echo "✓ 'referred_by' column exists in users table<br>";
} else {
    echo "✗ 'referred_by' column does not exist in users table<br>";
}

// Test 3: Check referrer codes
echo "<h3>3. Checking Referrer Codes</h3>";
$result = $conn->query("SELECT * FROM referrer_codes WHERE is_active = 1");
if ($result->num_rows > 0) {
    echo "✓ Found " . $result->num_rows . " active referrer codes:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['code']}: {$row['name']}<br>";
    }
} else {
    echo "✗ No active referrer codes found<br>";
}

// Test 4: Check existing referrals
echo "<h3>4. Checking Existing Referrals</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM referrals");
$row = $result->fetch_assoc();
echo "✓ Found " . $row['count'] . " referrals in database<br>";

// Test 5: Check users with referrals
echo "<h3>5. Checking Users with Referrals</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE referred_by IS NOT NULL");
$row = $result->fetch_assoc();
echo "✓ Found " . $row['count'] . " users with referrals<br>";

// Test 6: Sample referral data
echo "<h3>6. Sample Referral Data</h3>";
$result = $conn->query("
    SELECT u.name, u.referred_by, r.referral_date, r.status 
    FROM users u 
    LEFT JOIN referrals r ON u.transaction_id = r.referred_transaction_id 
    WHERE u.referred_by IS NOT NULL 
    LIMIT 5
");

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Name</th><th>Referred By</th><th>Referral Date</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['referred_by']}</td>";
        echo "<td>{$row['referral_date']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No referral data found<br>";
}

$conn->close();
echo "<br><strong>Test completed!</strong>";
?>
