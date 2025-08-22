<?php
header('Content-Type: application/json');

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'get_data' => $_GET,
    'referrer_code' => $_POST['referrer_code'] ?? 'NOT_FOUND',
    'all_post_keys' => array_keys($_POST),
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
