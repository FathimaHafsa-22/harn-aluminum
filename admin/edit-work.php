<?php
require_once __DIR__ . '/auth.php';

$success = '';
$error   = '';

$categories = [
    'Partitions', 'Sliding Doors', 'Aluminum Windows',
    'Wall Cupboards', 'Pantry Cupboards', 'Shop Fitting', 'Gypsum Ceilings'
];

// Get work ID
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: manage-works.php');
    exit;
}

// Fetch the existing work
$stmt = mysqli_prepare($conn, "SELECT * FROM works WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$work = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$work) {
    header('Location: manage-works.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $category === '' || $description === '') {
        $error = 'Please fill in all fields.';
    } elseif (!in_array($category, $categories)) {
        $error = 'Invalid category selected.';
    } else {
        $new_image_path = $work['image_path']; // keep existing by default
        $old_image_to_delete = null;

        // If a new image was uploaded, validate and save it
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Image upload failed. Please try again.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Image is too large. Maximum size is 5MB.';
            } else {
                $image_info = @getimagesize($file['tmp_name']);
                if ($image_info === false) {
                    $error = 'The file is not a valid image.';
                } else {
                    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                    if (!in_array($image_info['mime'], $allowed)) {
                        $error = 'Only JPG, PNG, WEBP, and GIF images are allowed.';
                    } else {
                        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $filename = 'work_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $upload_dir = __DIR__ . '/../uploads/';
                        $target_path = $upload_dir . $filename;

                        if (move_uploaded_file($file['tmp_name'], $target_path)) {
                            $new_image_path = 'uploads/' . $filename;
                            $old_image_to_delete = $work['image_path']; // remember to delete old
                        } else {
                            $error = 'Failed to save the new image.';
                        }
                    }
                }
            }
        }

        // If everything is valid, update the DB
        if ($error === '') {
            $upd = mysqli_prepare($conn,
                "UPDATE works SET title = ?, category = ?, description = ?, image_path = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd, 'ssssi',
                $title, $category, $description, $new_image_path, $id);

            if (mysqli_stmt_execute($upd)) {
                // Delete old image if it was replaced
                if ($old_image_to_delete && strpos($old_image_to_delete, 'uploads/') === 0) {
                    $old_path = __DIR__ . '/../' . $old_image_to_delete;
                    if (file_exists($old_path)) @unlink($old_path);
                }

                $success = 'Work updated successfully!';
                // Refresh the $work variable
                $work['title'] = $title;
                $work['category'] = $category;
                $work['description'] = $description;
                $work['image_path'] = $new_image_path;
            } else {
                $error = 'Database update failed.';
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
    <title>Edit Work — HARN Aluminum Admin</title>
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
            border-radius: 5px; cursor: pointer;
        }
        .btn-submit:hover { background: #e67e00; }
        .btn-cancel {
            background: #e5e7eb; color: #1e3a5f; padding: 12px 24px;
            border-radius: 5px; text-decoration: none; font-weight: 600;
            margin-left: 10px;
        }
        .alert-success {
            background: #d4edda; color: #155724; padding: 15px;
            border-radius: 8px; margin-bottom: 20px;
        }
        .alert-error {
            background: #f8d7da; color: #721c24; padding: 15px;
            border-radius: 8px; margin-bottom: 20px;
        }
        .current-image {
            max-width: 100%; height: auto; border-radius: 8px;
            margin-bottom: 10px; max-height: 250px; object-fit: cover;
        }
        .image-note {
            color: #6c757d; font-size: 0.85rem;
            margin-bottom: 20px; font-style: italic;
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
        <h2 class="page-title">Edit Work</h2>

        <?php if ($success): ?>
            <div class="alert-success">
                ✅ <?php echo htmlspecialchars($success); ?>
                <a href="manage-works.php" style="color: #155724; text-decoration: underline; margin-left: 10px;">
                    Back to list →
                </a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-box">
            <input type="hidden" name="id" value="<?php echo $work['id']; ?>">

            <label>Current Image</label>
            <img src="/harn-aluminum/<?php echo htmlspecialchars($work['image_path']); ?>"
                 alt="Current image" class="current-image">

            <label>Replace Image (optional)</label>
            <input type="file" name="image" accept="image/*"
                   onchange="previewImage(this)">
            <p class="image-note">Leave blank to keep the current image. Max 5MB. JPG/PNG/WEBP/GIF.</p>
            <img id="preview" style="display:none; max-width: 100%; max-height: 250px;
                 border-radius: 8px; margin-bottom: 20px;">

            <label>Title *</label>
            <input type="text" name="title" required maxlength="150"
                   value="<?php echo htmlspecialchars($work['title']); ?>">

            <label>Category *</label>
            <select name="category" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo $work['category'] === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Description *</label>
            <textarea name="description" required maxlength="1000"><?php echo htmlspecialchars($work['description']); ?></textarea>

            <div>
                <button type="submit" class="btn-submit">Save Changes</button>
                <a href="manage-works.php" class="btn-cancel">Cancel</a>
            </div>
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
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>