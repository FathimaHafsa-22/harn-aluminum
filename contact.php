<?php
require_once 'config/database.php';

$page_title = 'Contact — HARN Aluminum';

$success_msg = '';
$error_msg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '' || $message === '') {
        $error_msg = 'Please fill in your name, phone, and message.';
    } elseif (strlen($name) > 100 || strlen($phone) > 20 || strlen($email) > 150 || strlen($message) > 2000) {
        $error_msg = 'One or more fields exceed the allowed length.';
    } else {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO inquiries (name, phone, email, service, message) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssss', $name, $phone, $email, $service, $message);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = 'Thank you! Your message has been received. We will get back to you shortly.';
            $name = $phone = $email = $service = $message = '';
        } else {
            $error_msg = 'Something went wrong. Please try again or contact us on WhatsApp.';
        }
    }
}

// Services list for dropdown
$services = [
    'Partitions', 'Sliding Doors', 'Aluminum Windows',
    'Wall Cupboards', 'Pantry Cupboards', 'Shop Fitting', 'Gypsum Ceilings'
];

include 'includes/header.php';
?>

<!-- Page Header -->
<section style="background: var(--primary); color: white; padding: 50px 20px; text-align: center;">
    <div class="container">
        <h1 style="font-size: 2.3rem; margin-bottom: 10px;">Get In Touch</h1>
        <p style="opacity: 0.9;">Free quotes • Fast response • Quality guaranteed</p>
    </div>
</section>

<!-- Contact Info Cards -->
<section style="padding: 50px 20px;">
    <div class="container">
        <div class="category-grid">
            <!-- WhatsApp Card -->
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">💬</div>
                <h3>WhatsApp Us</h3>
                <p style="color: var(--grey); margin-bottom: 15px;">Fastest way to reach us.</p>
                <?php 
                $whatsapp_message = "Hi HARN Aluminum, I'd like to inquire about your services.";
                include 'includes/whatsapp.php'; 
                ?>
            </div>

            <!-- Phone Card -->
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">📞</div>
                <h3>Call Us</h3>
                <p style="color: var(--grey); margin-bottom: 15px;">Mon–Sat, 8 AM – 6 PM</p>
                <a href="tel:+254712345678" style="color: var(--accent); font-weight: 600; text-decoration: none;">
                    +254 712 345 678
                </a>
            </div>

            <!-- Location Card -->
            <div class="category-card">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">📍</div>
                <h3>Service Area</h3>
                <p style="color: var(--grey);">We serve your city and surrounding areas. Contact us for a free site visit.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section style="padding: 50px 20px; background: var(--light);">
    <div class="container" style="max-width: 700px;">
        <h2 style="text-align: center; color: var(--primary); margin-bottom: 30px;">Send Us a Message</h2>

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
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Phone Number *</label>
                <input type="text" name="phone" required maxlength="20"
                       value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email (optional)</label>
                <input type="email" name="email" maxlength="150"
                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Service Interested In</label>
                <select name="service"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                    <option value="">Select a service...</option>
                    <?php foreach ($services as $svc): ?>
                        <option value="<?php echo htmlspecialchars($svc); ?>"
                                <?php echo (isset($service) && $service === $svc) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($svc); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Your Message *</label>
                <textarea name="message" rows="5" required maxlength="2000"
                          placeholder="Tell us about your project, dimensions, timeline, etc."
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; font-family: inherit;"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn-primary" style="border: none; cursor: pointer; width: 100%; font-size: 1rem;">
                Send Message
            </button>
        </form>
    </div>
</section>

<!-- Final CTA -->
<section style="padding: 60px 20px; background: var(--primary); color: white; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 15px;">Prefer to Chat Directly?</h2>
        <p style="margin-bottom: 25px;">We're one tap away on WhatsApp.</p>
        <?php 
        $whatsapp_message = "Hi HARN Aluminum, I'd like a quote.";
        include 'includes/whatsapp.php'; 
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>