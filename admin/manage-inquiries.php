<?php
require_once __DIR__ . '/auth.php';

// Handle actions (mark as read/replied, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'mark_read' && $id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE inquiries SET status = 'read' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'mark_replied' && $id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE inquiries SET status = 'replied' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'delete' && $id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM inquiries WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }

    header('Location: manage-inquiries.php');
    exit;
}

// Fetch all inquiries
$result = mysqli_query($conn, "SELECT * FROM inquiries ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries — HARN Aluminum Admin</title>
    <link rel="stylesheet" href="/harn-aluminum/assets/css/style.css">
    <style>
        .admin-nav {
            background: #1e3a5f; color: white; padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .admin-nav h1 { font-size: 1.2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 0.9rem; }
        .admin-nav a:hover { color: #ff8c00; }
        .admin-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .page-title { color: #1e3a5f; margin-bottom: 25px; }
        .inquiry-card {
            background: white; border-radius: 10px; padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08); margin-bottom: 20px;
            border-left: 5px solid #6c757d;
        }
        .inquiry-card.status-new { border-left-color: #ff8c00; }
        .inquiry-card.status-read { border-left-color: #3b82f6; }
        .inquiry-card.status-replied { border-left-color: #10b981; }
        .inquiry-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 15px; flex-wrap: wrap; gap: 10px;
        }
        .inquiry-header h3 { color: #1e3a5f; font-size: 1.1rem; }
        .status-badge {
            padding: 4px 12px; border-radius: 12px; font-size: 0.75rem;
            font-weight: 600; text-transform: uppercase;
        }
        .status-new { background: #fff3e0; color: #e67e00; }
        .status-read { background: #e0edff; color: #1e40af; }
        .status-replied { background: #d1fae5; color: #047857; }
        .inquiry-meta {
            color: #6c757d; font-size: 0.85rem; margin-bottom: 12px;
            display: flex; gap: 20px; flex-wrap: wrap;
        }
        .inquiry-message {
            background: #f8f9fa; padding: 15px; border-radius: 8px;
            margin: 15px 0; line-height: 1.6;
        }
        .action-btns { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-sm {
            padding: 7px 15px; border: none; border-radius: 5px;
            cursor: pointer; font-size: 0.85rem; font-weight: 500;
            text-decoration: none; display: inline-block;
        }
        .btn-read { background: #3b82f6; color: white; }
        .btn-replied { background: #10b981; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-sm:hover { opacity: 0.85; }
        .empty-state {
            text-align: center; padding: 60px 20px; background: #f5f7fa;
            border-radius: 10px; color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <h1>HARN Aluminum — Admin</h1>
        <div>
            <a href="dashboard.php">← Dashboard</a>
            <span>👋 <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="admin-container">
        <h2 class="page-title">Customer Inquiries</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="inquiry-card status-<?php echo $row['status']; ?>">
                    <div class="inquiry-header">
                        <h3>📥 <?php echo htmlspecialchars($row['name']); ?></h3>
                        <span class="status-badge status-<?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>

                    <div class="inquiry-meta">
                        <span>📞 <?php echo htmlspecialchars($row['phone']); ?></span>
                        <?php if ($row['email']): ?>
                            <span>✉️ <?php echo htmlspecialchars($row['email']); ?></span>
                        <?php endif; ?>
                        <?php if ($row['service']): ?>
                            <span>🔧 <?php echo htmlspecialchars($row['service']); ?></span>
                        <?php endif; ?>
                        <span>📅 <?php echo date('M j, Y — g:ia', strtotime($row['created_at'])); ?></span>
                    </div>

                    <div class="inquiry-message">
                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                    </div>

                    <div class="action-btns">
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $row['phone']); ?>?text=<?php echo urlencode('Hi ' . $row['name'] . ', thank you for your inquiry about ' . ($row['service'] ?: 'our services') . '!'); ?>"
                           class="btn-sm btn-replied" target="_blank">💬 Reply on WhatsApp</a>

                        <?php if ($row['status'] === 'new'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-sm btn-read">Mark as Read</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($row['status'] !== 'replied'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="mark_replied">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-sm btn-replied">Mark as Replied</button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('Delete this inquiry permanently?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-sm btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 3rem; margin-bottom: 10px;">📭</div>
                <p>No inquiries yet. When customers contact you, they'll appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>