<?php
// Start the session
session_start();

// Check if the user is logged in by checking the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: /admin/login");
    exit(); // Ensure no further script execution
}
?>

<!DOCTYPE html>
<html lang="en" class="w-full">

<head class="w-full">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Data</title>

    <!-- Flowbite CSS (Tailwind CSS is automatically included in Flowbite) -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />

    <!-- IziToast CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css" />

    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- Simple DataTables CDN -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>

    <!-- Add to your HTML header to include NProgress library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.css" />
    <script src="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.js"></script>

    <style>
        /* Full width table container */
        .table-container {
            width: 100vw;
            max-width: 100vw;
            padding: 24px 24px;
            margin: 0;
            box-sizing: border-box;
            background-color: #ffffff;
            border-radius: 8px;
        }
        
        /* Table styling for full width */
        #pagination-table {
            width: 100%;
            min-width: 100%;
            table-layout: auto;
            border-collapse: collapse;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Ensure table cells have proper spacing */
        #pagination-table th,
        #pagination-table td {
            padding: 16px 16px;
            vertical-align: top;
        }
        
        /* Make sure the table header spans full width */
        #pagination-table thead th {
            background-color: #dbeafe;
            font-weight: 600;
            color: #1e3a8a;
            border-bottom: 2px solid #3b82f6;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        /* Zebra striping for table rows */
        #pagination-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        
        #pagination-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        /* Add subtle spacing between rows */
        #pagination-table tbody tr {
            margin-bottom: 2px;
        }
        
        /* Hover effect for table rows */
        #pagination-table tbody tr:hover {
            background-color: #f3f4f6 !important;
            transition: background-color 0.2s ease;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .editable-jersey, .editable-status {
            transition: all 0.2s ease;
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 4px;
            display: block;
            text-align: center;
        }
        
        .editable-jersey:hover, .editable-status:hover {
            background-color: #f3f4f6 !important;
            transform: scale(1.05);
        }
        
        .editable-jersey:active, .editable-status:active {
            transform: scale(0.95);
        }
        
        /* Dropdown styling */
        .editable-dropdown {
            border: 2px solid #3b82f6;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.875rem;
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            min-width: 100px;
        }
        
        .editable-dropdown:focus {
            outline: none;
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Ensure status badges maintain their styling */
        .editable-status.bg-green-100,
        .editable-status.bg-yellow-100,
        .editable-status.bg-pink-100,
        .editable-status.bg-gray-100 {
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Jersey color container styling */
        .jersey-color-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        
        /* Jersey color individual styling */
        .editable-jersey {
            min-width: 80px;
            font-weight: 500;
        }
        
        /* Status styling - keep inline for multiple statuses */
        .editable-status {
            display: inline-block;
            margin: 2px;
        }
        
        /* Contact container styling */
        .contact-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
        }
        
        .editable-phone {
            transition: all 0.2s ease;
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .editable-phone:hover {
            background-color: #f3f4f6 !important;
            transform: scale(1.05);
        }
        
        .editable-phone:active {
            transform: scale(0.95);
        }
        
        /* Styling for empty phone number */
        .editable-phone.italic {
            font-style: italic;
            color: #9ca3af;
        }
        
        .editable-phone.italic:hover {
            color: #6b7280;
        }
        
        /* Size container styling */
        .size-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        
        /* Editable size styling */
        .editable-size {
            transition: all 0.2s ease;
            cursor: pointer;
            padding: 2px 4px;
            border-radius: 4px;
            display: block;
            text-align: center;
            min-width: 60px;
            font-weight: 500;
        }
        
        .editable-size:hover {
            background-color: #f3f4f6 !important;
            transform: scale(1.05);
        }
        
        .editable-size:active {
            transform: scale(0.95);
        }
        
        /* Ensure full width for body and html */
        html, body {
            width: 100vw;
            max-width: 100vw;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }
        
        /* Filter styling */
        .filter-container {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
        }
        
        .filter-container label {
            font-weight: 500;
            color: #374151;
        }
        
        .filter-container select {
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .filter-container select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .filter-container select:hover {
            border-color: #9ca3af;
        }
        
        /* Status counter styling */
        .status-counter {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-counter span:first-child {
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
        }
        
        .status-counter span:last-child {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
        }
        
        /* Responsive counter layout */
        @media (max-width: 768px) {
            .filter-container .flex.justify-between {
                flex-direction: column;
                gap: 12px;
            }
            
            .filter-container .flex.items-center.space-x-6 {
                justify-content: center;
                flex-wrap: wrap;
                gap: 8px;
            }
        }
        
        /* Responsive padding for different screen sizes */
        @media (max-width: 768px) {
            .table-container {
                padding: 16px 16px;
            }
            
            .filter-container {
                padding: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .table-container {
                padding: 12px 12px;
            }
            
            .filter-container {
                padding: 8px;
            }
        }
    </style>

</head>

<body class="bg-gray-100 w-full">

<!-- Container for the Table -->
<div class="table-container">
    
    <table id="pagination-table" class="table-auto w-full text-sm text-left text-gray-500 border-collapse">
        <thead class="bg-blue-100">
            <tr>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Runners
                </th>
                <th class="px-6 py-3 font-medium text-center text-gray-900">
                    Size Jersey <span class="text-xs text-gray-500">(Click to edit)</span>
                </th>
                <th class="px-6 py-3 font-medium text-center text-gray-900">
                    Jersey Color <span class="text-xs text-gray-500">(Click to edit)</span>
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Contact <span class="text-xs text-gray-500">(Click phone to edit/add)</span>
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Transaction Date
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Amount
                </th>
                <th class="px-6 py-3 font-medium text-center text-gray-900">
                    Payment Status <span class="text-xs text-gray-500">(Click to edit)</span>
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                </th>
            </tr>
        </thead>
        <tbody class="bg-white">
            <!-- Data rows will be inserted here dynamically -->
        </tbody>
    </table>
        <!-- Filter and Search Container -->
        <div class="filter-container">
        <div class="flex justify-between items-center">
            <div></div>
            <div class="flex items-center space-x-6">
                <div class="status-counter">
                    <span>Pending:</span>
                    <span id="pending-count" class="text-yellow-600 bg-yellow-100">0</span>
                </div>
                <div class="status-counter">
                    <span>Paid:</span>
                    <span id="paid-count" class="text-green-600 bg-green-100">0</span>
                </div>
                <div class="status-counter">
                    <span>Verified:</span>
                    <span id="verified-count" class="text-pink-600 bg-pink-100">0</span>
                </div>
                <div class="status-counter">
                    <span>Total:</span>
                    <span id="total-count" class="text-blue-600 bg-blue-100">0</span>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- IziToast JS -->
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
<!-- Flowbite -->
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

<!-- Modal HTML -->
<div id="update-status-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white shadow-lg">

            <!-- Image (Payment Proof) -->
            <img id="payment-proof-image" class="w-full h-auto mb-4" src="" alt="Payment Proof" />

            <!-- Close Button -->
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-50 hover:bg-opacity-50 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" id="close-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>

            <!-- Modal Content -->
            <div class="p-4 space-y-4">
                <!-- Transaction Details -->
                <h5 id="modal-name" class="text-lg font-semibold tracking-tight text-gray-900">Name Here</h5>
                <div class="flex items-center justify-between">
                    <span id="modal-amount" class="text-xl font-bold text-gray-900">Rp0</span>
                    <button type="button" id="verified-btn" class="text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function fetchData() {
        try {
            const response = await fetch('get_transactions.php');
            const data = await response.json(); // Assuming the response is in JSON format
            
            // Store original data for filtering
            originalData = data;

            const tableBody = document.querySelector("#pagination-table tbody");
            tableBody.innerHTML = ''; // Clear existing rows

            // Sort data by status: pending first, then paid, then verified
            data.sort((a, b) => {
                const statusOrder = { 'pending': 1, 'paid': 2, 'verified': 3 };
                const aOrder = statusOrder[a.status] || 4;
                const bOrder = statusOrder[b.status] || 4;
                return aOrder - bOrder;
            });
            
            // Loop through the data and create table rows dynamically
            data.forEach(item => {
                const formattedAmount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.total_amount);

                const row = document.createElement("tr");
                row.classList.add('border-b');

                let badge_class = item.badge_class;

                row.innerHTML = `
                    <th scope="row" class="flex items-center px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    <img class="w-10 h-10 rounded-full" src="https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_1280.png" alt="D">
                    <div class="ps-3">
                        ${item.size_1 ? `<div class="font-semibold text-gray-500">${item.name_1} (${item.mantan_1})</div>` : ''}
                        ${item.size_2 ? `<div class="font-semibold text-gray-500">${item.name_2} (${item.mantan_2})</div>` : ''}
                    </div>
                    </th>
                    <th class="px-6 py-4 font-medium text-center text-gray-900">
                        <div class="size-container">
                            ${item.size_1 ? `<div class="editable-size" data-transaction-id="${item.transaction_id}" data-field="size_1" data-value="${item.size_1}" title="Click to edit jersey size">${item.size_1.toUpperCase()}</div>` : ''}
                            ${item.size_2 ? `<div class="editable-size" data-transaction-id="${item.transaction_id}" data-field="size_2" data-value="${item.size_2}" title="Click to edit jersey size">${item.size_2.toUpperCase()}</div>` : ''}
                        </div>
                    </th>
                    <th class="px-6 py-4 font-medium text-center text-gray-900">
                        <div class="jersey-color-container">
                            ${item.jersey_color_1 ? `<div class="editable-jersey" data-transaction-id="${item.transaction_id}" data-field="jersey_color_1" data-value="${item.jersey_color_1}" title="Click to edit jersey color">${item.jersey_color_1.toUpperCase()}</div>` : ''}
                            ${item.jersey_color_2 ? `<div class="editable-jersey" data-transaction-id="${item.transaction_id}" data-field="jersey_color_2" data-value="${item.jersey_color_2}" title="Click to edit jersey color">${item.jersey_color_2.toUpperCase()}</div>` : ''}
                        </div>
                    </th>
                    <th class="px-6 py-4 font-medium text-gray-900">
                        <div class="contact-container">
                            <div class="contact-item">
                                ${item.phone_1 ? 
                                    `<span class="editable-phone font-normal text-gray-500" 
                                        data-transaction-id="${item.transaction_id}" 
                                        data-field="phone_1" 
                                        data-value="${item.phone_1}" 
                                        title="Click to edit phone number">${item.phone_1}</span>` : 
                                    `<span class="editable-phone font-normal text-gray-400 italic" 
                                        data-transaction-id="${item.transaction_id}" 
                                        data-field="phone_1" 
                                        data-value="" 
                                        title="Click to add phone number">Add phone number</span>`
                                }
                            </div>
                            <div class="ml-1 font-normal text-gray-500">${item.email_1}</div>
                        </div>
                    </th>
                    <td class="px-6 py-4">${item.transaction_date}</td>
                    <td class="px-6 py-4">${formattedAmount}</td>
                    <td class="text-center px-6 py-4">
                        <span class="editable-status ${badge_class}" data-transaction-id="${item.transaction_id}" data-field="status" data-value="${item.status}" title="Click to edit payment status">
                            ${item.status}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button type="button" class="update-status-btn text-gray-900 hover:text-white border border-gray-800 hover:bg-gray-900 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2" 
                        data-transaction-id="${item.transaction_id}" 
                        data-phone="${item.phone_1}" 
                        data-name="${item.name_1} ${item.name_2 ? ' / ' + item.name_2 : ''}" 
                        data-amount="${formattedAmount}" data-payment-img="../users/paymentprooft/${item.payment_prooft}" 
                        data-current-status="${item.status}">
                            Confirm
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Initialize DataTable
            if (document.getElementById("pagination-table") && typeof simpleDatatables.DataTable !== 'undefined') {
                new simpleDatatables.DataTable("#pagination-table", {
                    searchable: true,
                    paging: true,
                    perPage: 10,
                    perPageSelect: [5, 10, 15, 20, 25],
                    sortable: false
                });
            }
            
            // Update status counts after data is loaded
            updateStatusCounts();

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    document.addEventListener('click', (event) => {
        if (event.target && event.target.classList.contains('update-status-btn')) {
            const transactionId = event.target.getAttribute('data-transaction-id');
            const phone = event.target.getAttribute('data-phone');
            const name = event.target.getAttribute('data-name');
            const amount = event.target.getAttribute('data-amount');
            const paymentImg = event.target.getAttribute('data-payment-img');
            const currentStatus = event.target.getAttribute('data-current-status');

            // Check if the transaction status is "Pending"
            if (currentStatus === 'pending') {
                // Notify the user that the transaction is still pending
                iziToast.info({
                    title: 'Info',
                    message: 'This transaction is still Pending. You cannot confirm it yet.',
                    position: 'topRight',
                });
                return; // Do not proceed to open the modal if the status is Pending
            }

            // Populate modal with details
            document.getElementById('payment-proof-image').src = paymentImg; // Optionally set the image
            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-amount').textContent = amount;
            document.getElementById('verified-btn').dataset.transactionId = transactionId; // Add transactionId to the button for submission
            document.getElementById('verified-btn').dataset.phone = phone;

            // Show the modal
            document.getElementById('update-status-modal').classList.remove('hidden');
        }
    });

    // Close the modal
    document.getElementById('close-modal').addEventListener('click', () => {
        document.getElementById('update-status-modal').classList.add('hidden');
    });

    document.getElementById('verified-btn').addEventListener('click', async (event) => {
        const transactionId = event.target.dataset.transactionId;
        const newStatus = "verified"; // Set status directly to "Verified"
        const apiKey = "JkGJqE9infpzKbwD6QrmrciZPF1fwt";  // API Key kamu
        const sender = "6281770019808"; // Nomor pengirim
        const recipientNumber = event.target.dataset.phone; // Nomor penerima yang diambil dari dataset tombol
        const message = "*Pembayaran Anda telah diverifikasi!*\n\n_Tunjukkan QR code ini kepada staff kami saat pengambilan_ *RCP* (racepack).\n_Terima kasih atas partisipasi anda._\n\n*Pengambilan Racepack*\n11 Oktober 2025\n10:00 - 19:00 WIB\nHotel Dafam Semarang\n\n*Funrun - Lari Sama Mantan*\nTgl 12 Oktober 2025\nStart 06:00 WIB\n\n*access Runmap*\nhttps://funrun.dafam.cloud";

        // Validasi jika nomor penerima ada
        if (!recipientNumber) {
            console.error('Recipient number is missing!');
            iziToast.error({
                title: 'Error',
                message: 'Recipient number is missing.',
                position: 'topRight',
            });
            return;
        }

        // Start the loading progress bar
        NProgress.start();

        try {
            // Step 1: Update status transaksi ke "verified"
            const updateResponse = await fetch('update_transactions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ transaction_id: transactionId, status: newStatus }),
            });

            const updateResult = await updateResponse.json();
            if (updateResult.success) {
                // Step 2: Generate QR code for transaction ID
                const qrCodeDataUrl = await generateQRCode(transactionId);  // Generates QR code and returns its Data URL

                // Step 3: Send QR code data URL to backend for saving as an image in the "qrid" folder
                const qrCodeFileUrl = await saveQRCode(qrCodeDataUrl, transactionId);  // Get the URL of the saved QR code

                // Step 4: Send media message with the URL of the saved QR code to the recipient
                const url = `https://wapi.dafam.cloud/send-media?api_key=${apiKey}&sender=${sender}&number=${recipientNumber}&media_type=image&caption=${encodeURIComponent(message)}&url=${encodeURIComponent(qrCodeFileUrl)}`;
                const sendMessageResponse = await fetch(url, { mode: 'no-cors' });

                iziToast.success({
                    title: 'Success',
                    message: 'Payment status updated to Verified, QR code sent!',
                    position: 'topRight',
                });
                fetchData(); // Reload data after update
                document.getElementById('update-status-modal').classList.add('hidden'); // Close modal
            } else {
                iziToast.info({
                    title: 'Info',
                    message: 'Payment has already been Verified.',
                    position: 'topRight',
                });
            }
        } catch (error) {
            console.error('Error updating status or sending message:', error);
            iziToast.error({
                title: 'Error',
                message: 'Error updating status or sending message. Please try again.',
                position: 'topRight',
            });
        } finally {
            // Complete the progress bar after the process finishes
            NProgress.done();
        }
    });

    // Function to generate QR code as a data URL
    function generateQRCode(transactionId) {
        return new Promise((resolve, reject) => {
            try {
                QRCode.toDataURL(transactionId, { width: 500, height: 500 }, (err, url) => {
                    if (err) reject(err);
                    else resolve(url);
                });
            } catch (err) {
                reject(err);
            }
        });
    }

    // Function to send the generated QR code to the backend for saving
    async function saveQRCode(qrCodeDataUrl, transactionId) {
        const response = await fetch('save_qr_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ qr_code_data_url: qrCodeDataUrl, transaction_id: transactionId }),
        });

        const result = await response.json();
        if (result.success) {
            // Return the URL of the saved QR code image
            return result.file_url;  // The file URL returned by the backend
        } else {
            console.error('Failed to save QR code');
            throw new Error('Failed to save QR code');
        }
    }

    // Call fetchData to populate the table
    fetchData();
    
    // Store original data for filtering
    let originalData = [];
    
    // Function to update status counts (from all data, not just visible rows)
    function updateStatusCounts() {
        let pendingCount = 0;
        let paidCount = 0;
        let verifiedCount = 0;
        let totalCount = 0;

        // Count from all originalData
        originalData.forEach(item => {
            totalCount++;
            switch(item.status) {
                case 'pending':
                    pendingCount++;
                    break;
                case 'paid':
                    paidCount++;
                    break;
                case 'verified':
                    verifiedCount++;
                    break;
            }
        });

        document.getElementById('pending-count').textContent = pendingCount;
        document.getElementById('paid-count').textContent = paidCount;
        document.getElementById('verified-count').textContent = verifiedCount;
        document.getElementById('total-count').textContent = totalCount;
    }
    
    // Function to filter table by status
    function filterTableByStatus(status) {
        const tableBody = document.querySelector("#pagination-table tbody");
        const rows = tableBody.querySelectorAll("tr");
        
        rows.forEach(row => {
            const statusCell = row.querySelector('.editable-status');
            if (statusCell) {
                const rowStatus = statusCell.getAttribute('data-value');
                if (status === '' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Update DataTable if it exists
        if (window.dataTable) {
            window.dataTable.refresh();
        }
        
        // Update counts after filtering
        updateStatusCounts();
    }

    // Add event listeners for inline editing
    document.addEventListener('click', function(e) {
        // Handle jersey color editing
        if (e.target.classList.contains('editable-jersey')) {
            const currentValue = e.target.getAttribute('data-value');
            const transactionId = e.target.getAttribute('data-transaction-id');
            const field = e.target.getAttribute('data-field');
            
            // Create dropdown for jersey colors
            const colors = ['darkblue', 'purple'];
            const dropdown = document.createElement('select');
            dropdown.className = 'editable-dropdown';
            
            colors.forEach(color => {
                const option = document.createElement('option');
                option.value = color;
                option.textContent = color.toUpperCase();
                if (color === currentValue) {
                    option.selected = true;
                }
                dropdown.appendChild(option);
            });
            
            // Replace the div with dropdown
            e.target.style.display = 'none';
            e.target.parentNode.insertBefore(dropdown, e.target);
            dropdown.focus();
            
            // Handle dropdown change
            dropdown.addEventListener('change', async function() {
                const newValue = this.value;
                try {
                    const response = await fetch('update_jersey_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId,
                            field: field,
                            value: newValue
                        }),
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        e.target.textContent = newValue.toUpperCase();
                        e.target.setAttribute('data-value', newValue);
                        iziToast.success({
                            title: 'Success',
                            message: 'Jersey color updated successfully!',
                            position: 'topRight',
                        });
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: result.message || 'Failed to update jersey color',
                            position: 'topRight',
                        });
                    }
                } catch (error) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to update jersey color',
                        position: 'topRight',
                    });
                }
                
                // Remove dropdown and show div again
                this.remove();
                e.target.style.display = 'block';
            });
            
            // Handle dropdown blur (cancel edit)
            dropdown.addEventListener('blur', function() {
                setTimeout(() => {
                    this.remove();
                    e.target.style.display = 'block';
                }, 100);
            });
        }
        
        // Handle status editing
        if (e.target.classList.contains('editable-status')) {
            const currentValue = e.target.getAttribute('data-value');
            const transactionId = e.target.getAttribute('data-transaction-id');
            const field = e.target.getAttribute('data-field');
            
            // Create dropdown for status
            const statuses = ['pending', 'paid', 'verified'];
            const dropdown = document.createElement('select');
            dropdown.className = 'editable-dropdown';
            
            statuses.forEach(status => {
                const option = document.createElement('option');
                option.value = status;
                option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                if (status === currentValue) {
                    option.selected = true;
                }
                dropdown.appendChild(option);
            });
            
            // Replace the span with dropdown
            e.target.style.display = 'none';
            e.target.parentNode.insertBefore(dropdown, e.target);
            dropdown.focus();
            
            // Handle dropdown change
            dropdown.addEventListener('change', async function() {
                const newValue = this.value;
                try {
                    const response = await fetch('update_jersey_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId,
                            field: field,
                            value: newValue
                        }),
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        // Update the status text and badge class
                        e.target.textContent = newValue;
                        e.target.setAttribute('data-value', newValue);
                        
                        // Update badge class based on new status
                        let badgeClass = '';
                        switch (newValue) {
                            case 'paid':
                                badgeClass = 'bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full';
                                break;
                            case 'pending':
                                badgeClass = 'bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full';
                                break;
                            case 'verified':
                                badgeClass = 'bg-pink-100 text-pink-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full';
                                break;
                            default:
                                badgeClass = 'bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full';
                                break;
                        }
                        
                        // Remove old badge classes and add new one
                        e.target.className = e.target.className.replace(/bg-\w+-100 text-\w+-800/g, '');
                        e.target.className = e.target.className.replace(/editable-status/, '');
                        e.target.className = `editable-status ${badgeClass}`;
                        
                        iziToast.success({
                            title: 'Success',
                            message: 'Payment status updated successfully!',
                            position: 'topRight',
                        });
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: result.message || 'Failed to update payment status',
                            position: 'topRight',
                        });
                    }
                } catch (error) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to update payment status',
                        position: 'topRight',
                    });
                }
                
                // Remove dropdown and show span again
                this.remove();
                e.target.style.display = 'inline';
            });
            
            // Handle dropdown blur (cancel edit)
            dropdown.addEventListener('blur', function() {
                setTimeout(() => {
                    this.remove();
                    e.target.style.display = 'inline';
                }, 100);
            });
        }
        
        // Handle phone number editing
        if (e.target.classList.contains('editable-phone')) {
            const currentValue = e.target.getAttribute('data-value');
            const transactionId = e.target.getAttribute('data-transaction-id');
            const field = e.target.getAttribute('data-field');
            
            // Create input field for phone number
            const input = document.createElement('input');
            input.type = 'tel';
            input.value = currentValue;
            input.className = 'editable-dropdown';
            input.placeholder = currentValue ? 'Enter phone number' : 'Enter phone number to add';
            
            // Replace the span with input
            e.target.style.display = 'none';
            e.target.parentNode.insertBefore(input, e.target);
            input.focus();
            input.select();
            
            // Handle input change
            input.addEventListener('blur', async function() {
                const newValue = this.value.trim();
                
                if (newValue && newValue !== currentValue) {
                    try {
                        const response = await fetch('update_jersey_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                transaction_id: transactionId,
                                field: field,
                                value: newValue
                            }),
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            // Update the phone number display
                            e.target.textContent = newValue;
                            e.target.setAttribute('data-value', newValue);
                            e.target.className = 'editable-phone font-normal text-gray-500';
                            e.target.setAttribute('title', 'Click to edit phone number');
                            
                            iziToast.success({
                                title: 'Success',
                                message: 'Phone number updated successfully!',
                                position: 'topRight',
                            });
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: result.message || 'Failed to update phone number',
                                position: 'topRight',
                            });
                        }
                    } catch (error) {
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to update phone number',
                            position: 'topRight',
                        });
                    }
                } else if (!newValue && currentValue) {
                    // If user cleared the phone number, show "Add phone number" again
                    e.target.textContent = 'Add phone number';
                    e.target.setAttribute('data-value', '');
                    e.target.className = 'editable-phone font-normal text-gray-400 italic';
                    e.target.setAttribute('title', 'Click to add phone number');
                }
                
                // Remove input and show span again
                this.remove();
                e.target.style.display = 'inline';
            });
            
            // Handle Enter key
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.blur();
                }
            });
            
            // Handle Escape key
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    this.remove();
                    e.target.style.display = 'inline';
                }
            });
        }
        
        // Handle size editing
        if (e.target.classList.contains('editable-size')) {
            const currentValue = e.target.getAttribute('data-value');
            const transactionId = e.target.getAttribute('data-transaction-id');
            const field = e.target.getAttribute('data-field');
            
            // Create dropdown for jersey sizes
            const sizes = ['xs', 's', 'm', 'l', 'xl', '2xl', '3xl', '4xl', '5xl'];
            const dropdown = document.createElement('select');
            dropdown.className = 'editable-dropdown';
            
            sizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size.toUpperCase();
                if (size === currentValue) {
                    option.selected = true;
                }
                dropdown.appendChild(option);
            });
            
            // Replace the div with dropdown
            e.target.style.display = 'none';
            e.target.parentNode.insertBefore(dropdown, e.target);
            dropdown.focus();
            
            // Handle dropdown change
            dropdown.addEventListener('change', async function() {
                const newValue = this.value;
                try {
                    const response = await fetch('update_jersey_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            transaction_id: transactionId,
                            field: field,
                            value: newValue
                        }),
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        e.target.textContent = newValue.toUpperCase();
                        e.target.setAttribute('data-value', newValue);
                        
                        iziToast.success({
                            title: 'Success',
                            message: 'Jersey size updated successfully!',
                            position: 'topRight',
                        });
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: result.message || 'Failed to update jersey size',
                            position: 'topRight',
                        });
                    }
                } catch (error) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to update jersey size',
                        position: 'topRight',
                    });
                }
                
                // Remove dropdown and show div again
                this.remove();
                e.target.style.display = 'block';
            });
            
            // Handle dropdown blur (cancel edit)
            dropdown.addEventListener('blur', function() {
                setTimeout(() => {
                    this.remove();
                    e.target.style.display = 'block';
                }, 100);
            });
        }
    });
</script>

</body>

</html>