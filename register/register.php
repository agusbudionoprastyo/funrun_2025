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

// Mendapatkan transaction ID dari form
$transactionId = $_POST['transactionid'];
// Mengambil data dari form
$registrationType = $_POST['registrationType'];

$name = $_POST['username'];
$size = $_POST['size'];
$mantan = $_POST['mantan'];
$phone = $_POST['phone'];
$voucherCode = $_POST['voucherCode'];
$jerseyColor = $_POST['jerseyColor'];

// Generate username dan password untuk pengguna pertama
$username = generateUsername($name);
$password = generateRandomPassword();

// Mulai transaksi untuk memastikan kedua insert berjalan bersamaan
$conn->begin_transaction();

try {
    // Proses penyimpanan data untuk pengguna pertama
    $query = "INSERT INTO users (transaction_id, name, mantan, size, phone, voucher_code, username, password, jersey_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssss", $transactionId, $name, $mantan, $size, $phone, $voucherCode, $username, $password, $jerseyColor);
    $stmt->execute();

    if ($registrationType === 'couple') {
        $coupleName = $_POST['coupleUsername'];
        $coupleMantan = $_POST['coupleMantan'];
        $coupleSize = $_POST['coupleSize'];
        $coupleJerseyColor = $_POST['coupleJerseyColor'];
        $coupleUsername = generateCoupleUsername($coupleName);
        
        // Proses penyimpanan data untuk pasangan
        $query = "INSERT INTO users (transaction_id, name, mantan, size, username, password, jersey_color) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $transactionId, $coupleName, $coupleMantan, $coupleSize, $coupleUsername, $password, $coupleJerseyColor);
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

    // Voucher code validation and discount calculation
    $discountAmount = 0;
    if (!empty($voucherCode)) {
        // Validasi voucher code (contoh: KOMUNITAS2025, RUNNING2025, dll)
        $validVouchers = ['KOMUNITAS2025', 'RUNNING2025', 'DAFAM2025', 'MANTAN2025'];
        
        if (in_array(strtoupper(trim($voucherCode)), $validVouchers)) {
            $discountAmount = 15000; // Potongan Rp 15.000
        } else {
            throw new Exception('Voucher code tidak valid');
        }
    }

    // Function to calculate price with surcharge for larger sizes
    function calculatePriceWithSurcharge($basePrice, $size) {
        if ($size === '3xl' || $size === '4xl' || $size === '5xl') {
            return $basePrice + 10000; // Add Rp 10,000 surcharge for 3XL, 4XL, 5XL
        }
        return $basePrice;
    }

    // Calculate base price
    $basePrice = ($registrationType === 'couple') ? $couplePrice : $itemPrice;
    
    // Calculate total amount with surcharge for XXXL size
    $totalAmount = $basePrice;
    
    // Add surcharge for first person
    $totalAmount += calculatePriceWithSurcharge($basePrice, $size) - $basePrice;
    
    // If it's a couple registration, also check the couple's size
    if ($registrationType === 'couple') {
        $coupleSize = $_POST['coupleSize'];
        $totalAmount += calculatePriceWithSurcharge($basePrice, $coupleSize) - $basePrice; // Add surcharge for couple's size if needed
    }
    
    // Apply voucher discount
    $totalAmount = max(0, $totalAmount - $discountAmount);

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
    $response = [
        'status' => 'success', 
        'message' => 'Form submitted successfully',
        'discount_applied' => $discountAmount > 0,
        'discount_amount' => $discountAmount,
        'original_amount' => $totalAmount + $discountAmount,
        'final_amount' => $totalAmount
    ];
    echo json_encode($response);
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