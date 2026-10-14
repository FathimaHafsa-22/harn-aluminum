<?php
require_once 'config/database.php';

$page_title = 'About Us — HARN Aluminum';

include 'includes/header.php';
?>

<!-- Page Header -->
<section style="background: var(--primary); color: white; padding: 60px 20px; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.4rem; margin-bottom: 15px;">20+ Years of Crafting Quality Aluminum Works</h1>
        <p style="opacity: 0.9; max-width: 700px; margin: 0 auto; font-size: 1.05rem;">
            Trusted craftsmanship for homes, shops, and offices across Colombo and beyond.
        </p>
    </div>
</section>

<!-- Story Section -->
<section style="padding: 60px 20px;">
    <div class="container" style="max-width: 900px;">
        <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 20px; text-align: center;">Our Story</h2>
        <p style="font-size: 1.05rem; line-height: 1.8; color: #333; margin-bottom: 20px;">
            <strong>HARN Aluminum</strong> has been serving clients across Colombo and surrounding areas for over
            <strong>two decades</strong>. With more than <strong>100 completed projects</strong>, we've built our
            reputation on neat workmanship, creative solutions, and dependable service.
        </p>
        <p style="font-size: 1.05rem; line-height: 1.8; color: #333; margin-bottom: 20px;">
            Whether it's a sliding door for a family home, custom wall cupboards for a kitchen, or a full shop
            fit-out, every project receives the same attention to detail. We take pride in finishing on time,
            staying within budget, and leaving every site clean.
        </p>
        <p style="font-size: 1.05rem; line-height: 1.8; color: #333;">
            While we're based in Colombo, we're happy to travel for the right project. If you have a design in
            mind — or just an idea — we'll work with you to bring it to life.
        </p>
    </div>
</section>

<!-- Why Choose Us -->
<section style="padding: 60px 20px; background: var(--light);">
    <div class="container">
        <h2 style="text-align: center; color: var(--primary); font-size: 1.8rem; margin-bottom: 40px;">Why Choose HARN Aluminum</h2>
        <div class="category-grid">
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">🏆</div>
                <h3>20+ Years Experience</h3>
                <p>Trusted craftsmanship built over two decades of hands-on work.</p>
            </div>
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">🎨</div>
                <h3>Neat & Creative</h3>
                <p>Every project finished with precision, care, and a creative touch.</p>
            </div>
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">🚗</div>
                <h3>Colombo & Beyond</h3>
                <p>Based in Colombo — willing to travel for the right project.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Recap -->
<section style="padding: 60px 20px;">
    <div class="container" style="max-width: 800px;">
        <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 30px; text-align: center;">What We Do</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
            <?php
            $services = [
                'Partitions',
                'Sliding Doors',
                'Aluminum Windows',
                'Wall Cupboards',
                'Pantry Cupboards',
                'Shop Fitting',
                'Gypsum Ceilings'
            ];
            foreach ($services as $service):
            ?>
                <div style="background: var(--light); padding: 15px 20px; border-radius: 8px; border-left: 4px solid var(--accent); font-weight: 500; color: var(--primary);">
                    ✓ <?php echo htmlspecialchars($service); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="padding: 60px 20px; background: var(--primary); color: white; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 15px;">Let's Build Something Together</h2>
        <p style="margin-bottom: 25px; font-size: 1.05rem;">Free consultation • Fast quote • Quality guaranteed</p>
        <?php 
        $whatsapp_message = "Hi HARN Aluminum, I'd like to discuss a project.";
        include 'includes/whatsapp.php'; 
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>