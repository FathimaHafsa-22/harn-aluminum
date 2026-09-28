<?php
require_once __DIR__ . '/auth.php';

$success = '';
$error   = '';

$categories = [
    'Partitions', 'Sliding Doors', 'Aluminum Windows',
    'Wall Cupboards', 'Pantry Cupboards', 'Shop Fitting', 'Gypsum Ceilings'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if ($title === '' || $category === '' || $description === '') {
        $error = 'Please fill in all fields.';
    } elseif (!in_array($category, $categories)) {
        $error = 'Invalid category selected.';
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please choose an image to upload.';
    } else {
        $file = $_FILES['image'];

        // Check file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $error = 'Image is too large. Maximum size is 5MB.';
        } else {
            // Verify real image
            $image_info = @getimagesize($file['tmp_name']);
            if ($image_info === false) {
                $error = 'The file is not a valid image.';
            } else {
                $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($image_info['mime'], $allowed)) {
                    $error = 'Only JPG, PNG, WEBP, and GIF images are allowed.';
                } else {
                    // Generate unique filename
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $filename = 'work_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

                    // Upload directory
                    $upload_dir = __DIR__ . '/../uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $target_path = $upload_dir . $filename;
                    $db_path = 'uploads/' . $filename;

                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        // Insert into database
                        $stmt = mysqli_prepare($conn,
                            "INSERT INTO works (title, category, description, image_path) VALUES (?, ?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, 'ssss',
                            $title, $category, $description, $db_path);

                        if (mysqli_stmt_execute($stmt)) {
                            $success = 'Work uploaded successfully!';
                            $title = $category = $description = '';
                        } else {
                            @unlink($target_path); // rollback file
                            $error = 'Database error. Please try again.';
                        }
                    } else {
                        $error = 'Failed to save the uploaded image.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Work — HARN Aluminum Admin</title>
    <link rel="stylesheet" href="/harn-aluminum/assets/css/style.css">
    <style>
        .admin-nav {
            background: #1e3a5f; color: white; padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .admin-nav h1 { font-size: 1.2rem; }
        .admin-nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 0.9rem; }
        .admin-nav a:hover { color: #ff8c00; }
        .admin-container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .page-title { color: #1e3a5f; margin-bottom: 25px; }
        .form-box {
            background: white; padding: 30px; border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        .form-box label {
            display: block; margin-bottom: 8px; font-weight: 600; color: #1e3a5f;
        }
        .form-box input, .form-box select, .form-box textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd;
            border-radius: 5px; font-size: 1rem; font-family: inherit;
            margin-bottom: 20px;
        }
        .form-box textarea { min-height: 100px; resize: vertical; }
        .btn-submit {
            background: #ff8c00; color: white; border: none;
            padding: 12px 30px; font-size: 1rem; font-weight: 600;
            border-radius: 5px; cursor: pointer; width: 100%;
        }
        .btn-submit:hover { background: #e67e00; }
        .alert-success {
            background: #d4edda; color: #155724; padding: 15px;
            border-radius: 8px; margin-bottom: 20px;
        }
        .alert-error {
            background: #f8d7da; color: #721c24; padding: 15px;
            border-radius: 8px; margin-bottom: 20px;
        }
        .preview {
            max-width: 200px; max-height: 200px; border-radius: 8px;
            margin-top: 10px; display: none;
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
        <h2 class="page-title">Upload New Work</h2>

        <?php if ($success): ?>
            <div class="alert-success">
                ✅ <?php echo htmlspecialchars($success); ?>
                <a href="manage-works.php" style="color: #155724; text-decoration: underline; margin-left: 10px;">
                    View all works →
                </a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-box">
            <label>Title *</label>
            <input type="text" name="title" required maxlength="150"
                   placeholder="e.g. Kitchen sliding door — 2 panels"
                   value="<?php echo htmlspecialchars($title ?? ''); ?>">

            <label>Category *</label>
            <select name="category" required>
                <option value="">Select a category...</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo (isset($category) && $category === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Description *</label>
            <textarea name="description" required maxlength="1000"
                      placeholder="Describe the project — materials, size, finish, timeline..."><?php echo htmlspecialchars($description ?? ''); ?></textarea>

            <label>Project Image * (JPG / PNG / WEBP, max 5MB)</label>
            <input type="file" name="image" accept="image/*" required
                   onchange="previewImage(this)">
            <img id="preview" class="preview" alt="Preview">

            <button type="submit" class="btn-submit">Upload Work</button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>