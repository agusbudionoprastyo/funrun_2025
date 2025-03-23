<?php
// Menampilkan error untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include koneksi ke database
include('../helper/db.php'); 

// Mendapatkan kode dari URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];
} else {
    die('No authorization code received.');
}

// Menyiapkan data untuk POST request ke Strava API untuk mendapatkan access token
$client_id = '152874';  // Ganti dengan Client ID Anda
$client_secret = '8f08879c914efa7601a65c63967bfbc6d3a4e8c3';  // Ganti dengan Client Secret Anda
$redirect_uri = 'https://funrun.dafam.cloud/strava/callback.php';  // Ganti dengan Redirect URI Anda

// URL untuk tukar kode otorisasi menjadi access token
$url = 'https://www.strava.com/oauth/token';

// Membuat request ke Strava API untuk menukarkan kode otorisasi dengan access token
$data = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'code' => $code,
    'grant_type' => 'authorization_code'
];

// Inisialisasi cURL untuk request POST
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// Eksekusi request dan ambil respon
$response = curl_exec($ch);
if(curl_errno($ch)) {
    die('Error:' . curl_error($ch));
}
curl_close($ch);

// Decode JSON response
$response_data = json_decode($response, true);

// Cek apakah token diterima
if (isset($response_data['access_token'])) {
    $access_token = $response_data['access_token'];
    $strava_id = $response_data['athlete']['id'];
    $athlete_name = $response_data['athlete']['firstname'] . ' ' . $response_data['athlete']['lastname'];

    // Langkah 2: Menyimpan Access Token ke Database dengan Prepared Statements
    $stmt = $conn->prepare("INSERT INTO strava_users (strava_id, access_token, name) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE access_token = ?");
    
    // Binding parameter untuk prepared statement
    $stmt->bind_param("ssss", $strava_id, $access_token, $athlete_name, $access_token);

    // Menjalankan query
    if ($stmt->execute()) {
        echo "Data berhasil disimpan atau diperbarui!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Menutup statement
    $stmt->close();
} else {
    echo "Gagal mendapatkan access token.";
}

// Menutup koneksi
$conn->close();
?>
