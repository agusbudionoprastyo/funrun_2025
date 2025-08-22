<?php
include 'helper/db.php';

echo "<h2>Adding Bluehouse Referrer Code</h2>";

// Add bluehouse referrer code
$insertQuery = "INSERT IGNORE INTO referrer_codes (code, name, is_active) VALUES ('bluehouse', 'Blue House Team', 1)";

if ($conn->query($insertQuery)) {
    echo "✓ Bluehouse referrer code added successfully<br>";
} else {
    echo "✗ Error adding bluehouse referrer code: " . $conn->error . "<br>";
}

// Verify it exists
$result = $conn->query("SELECT * FROM referrer_codes WHERE code = 'bluehouse'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ Bluehouse referrer code verified:<br>";
    echo "- Code: " . $row['code'] . "<br>";
    echo "- Name: " . $row['name'] . "<br>";
    echo "- Active: " . ($row['is_active'] ? 'Yes' : 'No') . "<br>";
} else {
    echo "✗ Bluehouse referrer code not found<br>";
}

$conn->close();
echo "<br><strong>Bluehouse referrer code setup completed!</strong>";
echo "<br><br><strong>Test Link:</strong>";
echo "<br><a href='register/?member=bluehouse' target='_blank'>register/?member=bluehouse</a>";
?>
