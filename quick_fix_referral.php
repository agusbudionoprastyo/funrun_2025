<?php
include 'helper/db.php';

echo "<h2>Quick Fix for Referral System</h2>";

// Step 1: Ensure referrer_codes table exists and has data
echo "<h3>Step 1: Setup Referrer Codes</h3>";

// Create table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS referrer_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($createTable)) {
    echo "✓ referrer_codes table created/verified<br>";
} else {
    echo "✗ Error creating referrer_codes table: " . $conn->error . "<br>";
}

// Insert default referrer codes
$insertCodes = "INSERT IGNORE INTO referrer_codes (code, name, is_active) VALUES 
    ('ag', 'Admin User', 1),
    ('admin', 'Administrator', 1),
    ('dafam', 'Dafam Team', 1),
    ('runner', 'Runner Community', 1)";

if ($conn->query($insertCodes)) {
    echo "✓ Default referrer codes inserted<br>";
} else {
    echo "✗ Error inserting referrer codes: " . $conn->error . "<br>";
}

// Step 2: Ensure referrals table exists
echo "<h3>Step 2: Setup Referrals Table</h3>";

$createReferralsTable = "CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_code VARCHAR(50) NOT NULL,
    referred_transaction_id VARCHAR(100) NOT NULL,
    referred_name VARCHAR(255) NOT NULL,
    referral_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    UNIQUE KEY unique_referral (referrer_code, referred_transaction_id)
)";

if ($conn->query($createReferralsTable)) {
    echo "✓ referrals table created/verified<br>";
} else {
    echo "✗ Error creating referrals table: " . $conn->error . "<br>";
}

// Step 3: Ensure users table has referred_by column
echo "<h3>Step 3: Setup Users Table</h3>";

$addColumn = "ALTER TABLE users ADD COLUMN IF NOT EXISTS referred_by VARCHAR(50) NULL";

if ($conn->query($addColumn)) {
    echo "✓ referred_by column added/verified to users table<br>";
} else {
    echo "✗ Error adding referred_by column: " . $conn->error . "<br>";
}

// Step 4: Test the complete flow
echo "<h3>Step 4: Test Complete Flow</h3>";

$testTransactionId = 'QUICK-TEST-' . time();
$testName = 'Quick Test User';
$testReferrerCode = 'ag';

// Test user insertion
$insertUser = "INSERT INTO users (transaction_id, name, mantan, size, phone, voucher_code, username, password, jersey_color, referred_by) 
               VALUES (?, ?, 'Test Mantan', 'L', '08123456789', '', 'test123', '123456', 'darkblue', ?)";
$stmt = $conn->prepare($insertUser);
$stmt->bind_param("sss", $testTransactionId, $testName, $testReferrerCode);

if ($stmt->execute()) {
    echo "✓ Test user inserted successfully<br>";
} else {
    echo "✗ Error inserting test user: " . $stmt->error . "<br>";
}
$stmt->close();

// Test referral insertion
$insertReferral = "INSERT INTO referrals (referrer_code, referred_transaction_id, referred_name, status) 
                   VALUES (?, ?, ?, 'pending')";
$stmt = $conn->prepare($insertReferral);
$stmt->bind_param("sss", $testReferrerCode, $testTransactionId, $testName);

if ($stmt->execute()) {
    echo "✓ Test referral inserted successfully<br>";
} else {
    echo "✗ Error inserting test referral: " . $stmt->error . "<br>";
}
$stmt->close();

// Step 5: Verify data
echo "<h3>Step 5: Verify Data</h3>";

$result = $conn->query("SELECT transaction_id, name, referred_by FROM users WHERE transaction_id = '$testTransactionId'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ User verification:<br>";
    echo "- Transaction ID: " . $row['transaction_id'] . "<br>";
    echo "- Name: " . $row['name'] . "<br>";
    echo "- Referred By: " . ($row['referred_by'] ?: 'NULL') . "<br>";
} else {
    echo "✗ User not found<br>";
}

$result = $conn->query("SELECT * FROM referrals WHERE referred_transaction_id = '$testTransactionId'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Referral verification:<br>";
    echo "- Referrer Code: " . $row['referrer_code'] . "<br>";
    echo "- Referred Name: " . $row['referred_name'] . "<br>";
    echo "- Status: " . $row['status'] . "<br>";
} else {
    echo "✗ Referral not found<br>";
}

// Step 6: Clean up
echo "<h3>Step 6: Clean Up</h3>";
$conn->query("DELETE FROM referrals WHERE referred_transaction_id = '$testTransactionId'");
$conn->query("DELETE FROM users WHERE transaction_id = '$testTransactionId'");
echo "✓ Test data cleaned up<br>";

// Step 7: Show current referrer codes
echo "<h3>Step 7: Current Referrer Codes</h3>";
$result = $conn->query("SELECT * FROM referrer_codes WHERE is_active = 1");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Code</th><th>Name</th><th>Active</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['code']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No active referrer codes found<br>";
}

$conn->close();
echo "<br><strong>Quick fix completed! Now try the referral system again.</strong>";
echo "<br><br><strong>Test Links:</strong>";
echo "<br><a href='register/?member=ag' target='_blank'>register/?member=ag</a>";
echo "<br><a href='register/?member=admin' target='_blank'>register/?member=admin</a>";
echo "<br><a href='register/?member=dafam' target='_blank'>register/?member=dafam</a>";
?>
