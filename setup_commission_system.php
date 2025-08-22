<?php
include 'helper/db.php';

echo "<h2>Setup Commission System</h2>";

// Step 1: Add commission columns to referrer_codes table
echo "<h3>Step 1: Adding Commission Columns to Referrer Codes</h3>";

$alterQueries = [
    "ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS commission_rate DECIMAL(5,2) DEFAULT 0.00",
    "ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS commission_amount DECIMAL(10,2) DEFAULT 0.00",
    "ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS total_commission DECIMAL(10,2) DEFAULT 0.00",
    "ALTER TABLE referrer_codes ADD COLUMN IF NOT EXISTS referral_link VARCHAR(255) NULL"
];

foreach ($alterQueries as $query) {
    if ($conn->query($query)) {
        echo "✓ " . substr($query, 0, 50) . "...<br>";
    } else {
        echo "✗ Error: " . $conn->error . "<br>";
    }
}

// Step 2: Add commission columns to referrals table
echo "<h3>Step 2: Adding Commission Columns to Referrals</h3>";

$referralAlterQueries = [
    "ALTER TABLE referrals ADD COLUMN IF NOT EXISTS commission_amount DECIMAL(10,2) DEFAULT 0.00",
    "ALTER TABLE referrals ADD COLUMN IF NOT EXISTS commission_paid BOOLEAN DEFAULT FALSE",
    "ALTER TABLE referrals ADD COLUMN IF NOT EXISTS commission_paid_date TIMESTAMP NULL"
];

foreach ($referralAlterQueries as $query) {
    if ($conn->query($query)) {
        echo "✓ " . substr($query, 0, 50) . "...<br>";
    } else {
        echo "✗ Error: " . $conn->error . "<br>";
    }
}

// Step 3: Create commission transactions table
echo "<h3>Step 3: Creating Commission Transactions Table</h3>";

$createCommissionTable = "CREATE TABLE IF NOT EXISTS commission_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_code VARCHAR(50) NOT NULL,
    referral_id INT NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    base_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL
)";

if ($conn->query($createCommissionTable)) {
    echo "✓ Commission transactions table created<br>";
} else {
    echo "✗ Error creating commission table: " . $conn->error . "<br>";
}

// Step 4: Update existing referrer codes with commission rates
echo "<h3>Step 4: Updating Existing Referrer Codes</h3>";

$updateQueries = [
    "UPDATE referrer_codes SET commission_rate = 5.00, commission_amount = 5000.00, referral_link = CONCAT('https://funrun.dafam.cloud/register/?member=', code) WHERE code = 'ag'",
    "UPDATE referrer_codes SET commission_rate = 3.00, commission_amount = 3000.00, referral_link = CONCAT('https://funrun.dafam.cloud/register/?member=', code) WHERE code = 'admin'",
    "UPDATE referrer_codes SET commission_rate = 4.00, commission_amount = 4000.00, referral_link = CONCAT('https://funrun.dafam.cloud/register/?member=', code) WHERE code = 'dafam'",
    "UPDATE referrer_codes SET commission_rate = 2.50, commission_amount = 2500.00, referral_link = CONCAT('https://funrun.dafam.cloud/register/?member=', code) WHERE code = 'runner'",
    "UPDATE referrer_codes SET commission_rate = 10.00, commission_amount = 10000.00, referral_link = CONCAT('https://funrun.dafam.cloud/register/?member=', code) WHERE code = 'bluehouse'"
];

foreach ($updateQueries as $query) {
    if ($conn->query($query)) {
        echo "✓ Updated referrer code commission<br>";
    } else {
        echo "✗ Error updating commission: " . $conn->error . "<br>";
    }
}

// Step 5: Add indexes for better performance
echo "<h3>Step 5: Adding Performance Indexes</h3>";

$indexQueries = [
    "CREATE INDEX IF NOT EXISTS idx_commission_referrer ON commission_transactions(referrer_code)",
    "CREATE INDEX IF NOT EXISTS idx_commission_status ON commission_transactions(status)",
    "CREATE INDEX IF NOT EXISTS idx_referrer_codes_link ON referrer_codes(referral_link)"
];

foreach ($indexQueries as $query) {
    if ($conn->query($query)) {
        echo "✓ Index created<br>";
    } else {
        echo "✗ Error creating index: " . $conn->error . "<br>";
    }
}

// Step 6: Show current referrer codes with commission
echo "<h3>Step 6: Current Referrer Codes with Commission</h3>";

$result = $conn->query("SELECT * FROM referrer_codes WHERE is_active = 1");
if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Code</th><th>Name</th><th>Commission Rate</th><th>Commission Amount</th><th>Total Commission</th><th>Referral Link</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['code']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['commission_rate']}%</td>";
        echo "<td>Rp " . number_format($row['commission_amount'], 0, ',', '.') . "</td>";
        echo "<td>Rp " . number_format($row['total_commission'], 0, ',', '.') . "</td>";
        echo "<td><a href='{$row['referral_link']}' target='_blank'>{$row['referral_link']}</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No active referrer codes found<br>";
}

// Step 7: Test commission calculation
echo "<h3>Step 7: Test Commission Calculation</h3>";

$testTransactionId = 'COMMISSION-TEST-' . time();
$testName = 'Commission Test User';
$testReferrerCode = 'bluehouse';

// Test user insertion with commission
$insertUser = "INSERT INTO users (transaction_id, name, mantan, size, phone, voucher_code, username, password, jersey_color, referred_by) 
               VALUES (?, ?, 'Test Mantan', 'L', '08123456789', '', 'test123', '123456', 'darkblue', ?)";
$stmt = $conn->prepare($insertUser);
$stmt->bind_param("sss", $testTransactionId, $testName, $testReferrerCode);

if ($stmt->execute()) {
    echo "✓ Test user inserted<br>";
} else {
    echo "✗ Error inserting test user: " . $stmt->error . "<br>";
}
$stmt->close();

// Insert test transaction
$insertTransaction = "INSERT INTO transactions (transaction_id, total_amount, status) VALUES (?, 100000, 'pending')";
$stmt = $conn->prepare($insertTransaction);
$stmt->bind_param("si", $testTransactionId, $testAmount = 100000);

if ($stmt->execute()) {
    echo "✓ Test transaction inserted<br>";
} else {
    echo "✗ Error inserting test transaction: " . $stmt->error . "<br>";
}
$stmt->close();

// Test commission processing
include_once 'register/process_referral.php';
$referralProcessed = processReferral($testReferrerCode, $testTransactionId, $testName);

if ($referralProcessed) {
    echo "✓ Commission processing test successful<br>";
} else {
    echo "✗ Commission processing test failed<br>";
}

// Verify commission data
$result = $conn->query("SELECT * FROM referrals WHERE referred_transaction_id = '$testTransactionId'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Referral with commission found:<br>";
    echo "- Commission Amount: Rp " . number_format($row['commission_amount'], 0, ',', '.') . "<br>";
    echo "- Status: " . $row['status'] . "<br>";
} else {
    echo "✗ Referral not found<br>";
}

$result = $conn->query("SELECT * FROM commission_transactions WHERE transaction_id = '$testTransactionId'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Commission transaction found:<br>";
    echo "- Commission Amount: Rp " . number_format($row['commission_amount'], 0, ',', '.') . "<br>";
    echo "- Commission Rate: " . $row['commission_rate'] . "%<br>";
    echo "- Status: " . $row['status'] . "<br>";
} else {
    echo "✗ Commission transaction not found<br>";
}

// Clean up test data
echo "<h3>Step 8: Clean Up Test Data</h3>";
$conn->query("DELETE FROM commission_transactions WHERE transaction_id = '$testTransactionId'");
$conn->query("DELETE FROM referrals WHERE referred_transaction_id = '$testTransactionId'");
$conn->query("DELETE FROM users WHERE transaction_id = '$testTransactionId'");
$conn->query("DELETE FROM transactions WHERE transaction_id = '$testTransactionId'");
echo "✓ Test data cleaned up<br>";

$conn->close();
echo "<br><strong>Commission system setup completed!</strong>";
echo "<br><br><strong>Next Steps:</strong>";
echo "<br>1. <a href='admin/referral_management.php' target='_blank'>View Referral Management</a>";
echo "<br>2. <a href='register/?member=bluehouse' target='_blank'>Test Bluehouse Referral</a>";
echo "<br>3. <a href='register/?member=ag' target='_blank'>Test AG Referral</a>";
?>
