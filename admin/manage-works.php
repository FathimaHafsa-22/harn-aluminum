<?php
require_once __DIR__ . '/auth.php';

$success = '';
$error   = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        // Fetch image path first
        $stmt = mysqli_prepare($conn, "SELECT image_path FROM works WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($row) {
            // Delete DB row
            $del = mysqli_prepare($conn, "DELETE FROM works WHERE id = ?");
            mysqli_stmt_bind_param($del, 'i', $id);

            if (mysqli_stmt_execute($del)) {
                // Delete image file
                $file_path = __DIR__ . '/../' . $row['image_path'];
                if (file_exists($file_path) && strpos($row['image_path'], 'uploads/') === 0) {
                    @unlink($file_path);
                }
                $success = 'Work deleted successfully.';
            } else {
                $error = 'Could not delete the work.';
            }
        }
    }
}

// Fetch all works
$works = mysqli_query($conn, "SELECT * FROM works ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Works — HARN Aluminum Admin</title>
    <link rel="stylesheet" href="/harn-aluminum/assets/css/style.css">
    <style>
        .admin-nav {
            background: #1e3a5f; color: white; padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .admin-nav h1 { font-size: 1.2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 0.9rem; }
        .admin-nav a:hover { color: #ff8c00; }
        .admin-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 25px; flex-wrap: wrap; gap: 15px;
        }
        .page-title { color: #1e3a5f; }
        .btn-add {
            background: #ff8c00; color: white; padding: 10px 22px;
            border-radius: 5px; text-decoration: none; font-weight: 600;
        }
        .btn-add:hover { background: #e67e00; }

        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; }

        .works-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .work-card {
            background: white; border-radius: 10px; overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        .work-card img {
            width: 100%; height: 180px; object-fit: cover; display: block;
        }
        .work-body { padding: 18px; }
        .work-body h3 { color: #1e3a5f; font-size: 1.05rem; margin-bottom: 8px; }
        .work-category {
            display: inline-block; background: #ff8c00; color: white;
            font-size: 0.7rem; padding: 3px 10px; border-radius: 10px;
            margin-bottom: 10px; text-transform: uppercase; font-weight: 600;
        }
        .work-description {
            color: #6c757d; font-size: 0.85rem; margin-bottom: 15px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .work-actions { display: flex; gap: 8px; }
        .btn-sm {
            padding: 7px 14px; border: none; border-radius: 5px;
            cursor: pointer; font-size: 0.8rem; font-weight: 500;
            text-decoration: none; display: inline-block;
        }
        .btn-edit { background: #3b82f6; color: white; }
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
        <div class="page-header">
            <h2 class="page-title">Manage Portfolio Works</h2>
            <a href="upload.php" class="btn-add">➕ Upload New Work</a>
        </div>

        <?php if ($success): ?>
            <div class="alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($works) > 0): ?>
            <div class="works-grid">
                <?php while ($work = mysqli_fetch_assoc($works)): ?>
                    <div class="work-card">
                        <img src="/harn-aluminum/<?php echo htmlspecialchars($work['image_path']); ?>"
                             alt="<?php echo htmlspecialchars($work['title']); ?>">
                        <div class="work-body">
                            <span class="work-category"><?php echo htmlspecialchars($work['category']); ?></span>
                            <h3><?php echo htmlspecialchars($work['title']); ?></h3>
                            <p class="work-description"><?php echo htmlspecialchars($work['description']); ?></p>

                            <div class="work-actions">
                                <a href="edit-work.php?id=<?php echo $work['id']; ?>" 
                                   class="btn-sm btn-edit">✏️ Edit</a>

                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Delete this work and its image permanently?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $work['id']; ?>">
                                    <button type="submit" class="btn-sm btn-delete">🗑 Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 3rem; margin-bottom: 10px;">🖼️</div>
                <p>No works uploaded yet.</p>
                <a href="upload.php" class="btn-add" style="margin-top: 20px; display: inline-block;">
                    Upload Your First Work
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>