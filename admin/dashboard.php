<?php
require_once __DIR__ . '/auth.php';

// Get counts for stats
$works_count     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM works"))['c'];
$feedback_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM feedback WHERE status = 'pending'"))['c'];
$inquiries_new   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM inquiries WHERE status = 'new'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — HARN Aluminum Admin</title>
    <link rel="stylesheet" href="/harn-aluminum/assets/css/style.css">
    <style>
        .admin-nav {
            background: #1e3a5f;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav h1 { font-size: 1.2rem; }
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 0.9rem;
        }
        .admin-nav a:hover { color: #ff8c00; }
        .admin-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border-left: 5px solid #ff8c00;
        }
        .stat-card h3 {
            font-size: 2.2rem;
            color: #1e3a5f;
            margin-bottom: 5px;
        }
        .stat-card p {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .action-btn {
            display: block;
            padding: 20px;
            background: white;
            border-radius: 10px;
            text-decoration: none;
            color: #1e3a5f;
            font-weight: 600;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.2s;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            color: #ff8c00;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <h1>HARN Aluminum — Admin</h1>
        <div>
            <span>👋 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2 style="color: #1e3a5f; margin-bottom: 25px;">Dashboard Overview</h2>

        <div class="stat-grid">
            <div class="stat-card">
                <h3><?php echo $works_count; ?></h3>
                <p>Portfolio Works</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $feedback_pending; ?></h3>
                <p>Pending Reviews</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $inquiries_new; ?></h3>
                <p>New Inquiries</p>
            </div>
        </div>

        <h2 style="color: #1e3a5f; margin-bottom: 20px;">Quick Actions</h2>
        <div class="quick-actions">
            <a href="upload.php" class="action-btn">➕ Upload New Work</a>
            <a href="manage-works.php" class="action-btn">🖼️ Manage Works</a>
            <a href="manage-feedback.php" class="action-btn">💬 Manage Reviews</a>
            <a href="manage-inquiries.php" class="action-btn">📥 View Inquiries</a>
        </div>
    </div>
</body>
</html>