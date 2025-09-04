<?php
header('Content-Type: application/json');
include('helper/db.php');
include('helper/voucher.php');

echo "=== TESTING VOUCHER SYSTEM WITH DATABASE ===\n\n";

// Test 1: Get all active vouchers
echo "1. Testing getAllActiveVouchers():\n";
$vouchers = getAllActiveVouchers();
foreach ($vouchers as $voucher) {
    echo "   - {$voucher['code']}: Rp {$voucher['discount_amount']} (Usage: {$voucher['current_usage']}/{$voucher['max_usage']})\n";
}
echo "\n";

// Test 2: Test BESTIFITY voucher validation
echo "2. Testing BESTIFITY voucher validation:\n";
$result = validateVoucherFromDatabase('BESTIFITY');
echo "   Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Test USM150 voucher validation (different discount amount)
echo "3. Testing USM150 voucher validation:\n";
$result = validateVoucherFromDatabase('USM150');
echo "   Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Test invalid voucher
echo "4. Testing invalid voucher (INVALIDCODE):\n";
$result = validateVoucherFromDatabase('INVALIDCODE');
echo "   Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Test case sensitivity
echo "5. Testing case sensitivity (bestifity vs BESTIFITY):\n";
$result1 = validateVoucherFromDatabase('bestifity');
$result2 = validateVoucherFromDatabase('BESTIFITY');
echo "   'bestifity': " . ($result1['valid'] ? 'Valid' : 'Invalid') . "\n";
echo "   'BESTIFITY': " . ($result2['valid'] ? 'Valid' : 'Invalid') . "\n\n";

// Test 6: Test API endpoint
echo "6. Testing API endpoint get_vouchers.php:\n";
$apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/register/get_vouchers.php';
echo "   API URL: $apiUrl\n";
echo "   (Test this URL in browser or use curl)\n\n";

echo "=== TEST COMPLETED ===\n";
?>
