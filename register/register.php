<?php
// Mengimpor koneksi database
include('../helper/db.php');

function generateRandomPassword() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT); // Menghasilkan password 6 digit dengan padding jika kurang
}

// Fungsi untuk generate username
function generateUsername($name) {
    // Ambil 3 huruf pertama dari nama
    $usernameBase = substr($name, 0, 3);
    // Ubah menjadi huruf kecil
    $usernameBase = strtolower($usernameBase);
    // Tambahkan 3 angka acak
    $randomNumbers = rand(100, 999);
    return $usernameBase . $randomNumbers;
}

// Fungsi untuk generate username pasangan
function generateCoupleUsername($coupleName) {
    // Ambil 3 huruf pertama dari nama
    $usernameBase = substr($coupleName, 0, 3);
    // Ubah menjadi huruf kecil
    $usernameBase = strtolower($usernameBase);
    // Tambahkan 3 angka acak
    $randomNumbers = rand(100, 999);
    return $usernameBase . $randomNumbers;
}

// Mengambil data dari form
$registrationType = $_POST['registrationType'];

$name = $_POST['username'];
$size = $_POST['size'];
$mantan = $_POST['mantan'];
$phone = $_POST['phone'];
$email = $_POST['email'];

// Generate username dan password untuk pengguna pertama
$username = generateUsername($name);
$password = generateRandomPassword();

// Mulai transaksi untuk memastikan kedua insert berjalan bersamaan
$conn->begin_transaction();

try {
    // Proses penyimpanan data untuk pengguna pertama
    $query = "INSERT INTO users (transaction_id, name, mantan, size, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $transactionId, $name, $mantan, $size, $phone, $email, $username, $password);
    $stmt->execute();

    if ($registrationType === 'couple') {
        $coupleName = $_POST['coupleUsername'];
        $coupleMantan = $_POST['coupleMantan'];
        $coupleSize = $_POST['coupleSize'];
        $coupleUsername = generateCoupleUsername($coupleName);
        
        // Proses penyimpanan data untuk pasangan
        $query = "INSERT INTO users (transaction_id, name, mantan, size, username, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $transactionId, $coupleName, $coupleMantan, $coupleSize, $coupleUsername, $password);
        $stmt->execute();
    }

    // Mengambil harga item yang aktif dan memiliki stok lebih dari 0
    $query = "SELECT id, price, couple_price, stock FROM items WHERE NOW() BETWEEN start_date AND expiry LIMIT 1"; // Ambil satu item
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bind_result($itemId, $itemPrice, $couplePrice, $itemStock);
    $stmt->fetch();
    $stmt->close();

    // Jika tidak ada item yang aktif, tampilkan error
    if (!$itemPrice) {
        throw new Exception('Item tidak tersedia');
    }

    // Jika pendaftaran adalah pasangan, kalikan harga item dengan 2 dan kurangi stok 2 kali
    $totalAmount = ($registrationType === 'couple') ? $couplePrice : $itemPrice;

    // Mendapatkan transaction ID dari form
    $transactionId = $_POST['transactionid'];

    // Proses penyimpanan transaksi
    $query = "INSERT INTO transactions (transaction_id, total_amount, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $transactionId, $totalAmount);
    $stmt->execute();

    // Update stok item di tabel items
    if ($itemStock > 0) {
        // Jika pendaftaran adalah pasangan, kurangi stok 2 kali
        $newStock = ($registrationType === 'couple') ? $itemStock - 2 : $itemStock - 1;
        
        if ($newStock < 0) {
            throw new Exception('Stok item tidak mencukupi');
        }

        // Update stok di database
        $query = "UPDATE items SET stock = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $newStock, $itemId);
        $stmt->execute();
    } else {
        throw new Exception('Stok item tidak mencukupi');
    }

    // Commit transaksi
    $conn->commit();

    // Menutup koneksi
    echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
} catch (Exception $e) {
    // Jika ada error, rollback transaksi
    $conn->rollback();

    // Menampilkan error
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    // Menutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>
