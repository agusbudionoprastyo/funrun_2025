<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /admin/login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Management</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
</head>

<body class="bg-gray-100 w-full">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Referral Management</h1>
            <a href="index.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Referrers</h3>
                <p class="text-3xl font-bold text-blue-600" id="total-referrers">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Referrals</h3>
                <p class="text-3xl font-bold text-green-600" id="total-referrals">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Completed</h3>
                <p class="text-3xl font-bold text-purple-600" id="completed-referrals">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pending</h3>
                <p class="text-3xl font-bold text-yellow-600" id="pending-referrals">0</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Referral Statistics</h2>
            </div>
            <div class="p-6">
                <table id="referral-table" class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Referrer Code</th>
                            <th class="px-6 py-3">Referrer Name</th>
                            <th class="px-6 py-3">Total Referrals</th>
                            <th class="px-6 py-3">Completed</th>
                            <th class="px-6 py-3">Pending</th>
                            <th class="px-6 py-3">Success Rate</th>
                        </tr>
                    </thead>
                    <tbody id="referral-table-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mt-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Add New Referrer</h2>
            </div>
            <div class="p-6">
                <form id="add-referrer-form" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referrer Code</label>
                            <input type="text" id="referrer-code" name="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Referrer Name</label>
                            <input type="text" id="referrer-name" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Referrer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    <script>
        async function loadReferralStats() {
            try {
                const response = await fetch('referral_stats.php');
                const data = await response.json();
                
                let totalReferrers = 0;
                let totalReferrals = 0;
                let completedReferrals = 0;
                let pendingReferrals = 0;
                
                const tableBody = document.getElementById('referral-table-body');
                tableBody.innerHTML = '';
                
                data.forEach(item => {
                    totalReferrers++;
                    totalReferrals += parseInt(item.total_referrals);
                    completedReferrals += parseInt(item.completed_referrals);
                    pendingReferrals += parseInt(item.pending_referrals);
                    
                    const successRate = item.total_referrals > 0 ? 
                        ((item.completed_referrals / item.total_referrals) * 100).toFixed(1) : '0.0';
                    
                    const row = document.createElement('tr');
                    row.className = 'bg-white border-b hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-6 py-4 font-medium text-gray-900">${item.code}</td>
                        <td class="px-6 py-4">${item.referrer_name}</td>
                        <td class="px-6 py-4">${item.total_referrals}</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                ${item.completed_referrals}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                ${item.pending_referrals}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                ${successRate}%
                            </span>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                document.getElementById('total-referrers').textContent = totalReferrers;
                document.getElementById('total-referrals').textContent = totalReferrals;
                document.getElementById('completed-referrals').textContent = completedReferrals;
                document.getElementById('pending-referrals').textContent = pendingReferrals;
                
            } catch (error) {
                console.error('Error loading referral stats:', error);
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load referral statistics',
                    position: 'topRight',
                });
            }
        }

        document.getElementById('add-referrer-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('add_referrer.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    iziToast.success({
                        title: 'Success',
                        message: 'Referrer added successfully',
                        position: 'topRight',
                    });
                    
                    this.reset();
                    loadReferralStats();
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: result.message || 'Failed to add referrer',
                        position: 'topRight',
                    });
                }
            } catch (error) {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to add referrer',
                    position: 'topRight',
                });
            }
        });

        loadReferralStats();
    </script>
</body>
</html>
