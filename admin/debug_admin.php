<?php
include '../helper/db.php';

echo "<h1>Admin Debug</h1>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
if ($conn->connect_error) {
    echo "❌ Database connection failed: " . $conn->connect_error;
} else {
    echo "✅ Database connection successful";
}

// Test get_transactions.php
echo "<h2>get_transactions.php Test</h2>";
$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_transactions.php';
echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";

// Test direct query
echo "<h2>Direct Query Test</h2>";
$sql = "SELECT COUNT(*) as total FROM transactions";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Total transactions: " . $row['total'];
} else {
    echo "❌ Query failed: " . $conn->error;
}

// Test users table
echo "<h2>Users Table Test</h2>";
$sql = "SELECT COUNT(*) as total FROM users";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Total users: " . $row['total'];
} else {
    echo "❌ Query failed: " . $conn->error;
}

// Test voucher table
echo "<h2>Voucher Table Test</h2>";
$sql = "SELECT COUNT(*) as total FROM vouchers";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Total vouchers: " . $row['total'];
} else {
    echo "❌ Query failed: " . $conn->error;
}

$conn->close();
?>
