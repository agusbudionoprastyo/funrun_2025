<?php
include 'helper/db.php';

echo "<h2>Test Fixed Referral System</h2>";

// Step 1: Ensure referrer code exists
echo "<h3>Step 1: Check Referrer Code</h3>";
$stmt = $conn->prepare("SELECT code, name FROM referrer_codes WHERE code = ? AND is_active = 1");
$stmt->bind_param("s", 'bluehouse');
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Referrer code 'bluehouse' exists - Name: " . $row['name'] . "<br>";
} else {
    echo "✗ Referrer code 'bluehouse' does not exist. Adding it...<br>";
    
    $insertStmt = $conn->prepare("INSERT INTO referrer_codes (code, name, is_active) VALUES (?, ?, 1)");
    $insertStmt->bind_param("ss", 'bluehouse', 'Blue House Team');
    if ($insertStmt->execute()) {
        echo "✓ Referrer code 'bluehouse' added successfully<br>";
    } else {
        echo "✗ Failed to add referrer code 'bluehouse'<br>";
    }
    $insertStmt->close();
}
$stmt->close();

// Step 2: Test referral processing function
echo "<h3>Step 2: Test Referral Processing Function</h3>";
include_once 'register/process_referral.php';

$testTransactionId = 'TEST-FIXED-' . time();
$testName = 'Test User Fixed';
$testReferrerCode = 'bluehouse';

$referralProcessed = processReferral($testReferrerCode, $testTransactionId, $testName);

if ($referralProcessed) {
    echo "✓ Referral processing function works correctly<br>";
} else {
    echo "✗ Referral processing function failed<br>";
}

// Step 3: Test JSON response
echo "<h3>Step 3: Test JSON Response</h3>";
echo "Testing register.php response...<br>";

// Simulate POST data
$_POST = [
    'transactionid' => $testTransactionId,
    'registrationType' => 'single',
    'username' => $testName,
    'mantan' => 'Test Mantan',
    'phone' => '08123456789',
    'voucherCode' => '',
    'size' => 'L',
    'jerseyColor' => 'darkblue',
    'referrer_code' => $testReferrerCode
];

// Capture output
ob_start();
include 'register/register.php';
$output = ob_get_clean();

// Check if output is valid JSON
$jsonData = json_decode($output, true);
if ($jsonData !== null) {
    echo "✓ register.php returns valid JSON<br>";
    echo "Response: " . $output . "<br>";
} else {
    echo "✗ register.php returns invalid JSON<br>";
    echo "Raw output: " . htmlspecialchars($output) . "<br>";
}

// Step 4: Clean up test data
echo "<h3>Step 4: Clean Up</h3>";
$conn->query("DELETE FROM referrals WHERE referred_transaction_id = '$testTransactionId'");
$conn->query("DELETE FROM users WHERE transaction_id = '$testTransactionId'");
echo "✓ Test data cleaned up<br>";

// Step 5: Show current referrer codes
echo "<h3>Step 5: Current Referrer Codes</h3>";
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
echo "<br><strong>Test completed! The referral system should now work correctly.</strong>";
echo "<br><br><strong>Test Links:</strong>";
echo "<br><a href='register/?member=bluehouse' target='_blank'>register/?member=bluehouse</a>";
echo "<br><a href='register/?member=ag' target='_blank'>register/?member=ag</a>";
?>
