<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login");
    exit();
}

include '../helper/db.php';

$referrerCode = $_GET['code'] ?? '';

if (empty($referrerCode)) {
    header("Location: referral_management.php");
    exit();
}

// Get referrer details
$stmt = $conn->prepare("SELECT * FROM referrer_codes WHERE code = ?");
$stmt->bind_param("s", $referrerCode);
$stmt->execute();
$result = $stmt->get_result();
$referrer = $result->fetch_assoc();
$stmt->close();

if (!$referrer) {
    header("Location: referral_management.php");
    exit();
}

// Get referral details
$stmt = $conn->prepare("
    SELECT r.*, u.name as user_name, t.total_amount, t.status as transaction_status
    FROM referrals r
    LEFT JOIN users u ON r.referred_transaction_id = u.transaction_id
    LEFT JOIN transactions t ON r.referred_transaction_id = t.transaction_id
    WHERE r.referrer_code = ?
    ORDER BY r.referral_date DESC
");
$stmt->bind_param("s", $referrerCode);
$stmt->execute();
$referrals = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" class="w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referrer Details - <?= htmlspecialchars($referrer['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>

<body class="bg-gray-100 w-full">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Referrer Details</h1>
            <a href="referral_management.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Back to Management
            </a>
        </div>

        <!-- Referrer Info Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4"><?= htmlspecialchars($referrer['name']) ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Basic Info</h3>
                    <p><strong>Code:</strong> <?= htmlspecialchars($referrer['code']) ?></p>
                    <p><strong>Commission Rate:</strong> <?= $referrer['commission_rate'] ?>%</p>
                    <p><strong>Commission Amount:</strong> Rp <?= number_format($referrer['commission_amount'], 0, ',', '.') ?></p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Statistics</h3>
                    <p><strong>Total Commission:</strong> Rp <?= number_format($referrer['total_commission'], 0, ',', '.') ?></p>
                    <p><strong>Active:</strong> <?= $referrer['is_active'] ? 'Yes' : 'No' ?></p>
                    <p><strong>Created:</strong> <?= date('d M Y', strtotime($referrer['created_at'])) ?></p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Referral Link</h3>
                    <a href="<?= htmlspecialchars($referrer['referral_link']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                        <?= htmlspecialchars($referrer['referral_link']) ?>
                    </a>
                    <button onclick="copyToClipboard('<?= htmlspecialchars($referrer['referral_link']) ?>')" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white text-xs font-bold py-1 px-2 rounded">
                        Copy
                    </button>
                </div>
            </div>
        </div>

        <!-- Referrals Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Referral History</h2>
            </div>
            <div class="p-6">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Referred Name</th>
                            <th class="px-6 py-3">Transaction ID</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Commission</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Commission Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($referrals->num_rows > 0): ?>
                            <?php while ($referral = $referrals->fetch_assoc()): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($referral['referral_date'])) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($referral['referred_name']) ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($referral['referred_transaction_id']) ?></td>
                                    <td class="px-6 py-4">Rp <?= number_format($referral['total_amount'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="px-6 py-4">Rp <?= number_format($referral['commission_amount'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4">
                                        <span class="bg-<?= $referral['status'] === 'completed' ? 'green' : 'yellow' ?>-100 text-<?= $referral['status'] === 'completed' ? 'green' : 'yellow' ?>-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            <?= ucfirst($referral['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-<?= $referral['commission_paid'] ? 'green' : 'yellow' ?>-100 text-<?= $referral['commission_paid'] ? 'green' : 'yellow' ?>-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            <?= $referral['commission_paid'] ? 'Paid' : 'Pending' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No referrals found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                iziToast.success({
                    title: 'Success',
                    message: 'Referral link copied to clipboard!',
                    position: 'topRight',
                });
            }, function(err) {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to copy link',
                    position: 'topRight',
                });
            });
        }
    </script>
</body>
</html>
