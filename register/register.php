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

// Generate username
$username = generateUsername($name);
$password = generateRandomPassword();
// Proses penyimpanan data ke database
$query = "INSERT INTO users (name, mantan, size, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssissss", $name, $mantan, $size, $phone, $email, $username, $password);
$stmt->execute();
$maleUserId = $stmt->insert_id;

if ($registrationType === 'couple') {
    $coupleName = $_POST['coupleUsername'];
    $coupleMantan = $_POST['coupleMantan'];
    $coupleSize = $_POST['coupleSize'];
    $coupleUsername = generateCoupleUsername($coupleName);
}

if ($registrationType === 'couple') {
    // Simpan pasangan ke tabel 'users' dengan foto pasangan
    $query = "INSERT INTO users (name, mantan, size, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssissss", $coupleName, $coupleMantan, $coupleSize, $phone, $email, $coupleusername, $password);
    $stmt->execute();
    $femaleUserId = $stmt->insert_id; // ID pasangan (female)
}

// Mengambil harga item yang aktif dan memiliki stok lebih dari 0
$query = "SELECT price FROM items WHERE active = '1' AND stock > 0 LIMIT 1"; // Ambil satu item
$stmt = $conn->prepare($query);
$stmt->execute();
$stmt->bind_result($itemPrice);
$stmt->fetch();
$stmt->close();

// Jika tidak ada item yang aktif, tampilkan error
if (!$itemPrice) {
    echo json_encode(['status' => 'error', 'message' => 'Item tidak tersedia']);
    exit;
}

// Jika pendaftaran adalah pasangan, kalikan harga item dengan 2
$totalAmount = ($registrationType === 'couple') ? $itemPrice * 1 : $itemPrice;

// Mendapatkan transaction ID dari form
$transactionId = $_POST['transactionid'];

$query = "INSERT INTO transactions (transaction_id, user_id, total_amount, status) VALUES (?, ?, ?, 'pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $transactionId, $maleUserId, $totalAmount);
$stmt->execute();

// Menutup koneksi
echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
$stmt->close();
$conn->close();
?>