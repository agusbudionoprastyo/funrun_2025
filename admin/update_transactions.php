<?php
include '../helper/db.php'; // Koneksi ke database
header('Content-Type: application/json');

// Ambil data yang dikirim dengan metode POST
$data = json_decode(file_get_contents('php://input'), true);

// Cek apakah data yang diperlukan ada
if (isset($data['transaction_id']) && isset($data['status'])) {
    $transaction_id = $data['transaction_id']; // ID transaksi
    $status = $data['status']; // Status yang baru

    // Update status transaksi di database
    $sql = "UPDATE transactions SET status = ? WHERE transaction_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $transaction_id);

    if ($stmt->execute()) {
        // Cek apakah ada baris yang terpengaruh (berarti status berhasil diubah)
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Status transaksi berhasil diperbarui menjadi verified.']);
        } else {
            echo json_encode(['error' => 'Gagal memperbarui status transaksi.']);
        }
    } else {
        echo json_encode(['error' => 'Terjadi kesalahan saat memperbarui status transaksi.']);
    }

    // Tutup statement
    $stmt->close();
} else {
    // Jika data tidak lengkap
    echo json_encode(['error' => 'ID transaksi atau status tidak ditemukan.']);
}

// Tutup koneksi database
$conn->close();
?>