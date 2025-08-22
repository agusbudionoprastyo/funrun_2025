<?php
include 'helper/db.php';

echo "<h2>Test Complete Referral Flow</h2>";

// Test 1: Check if referrer code exists
echo "<h3>1. Check Referrer Code 'ag'</h3>";
$stmt = $conn->prepare("SELECT code, name FROM referrer_codes WHERE code = ? AND is_active = 1");
$stmt->bind_param("s", 'ag');
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Referrer code 'ag' exists - Name: " . $row['name'] . "<br>";
} else {
    echo "✗ Referrer code 'ag' does not exist or is not active<br>";
    
    // Insert it if it doesn't exist
    echo "Inserting referrer code 'ag'...<br>";
    $insertStmt = $conn->prepare("INSERT INTO referrer_codes (code, name, is_active) VALUES (?, ?, 1)");
    $insertStmt->bind_param("ss", 'ag', 'Admin User');
    if ($insertStmt->execute()) {
        echo "✓ Referrer code 'ag' inserted successfully<br>";
    } else {
        echo "✗ Failed to insert referrer code 'ag'<br>";
    }
    $insertStmt->close();
}
$stmt->close();

// Test 2: Simulate form data
echo "<h3>2. Simulate Form Data</h3>";
$testData = [
    'transactionid' => 'TEST-' . time(),
    'registrationType' => 'single',
    'username' => 'Test User',
    'mantan' => 'Test Mantan',
    'phone' => '08123456789',
    'voucherCode' => '',
    'size' => 'L',
    'jerseyColor' => 'darkblue',
    'referrer_code' => 'ag'
];

echo "Test data:<br>";
foreach ($testData as $key => $value) {
    echo "- $key: $value<br>";
}

// Test 3: Simulate user insertion
echo "<h3>3. Simulate User Insertion</h3>";
$transactionId = $testData['transactionid'];
$name = $testData['username'];
$mantan = $testData['mantan'];
$size = $testData['size'];
$phone = $testData['phone'];
$voucherCode = $testData['voucherCode'];
$jerseyColor = $testData['jerseyColor'];
$referrerCode = $testData['referrer_code'];

// Generate username and password
$username = strtolower(substr($name, 0, 3)) . rand(100, 999);
$password = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Insert user
$query = "INSERT INTO users (transaction_id, name, mantan, size, phone, voucher_code, username, password, jersey_color, referred_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssssss", $transactionId, $name, $mantan, $size, $phone, $voucherCode, $username, $password, $jerseyColor, $referrerCode);

if ($stmt->execute()) {
    echo "✓ Test user inserted successfully<br>";
    echo "- Transaction ID: $transactionId<br>";
    echo "- Name: $name<br>";
    echo "- Referred By: $referrerCode<br>";
} else {
    echo "✗ Failed to insert test user: " . $stmt->error . "<br>";
}
$stmt->close();

// Test 4: Simulate referral tracking
echo "<h3>4. Simulate Referral Tracking</h3>";
$referredName = $name;

// Include and test referral processing
include_once 'register/process_referral.php';
$referralProcessed = processReferral($referrerCode, $transactionId, $referredName);

if ($referralProcessed) {
    echo "✓ Referral tracking processed successfully<br>";
} else {
    echo "✗ Referral tracking failed<br>";
}

// Test 5: Verify data in database
echo "<h3>5. Verify Data in Database</h3>";

// Check user
$result = $conn->query("SELECT transaction_id, name, referred_by FROM users WHERE transaction_id = '$transactionId'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ User found in database:<br>";
    echo "- Transaction ID: " . $row['transaction_id'] . "<br>";
    echo "- Name: " . $row['name'] . "<br>";
    echo "- Referred By: " . ($row['referred_by'] ?: 'NULL') . "<br>";
} else {
    echo "✗ User not found in database<br>";
}

// Check referral
$result = $conn->query("SELECT * FROM referrals WHERE referred_transaction_id = '$transactionId'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Referral found in database:<br>";
    echo "- Referrer Code: " . $row['referrer_code'] . "<br>";
    echo "- Referred Name: " . $row['referred_name'] . "<br>";
    echo "- Status: " . $row['status'] . "<br>";
} else {
    echo "✗ Referral not found in database<br>";
}

// Test 6: Clean up test data
echo "<h3>6. Clean Up Test Data</h3>";
$conn->query("DELETE FROM referrals WHERE referred_transaction_id = '$transactionId'");
$conn->query("DELETE FROM users WHERE transaction_id = '$transactionId'");
echo "✓ Test data cleaned up<br>";

$conn->close();
echo "<br><strong>Test completed!</strong>";
?>
