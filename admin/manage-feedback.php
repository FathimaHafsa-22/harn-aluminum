<?php
require_once __DIR__ . '/auth.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        if ($action === 'approve') {
            $stmt = mysqli_prepare($conn, "UPDATE feedback SET status = 'approved' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
        } elseif ($action === 'reject') {
            $stmt = mysqli_prepare($conn, "UPDATE feedback SET status = 'rejected' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
        } elseif ($action === 'delete') {
            $stmt = mysqli_prepare($conn, "DELETE FROM feedback WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
        } elseif ($action === 'pending') {
            $stmt = mysqli_prepare($conn, "UPDATE feedback SET status = 'pending' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
        }
    }

    header('Location: manage-feedback.php?filter=' . urlencode($_POST['filter'] ?? 'all'));
    exit;
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$allowed_filters = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter, $allowed_filters)) $filter = 'all';

// Fetch reviews based on filter
if ($filter === 'all') {
    $result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY created_at DESC");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM feedback WHERE status = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $filter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

// Count per status for tabs
$counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
$count_q = mysqli_query($conn, "SELECT status, COUNT(*) AS c FROM feedback GROUP BY status");
while ($row = mysqli_fetch_assoc($count_q)) {
    $counts[$row['status']] = $row['c'];
    $counts['all'] += $row['c'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews — HARN Aluminum Admin</title>
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
        .page-title { color: #1e3a5f; margin-bottom: 20px; }

        .tabs { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
        .tab {
            padding: 8px 18px; border-radius: 20px; text-decoration: none;
            font-size: 0.9rem; font-weight: 500; background: white;
            color: #1e3a5f; border: 1px solid #ddd;
        }
        .tab.active { background: #1e3a5f; color: white; border-color: #1e3a5f; }
        .tab:hover { border-color: #ff8c00; }

        .review-card {
            background: white; border-radius: 10px; padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08); margin-bottom: 20px;
            border-left: 5px solid #6c757d;
        }
        .review-card.status-pending { border-left-color: #ff8c00; background: #fffbf5; }
        .review-card.status-approved { border-left-color: #10b981; }
        .review-card.status-rejected { border-left-color: #ef4444; background: #fdf5f5; }

        .review-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 12px; flex-wrap: wrap; gap: 10px;
        }
        .review-header h3 { color: #1e3a5f; font-size: 1.05rem; }
        .stars { color: #ffb400; font-size: 1.1rem; }

        .status-badge {
            padding: 4px 12px; border-radius: 12px; font-size: 0.75rem;
            font-weight: 600; text-transform: uppercase;
        }
        .status-pending { background: #fff3e0; color: #e67e00; }
        .status-approved { background: #d1fae5; color: #047857; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .review-message {
            background: #f8f9fa; padding: 15px; border-radius: 8px;
            margin: 15px 0; font-style: italic; line-height: 1.6;
        }
        .review-meta { color: #6c757d; font-size: 0.85rem; margin-bottom: 15px; }

        .action-btns { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-sm {
            padding: 7px 15px; border: none; border-radius: 5px;
            cursor: pointer; font-size: 0.85rem; font-weight: 500;
        }
        .btn-approve { background: #10b981; color: white; }
        .btn-reject { background: #ef4444; color: white; }
        .btn-pending { background: #ff8c00; color: white; }
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
        <h2 class="page-title">Client Reviews</h2>

        <!-- Filter Tabs -->
        <div class="tabs">
            <?php foreach (['all', 'pending', 'approved', 'rejected'] as $f): ?>
                <a href="?filter=<?php echo $f; ?>" 
                   class="tab <?php echo $filter === $f ? 'active' : ''; ?>">
                    <?php echo ucfirst($f); ?> (<?php echo $counts[$f]; ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="review-card status-<?php echo $row['status']; ?>">
                    <div class="review-header">
                        <h3>👤 <?php echo htmlspecialchars($row['name']); ?></h3>
                        <span class="status-badge status-<?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>

                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++) echo $i <= $row['rating'] ? '★' : '☆'; ?>
                    </div>

                    <div class="review-message">
                        "<?php echo nl2br(htmlspecialchars($row['message'])); ?>"
                    </div>

                    <div class="review-meta">
                        <?php if ($row['phone']): ?>
                            <span>📞 <?php echo htmlspecialchars($row['phone']); ?></span>
                        <?php endif; ?>
                        <span>📅 <?php echo date('M j, Y — g:ia', strtotime($row['created_at'])); ?></span>
                    </div>

                    <div class="action-btns">
                        <?php if ($row['status'] !== 'approved'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                <button type="submit" class="btn-sm btn-approve">✓ Approve</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($row['status'] !== 'rejected'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                <button type="submit" class="btn-sm btn-reject">✗ Reject</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($row['status'] !== 'pending'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="pending">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                                <button type="submit" class="btn-sm btn-pending">↺ Reset to Pending</button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" style="display:inline;"
                              onsubmit="return confirm('Delete this review permanently?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                            <button type="submit" class="btn-sm btn-reject">🗑 Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 3rem; margin-bottom: 10px;">💬</div>
                <p>No reviews in this category.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>