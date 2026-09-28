<?php
// Include database (which also starts session)
require_once __DIR__ . '/../config/database.php';

// If not logged in, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>