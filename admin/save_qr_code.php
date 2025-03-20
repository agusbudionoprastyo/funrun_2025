<?php
// save_qr_code.php

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$qrCodeDataUrl = $data['qr_code_data_url'];
$transactionId = $data['transaction_id'];

// Remove the prefix from the Data URL (e.g., "data:image/png;base64,")
$base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $qrCodeDataUrl);

// Decode the base64 data to binary data
$imageData = base64_decode($base64Data);

// Define the folder to save the QR code
$folderPath = 'qrid/';
if (!is_dir($folderPath)) {
    mkdir($folderPath, 0777, true);
}

// Define the file path for saving the image
$filePath = $folderPath . 'qr_' . $transactionId . '.png';

// Save the image to the file
if (file_put_contents($filePath, $imageData)) {
    // Get the URL of the saved image
    $fileUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $filePath;
    echo json_encode(['success' => true, 'message' => 'QR code saved successfully', 'file_url' => $fileUrl]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save QR code']);
}
?>