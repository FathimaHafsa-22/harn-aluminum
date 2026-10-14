<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/harn-aluminum');
}
$page_title = $page_title ?? 'HARN Aluminum';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="<?php echo BASE_URL; ?>/index.php" class="logo">HARN <span>Aluminum</span></a>
        <ul class="nav-links">
            <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/portfolio.php">Portfolio</a></li>
            <li><a href="<?php echo BASE_URL; ?>/feedback.php">Reviews</a></li>
            <li><a href="<?php echo BASE_URL; ?>/about.php">About</a></li>
            <li><a href="<?php echo BASE_URL; ?>/contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<main>