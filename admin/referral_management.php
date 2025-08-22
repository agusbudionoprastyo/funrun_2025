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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Commission</h3>
                <p class="text-3xl font-bold text-purple-600" id="total-commission">Rp 0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pending Commission</h3>
                <p class="text-3xl font-bold text-yellow-600" id="pending-commission">Rp 0</p>
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
                            <th class="px-6 py-3">Commission Rate</th>
                            <th class="px-6 py-3">Total Referrals</th>
                            <th class="px-6 py-3">Total Commission</th>
                            <th class="px-6 py-3">Paid Commission</th>
                            <th class="px-6 py-3">Referral Link</th>
                            <th class="px-6 py-3">Actions</th>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Commission Rate (%)</label>
                            <input type="number" id="commission-rate" name="commission_rate" step="0.01" min="0" max="100" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Commission Amount (Rp)</label>
                            <input type="number" id="commission-amount" name="commission_amount" step="1000" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
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
                let totalCommission = 0;
                let pendingCommission = 0;
                
                const tableBody = document.getElementById('referral-table-body');
                tableBody.innerHTML = '';
                
                data.forEach(item => {
                    totalReferrers++;
                    totalReferrals += parseInt(item.total_referrals);
                    totalCommission += parseFloat(item.total_commission_earned || 0);
                    pendingCommission += parseFloat(item.total_commission_earned || 0) - parseFloat(item.paid_commissions * (item.commission_amount || 0));
                    
                    const formattedCommission = new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(item.total_commission_earned || 0);
                    
                    const formattedPaidCommission = new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(item.paid_commissions * (item.commission_amount || 0));
                    
                    const row = document.createElement('tr');
                    row.className = 'bg-white border-b hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-6 py-4 font-medium text-gray-900">${item.code}</td>
                        <td class="px-6 py-4">${item.referrer_name}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                ${item.commission_rate || 0}%
                            </span>
                        </td>
                        <td class="px-6 py-4">${item.total_referrals}</td>
                        <td class="px-6 py-4 font-medium text-green-600">${formattedCommission}</td>
                        <td class="px-6 py-4 font-medium text-blue-600">${formattedPaidCommission}</td>
                        <td class="px-6 py-4">
                            <a href="${item.referral_link}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                ${item.referral_link}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="viewReferrerDetails('${item.code}')" class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">
                                Details
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                document.getElementById('total-referrers').textContent = totalReferrers;
                document.getElementById('total-referrals').textContent = totalReferrals;
                document.getElementById('total-commission').textContent = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(totalCommission);
                document.getElementById('pending-commission').textContent = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(pendingCommission);
                
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
        
        // Add function to view referrer details
        window.viewReferrerDetails = function(code) {
            window.open('referrer_details.php?code=' + code, '_blank');
        };
    </script>
</body>
</html>
