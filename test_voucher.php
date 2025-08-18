<?php
// Test file untuk debugging voucher code
header('Content-Type: application/json');

// Simulasi data POST
$testVoucherCode = 'SEMARANGRUNNER'; // Test dengan voucher valid

// Validasi voucher code
$validVouchers = ['SEMARANGRUNNER', 'FAKERUNNER', 'BERLARIBERSAMA', 'PLAYONAMBYAR', 'PLAYONNDESO', 'BESTIFITY', 'DURAKINGRUN', 'SALATIGARB', 'PELARIAN'];

echo "Testing voucher code: " . $testVoucherCode . "\n";
echo "Valid vouchers: " . implode(', ', $validVouchers) . "\n";

if (in_array(strtoupper(trim($testVoucherCode)), $validVouchers)) {
    echo "Voucher valid! Discount: Rp 15.000\n";
} else {
    echo "Voucher tidak valid!\n";
}

// Test dengan beberapa voucher codes
$testCodes = ['SEMARANGRUNNER', 'FAKERUNNER', 'INVALIDCODE', 'semarangrunner', ' SemarangRunner '];

foreach ($testCodes as $code) {
    $trimmed = strtoupper(trim($code));
    $isValid = in_array($trimmed, $validVouchers);
    echo "Code: '$code' -> Trimmed: '$trimmed' -> Valid: " . ($isValid ? 'Yes' : 'No') . "\n";
}
?>
