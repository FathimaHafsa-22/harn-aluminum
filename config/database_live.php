<?php
// HARN Aluminum — LIVE Database Connection
// Server: InfinityFree
define('BASE_URL', '');

define('DB_HOST', 'sql208.infinityfree.com');
define('DB_USER', 'if0_43035628');
define('DB_PASS', 'vV4d6Rp8w55VOn');   
define('DB_NAME', 'if0_43035628_harn_aluminum');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>