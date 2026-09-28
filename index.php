<?php
require_once 'config/database.php';

$page_title = 'HARN Aluminum — Custom Aluminum Works & Gypsum Ceilings';

// Pull latest 6 works for featured section
$featured_query = "SELECT * FROM works ORDER BY created_at DESC LIMIT 6";
$featured_result = mysqli_query($conn, $featured_query);

include 'includes/header.php';
?>

<!--  HERO  -->
<section class="hero">
    <div class="container">
        <h1>Custom Aluminum Works Done Right</h1>
        <p>Partitions • Sliding Doors • Aluminum Windows • Wall Cupboards • Pantry Cupboards • Shop Fitting • Gypsum Ceilings</p>
        <a href="portfolio.php" class="btn-primary" style="margin-right: 15px;">View Our Work</a>
        <?php 
        $whatsapp_message = "Hi HARN Aluminum, I'd like to get a free quote for aluminum works.";
        include 'includes/whatsapp.php'; 
        ?>
    </div>
</section>

<!--  CATEGORIES  -->
<section class="categories">
    <div class="container">
        <h2>What We Do</h2>
        <div class="category-grid">
            <?php
            $categories = [
                ['name' => 'Partitions',       'icon' => '🚪'],
                ['name' => 'Sliding Doors',    'icon' => '🚪'],
                ['name' => 'Aluminum Windows', 'icon' => '🪟'],
                ['name' => 'Wall Cupboards',   'icon' => '🗄️'],
                ['name' => 'Pantry Cupboards', 'icon' => '🍽️'],
                ['name' => 'Shop Fitting',     'icon' => '🏪'],
                ['name' => 'Gypsum Ceilings',  'icon' => '🏠'],
            ];
            foreach ($categories as $cat):
                $whatsapp_message = "Hi, I'm interested in {$cat['name']}. Please share more details.";
            ?>
                <div class="category-card">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;"><?php echo $cat['icon']; ?></div>
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                    <?php include 'includes/whatsapp.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!--  FEATURED WORKS  -->
<?php if (mysqli_num_rows($featured_result) > 0): ?>
<section class="featured-works" style="padding: 60px 20px;">
    <div class="container">
        <h2 style="text-align:center; font-size:2rem; color: var(--primary); margin-bottom: 40px;">Recent Projects</h2>
        <div class="category-grid">
            <?php while ($work = mysqli_fetch_assoc($featured_result)): ?>
                <div class="category-card">
                    <img src="<?php echo htmlspecialchars($work['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($work['title']); ?>"
                         style="width:100%; height:200px; object-fit:cover; border-radius:8px; margin-bottom:15px;">
                    <h3><?php echo htmlspecialchars($work['title']); ?></h3>
                    <p style="color: var(--grey); font-size: 0.9rem;"><?php echo htmlspecialchars($work['category']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="portfolio.php" class="btn-primary">See All Projects →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!--  WHY CHOOSE US  -->
<section style="padding: 60px 20px; background: var(--light);">
    <div class="container">
        <h2 style="text-align:center; font-size:2rem; color: var(--primary); margin-bottom: 40px;">Why Choose HARN Aluminum</h2>
        <div class="category-grid">
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">✨</div>
                <h3>Quality Craftsmanship</h3>
                <p>Precision cutting, clean finishing, durable materials.</p>
            </div>
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">⏱️</div>
                <h3>On-Time Delivery</h3>
                <p>We respect your time — projects delivered as promised.</p>
            </div>
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">💰</div>
                <h3>Fair Pricing</h3>
                <p>Transparent quotes. No hidden charges. Free site visit.</p>
            </div>
        </div>
    </div>
</section>

<!--  FINAL CTA  -->
<section style="padding: 60px 20px; background: var(--primary); color: white; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 20px;">Ready to Start Your Project?</h2>
        <p style="margin-bottom: 30px; font-size: 1.1rem;">Get a free quote today — no obligation.</p>
        <?php 
        $whatsapp_message = "Hi HARN Aluminum, I'd like to discuss a project.";
        include 'includes/whatsapp.php'; 
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>