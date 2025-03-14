<?php
include '../helper/db.php';

header('Content-Type: application/json');

// Query untuk mendapatkan id, name, description, price, dan couple_price dari tabel items
$sql = "SELECT id, name, description, price, couple_price FROM items WHERE NOW() BETWEEN start_date AND expiry LIMIT 1";
$result = $conn->query($sql);

// Cek apakah ada data
if ($result->num_rows > 0) {
    // Ambil data sebagai array asosiatif
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'couplePrice' => $row['couple_price'], // Include couple price
        ];
    }
    // Kembalikan data dalam format JSON
    echo json_encode($items);
} else {
    // Jika tidak ada data
    echo json_encode([]);
}

// Menutup koneksi
$conn->close();
?>