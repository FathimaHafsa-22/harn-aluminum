<?php
require_once 'config/database.php';

$page_title = 'Reviews — HARN Aluminum';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $rating  = (int)($_POST['rating'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    // Validation
    if ($name === '' || $message === '' || $rating < 1 || $rating > 5) {
        $error_msg = 'Please fill in your name, message, and rating (1–5 stars).';
    } elseif (strlen($name) > 100 || strlen($message) > 1000) {
        $error_msg = 'Name or message is too long.';
    } else {
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO feedback (name, phone, message, rating, status) VALUES (?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $phone, $message, $rating);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = 'Thank you! Your review has been submitted and is awaiting approval.';
            // Clear form values
            $name = $phone = $message = '';
            $rating = 0;
        } else {
            $error_msg = 'Something went wrong. Please try again.';
        }
    }
}

// Fetch approved feedback
$reviews_query = "SELECT name, message, rating, created_at 
                  FROM feedback 
                  WHERE status = 'approved' 
                  ORDER BY created_at DESC 
                  LIMIT 20";
$reviews_result = mysqli_query($conn, $reviews_query);

include 'includes/header.php';
?>

<!-- Page Header -->
<section style="background: var(--primary); color: white; padding: 50px 20px; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.3rem; margin-bottom: 10px;">Client Reviews</h1>
        <p style="opacity: 0.9;">What our clients say about working with HARN Aluminum.</p>
    </div>
</section>

<!-- Reviews List -->
<section style="padding: 50px 20px;">
    <div class="container">
        <h2 style="text-align: center; color: var(--primary); margin-bottom: 40px;">Recent Reviews</h2>

        <?php if (mysqli_num_rows($reviews_result) > 0): ?>
            <div class="category-grid">
                <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                    <div class="category-card" style="text-align: left;">
                        <div style="color: #ffb400; font-size: 1.2rem; margin-bottom: 10px;">
                            <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $review['rating'] ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <p style="font-style: italic; margin-bottom: 15px;">"<?php echo htmlspecialchars($review['message']); ?>"</p>
                        <p style="font-weight: 600; color: var(--primary);">— <?php echo htmlspecialchars($review['name']); ?></p>
                        <p style="font-size: 0.8rem; color: var(--grey); margin-top: 5px;">
                            <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: var(--light); border-radius: 10px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">💬</div>
                <p style="color: var(--grey);">No reviews yet. Be the first to share your experience!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Submit Review Form -->
<section style="padding: 50px 20px; background: var(--light);">
    <div class="container" style="max-width: 600px;">
        <h2 style="text-align: center; color: var(--primary); margin-bottom: 30px;">Leave a Review</h2>

        <?php if ($success_msg): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.08);">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Your Name *</label>
                <input type="text" name="name" required maxlength="100"
                       value="<?php echo htmlspecialchars($name ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Phone (optional)</label>
                <input type="text" name="phone" maxlength="20"
                       value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Rating *</label>
                <select name="rating" required
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                    <option value="">Select rating...</option>
                    <option value="5" <?php echo (isset($rating) && $rating == 5) ? 'selected' : ''; ?>>★★★★★ Excellent</option>
                    <option value="4" <?php echo (isset($rating) && $rating == 4) ? 'selected' : ''; ?>>★★★★☆ Very good</option>
                    <option value="3" <?php echo (isset($rating) && $rating == 3) ? 'selected' : ''; ?>>★★★☆☆ Good</option>
                    <option value="2" <?php echo (isset($rating) && $rating == 2) ? 'selected' : ''; ?>>★★☆☆☆ Fair</option>
                    <option value="1" <?php echo (isset($rating) && $rating == 1) ? 'selected' : ''; ?>>★☆☆☆☆ Poor</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Your Review *</label>
                <textarea name="message" rows="5" required maxlength="1000"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; font-family: inherit;"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn-primary" style="border: none; cursor: pointer; width: 100%; font-size: 1rem;">
                Submit Review
            </button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>