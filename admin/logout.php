<?php
require_once __DIR__ . '/../config/database.php';

// Clear all session data
$_SESSION = [];
session_destroy();

// Redirect to login
header('Location: login.php');
exit;
?>