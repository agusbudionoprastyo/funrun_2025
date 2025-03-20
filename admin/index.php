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
<html lang="en">

<head>
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
</head>

<body class="bg-gray-100">

<!-- Container for the Table -->
<div class="container mx-auto p-6">
    <table id="pagination-table" class="table-auto w-full text-sm text-left text-gray-500 border-collapse">
        <thead class="bg-blue-100">
            <tr>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Runners
                </th>
                <th class="px-6 py-3 font-medium text-center text-gray-900">
                    Size Jarsey
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Phone
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Email
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Transaction Date
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                    Amount
                </th>
                <th class="px-6 py-3 font-medium text-center text-gray-900">
                    Payment Status
                </th>
                <th class="px-6 py-3 font-medium text-gray-900">
                </th>
            </tr>
        </thead>
        <tbody class="bg-white">
            <!-- Data rows will be inserted here dynamically -->
        </tbody>
    </table>
</div>


<!-- IziToast JS -->
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
<!-- Flowbite -->
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

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

            const tableBody = document.querySelector("#pagination-table tbody");
            tableBody.innerHTML = ''; // Clear existing rows

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
                    <th class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap">
                        ${item.size_1 ? `<div class="font-normal text-gray-500 uppercase">${item.size_1}</div>` : ''}
                        ${item.size_2 ? `<div class="font-normal text-gray-500 uppercase">${item.size_2}</div>` : ''}
                    </th>
                    <td class="px-6 py-4">${item.phone_1}</td>
                    <td class="px-6 py-4">${item.email_1}</td>
                    <td class="px-6 py-4">${item.transaction_date}</td>
                    <td class="px-6 py-4">${formattedAmount}</td>
                    <td class="text-center px-6 py-4">
                        <span class="${badge_class}">
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
                    perPage: 5,
                    perPageSelect: [5, 10, 15, 20, 25],
                    sortable: false
                });
            }

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }
    // Open the modal and populate with transaction details
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
        const apiKey = "JkGJqE9infpzKbwD6QrmrciZPF1fwt";  // Replace with your actual API key
        const sender = "6281770019808"; // Replace with your sender's number
        const recipientNumber = event.target.dataset.phone; // Get the recipient's phone number from the clicked row (use `phone_1` dynamically)
        const message = "Your payment has been verified."; // Customize the message as needed

        // Log to check the recipient's phone number
        console.log('Recipient Number:', recipientNumber); // Log nomor telepon yang akan dikirim

        try {
            // Step 1: Update the transaction status
            const updateResponse = await fetch('update_transactions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ transaction_id: transactionId, status: newStatus }),
            });

            const updateResult = await updateResponse.json();
            if (updateResult.success) {
                // Step 2: Send message after successful status update
                const sendMessageResponse = await fetch('https://wapi.dafam.cloud/send-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        api_key: apiKey,
                        sender: sender,
                        number: recipientNumber, // Here phone_1 is used as the recipient
                        message: message
                    }),
                });

                const sendMessageResult = await sendMessageResponse.json();

                // Log response from the API send-message
                console.log('Response from send-message API:', sendMessageResult); // Log respons dari API

                if (sendMessageResult.success) {
                    iziToast.success({
                        title: 'Success',
                        message: 'Payment status updated to Verified and message sent!',
                        position: 'topRight',
                    });
                    fetchData(); // Refresh the data after update
                    document.getElementById('update-status-modal').classList.add('hidden'); // Close the modal
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: 'Failed to send message. Please try again.',
                        position: 'topRight',
                    });
                }
            } else {
                iziToast.info({
                    title: 'Info',
                    message: 'Payment has already been Verified.',
                    position: 'topRight',
                });
            }
        } catch (error) {
            console.error('Error updating status:', error);
            iziToast.error({
                title: 'Error',
                message: 'Error updating status or sending message. Please try again.',
                position: 'topRight',
            });
        }
    });

    // document.getElementById('verified-btn').addEventListener('click', async (event) => {
    //     const transactionId = event.target.dataset.transactionId;
    //     const newStatus = "Verified"; // Set status directly to "Verified"

    //     try {
    //         const response = await fetch('update_transactions.php', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //             },
    //             body: JSON.stringify({ transaction_id: transactionId, status: newStatus }),
    //         });

    //         const result = await response.json();
    //         if (result.success) {
    //             iziToast.success({
    //                 title: 'Success',
    //                 message: 'Payment status updated to Verified!',
    //                 position: 'topRight',
    //             });
    //             fetchData(); // Refresh the data after update
    //             document.getElementById('update-status-modal').classList.add('hidden'); // Close the modal
    //         } else {
    //             iziToast.info({
    //                 title: 'Info',
    //                 message: 'Payment has been Verified.',
    //                 position: 'topRight',
    //             });
    //         }
    //     } catch (error) {
    //         console.error('Error updating status:', error);
    //         iziToast.error({
    //             title: 'Error',
    //             message: 'Error updating status. Please try again.',
    //             position: 'topRight',
    //         });
    //     }
    // });

    // Call fetchData to populate the table
    fetchData();
</script>


</body>

</html>