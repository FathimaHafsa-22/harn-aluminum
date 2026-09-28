<?php
require_once 'config/database.php';

$page_title = 'Portfolio — HARN Aluminum';

// Get category filter from URL
$active_category = isset($_GET['category']) ? trim($_GET['category']) : 'All';

// Available categories
$categories = [
    'All' => 'All Projects',
    'Partitions' => 'Partitions',
    'Sliding Doors' => 'Sliding Doors',
    'Aluminum Windows' => 'Aluminum Windows',
    'Wall Cupboards' => 'Wall Cupboards',
    'Pantry Cupboards' => 'Pantry Cupboards',
    'Shop Fitting' => 'Shop Fitting',
    'Gypsum Ceilings' => 'Gypsum Ceilings',
];

// Build query based on filter
if ($active_category === 'All' || !array_key_exists($active_category, $categories)) {
    $active_category = 'All';
    $stmt = mysqli_prepare($conn, "SELECT * FROM works ORDER BY created_at DESC");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM works WHERE category = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, 's', $active_category);
}

mysqli_stmt_execute($stmt);
$works = mysqli_stmt_get_result($stmt);

include 'includes/header.php';
?>

<!-- Page Header -->
<section style="background: var(--primary); color: white; padding: 50px 20px; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.3rem; margin-bottom: 10px;">Our Portfolio</h1>
        <p style="opacity: 0.9;">Browse our finished projects — real work, real quality.</p>
    </div>
</section>

<!-- Category Filters -->
<section style="padding: 30px 20px; background: var(--light);">
    <div class="container" style="text-align: center;">
        <?php foreach ($categories as $key => $label): 
            $is_active = ($active_category === $key);
            $url = $key === 'All' ? 'portfolio.php' : 'portfolio.php?category=' . urlencode($key);
        ?>
            <a href="<?php echo $url; ?>" 
               style="display: inline-block; padding: 8px 18px; margin: 5px; border-radius: 20px; text-decoration: none; font-weight: 500; font-size: 0.9rem;
                      background: <?php echo $is_active ? 'var(--primary)' : 'white'; ?>;
                      color: <?php echo $is_active ? 'white' : 'var(--dark)'; ?>;
                      border: 1px solid <?php echo $is_active ? 'var(--primary)' : '#ddd'; ?>;">
                <?php echo htmlspecialchars($label); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Works Grid -->
<section style="padding: 50px 20px;">
    <div class="container">
        <?php if (mysqli_num_rows($works) > 0): ?>
            <div class="category-grid">
                <?php while ($work = mysqli_fetch_assoc($works)): ?>
                    <div class="category-card" style="padding: 0; overflow: hidden; text-align: left;">
                        <img src="<?php echo htmlspecialchars($work['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($work['title']); ?>"
                             style="width: 100%; height: 220px; object-fit: cover; display: block;">
                        <div style="padding: 20px;">
                            <span style="display: inline-block; background: var(--accent); color: white; font-size: 0.75rem; padding: 3px 10px; border-radius: 12px; margin-bottom: 10px;">
                                <?php echo htmlspecialchars($work['category']); ?>
                            </span>
                            <h3 style="color: var(--primary); margin-bottom: 8px;"><?php echo htmlspecialchars($work['title']); ?></h3>
                            <p style="color: var(--grey); font-size: 0.9rem;"><?php echo htmlspecialchars($work['description']); ?></p>
                            
                            <?php 
                            $whatsapp_message = "Hi, I like your work: " . $work['title'] . ". Can you do something similar for me?";
                            include 'includes/whatsapp.php'; 
                            ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: var(--light); border-radius: 10px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">🖼️</div>
                <h2 style="color: var(--primary); margin-bottom: 10px;">No projects to show yet</h2>
                <p style="color: var(--grey);">
                    <?php if ($active_category !== 'All'): ?>
                        No projects in the <strong><?php echo htmlspecialchars($active_category); ?></strong> category yet.
                        <br><a href="portfolio.php" style="color: var(--accent);">View all projects</a>
                    <?php else: ?>
                        Check back soon — new projects are being added!
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Bottom CTA -->
<section style="padding: 60px 20px; background: var(--primary); color: white; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 15px;">Like What You See?</h2>
        <p style="margin-bottom: 25px;">Let's discuss your project — free consultation.</p>
        <?php 
        $whatsapp_message = "Hi HARN Aluminum, I saw your portfolio and I'd like to discuss a project.";
        include 'includes/whatsapp.php'; 
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>