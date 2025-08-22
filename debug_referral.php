<?php
include 'helper/db.php';

echo "<h2>Debug Referral System</h2>";

// Test 1: Check if URL parameter is being captured
echo "<h3>1. URL Parameter Test</h3>";
$member = $_GET['member'] ?? 'NOT_FOUND';
echo "Member parameter: " . $member . "<br>";

// Test 2: Check if referrer code exists in database
echo "<h3>2. Referrer Code Validation</h3>";
if ($member !== 'NOT_FOUND') {
    $stmt = $conn->prepare("SELECT code, name FROM referrer_codes WHERE code = ? AND is_active = 1");
    $stmt->bind_param("s", $member);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "✓ Referrer code '$member' is valid - Name: " . $row['name'] . "<br>";
    } else {
        echo "✗ Referrer code '$member' is NOT valid or not active<br>";
    }
    $stmt->close();
} else {
    echo "✗ No member parameter found in URL<br>";
}

// Test 3: Check database tables
echo "<h3>3. Database Tables Check</h3>";
$tables = ['users', 'referrals', 'referrer_codes'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' does not exist<br>";
    }
}

// Test 4: Check users table structure
echo "<h3>4. Users Table Structure</h3>";
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'referred_by'");
if ($result->num_rows > 0) {
    echo "✓ 'referred_by' column exists in users table<br>";
} else {
    echo "✗ 'referred_by' column does NOT exist in users table<br>";
}

// Test 5: Check referrer codes data
echo "<h3>5. Referrer Codes Data</h3>";
$result = $conn->query("SELECT * FROM referrer_codes WHERE is_active = 1");
if ($result->num_rows > 0) {
    echo "✓ Found " . $result->num_rows . " active referrer codes:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['code']}: {$row['name']}<br>";
    }
} else {
    echo "✗ No active referrer codes found<br>";
}

// Test 6: Check recent registrations
echo "<h3>6. Recent Registrations (Last 5)</h3>";
$result = $conn->query("SELECT transaction_id, name, referred_by, created_at FROM users ORDER BY created_at DESC LIMIT 5");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Transaction ID</th><th>Name</th><th>Referred By</th><th>Created At</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['transaction_id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>" . ($row['referred_by'] ?: 'NULL') . "</td>";
        echo "<td>{$row['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No registrations found<br>";
}

// Test 7: Check referrals table
echo "<h3>7. Referrals Table Data</h3>";
$result = $conn->query("SELECT * FROM referrals ORDER BY referral_date DESC LIMIT 5");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Referrer Code</th><th>Transaction ID</th><th>Referred Name</th><th>Status</th><th>Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['referrer_code']}</td>";
        echo "<td>{$row['referred_transaction_id']}</td>";
        echo "<td>{$row['referred_name']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['referral_date']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No referrals found<br>";
}

// Test 8: Simulate form data
echo "<h3>8. Form Data Simulation</h3>";
echo "To test the referral system, you need to:<br>";
echo "1. Visit: <a href='register/?member=ag' target='_blank'>register/?member=ag</a><br>";
echo "2. Fill out the registration form<br>";
echo "3. Submit the form<br>";
echo "4. Check if the referral is captured<br>";

$conn->close();
echo "<br><strong>Debug completed!</strong>";
?>
