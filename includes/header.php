<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = $page_title ?? 'HARN Aluminum';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="/harn-aluminum/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="/harn-aluminum/index.php" class="logo">HARN <span>Aluminum</span></a>
        <ul class="nav-links">
            <li><a href="/harn-aluminum/index.php">Home</a></li>
            <li><a href="/harn-aluminum/portfolio.php">Portfolio</a></li>
            <li><a href="/harn-aluminum/feedback.php">Reviews</a></li>
            <li><a href="/harn-aluminum/about.php">About</a></li>
            <li><a href="/harn-aluminum/contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<main>