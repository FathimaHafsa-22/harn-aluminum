</main>

<footer>
    <div class="container">
        <p><strong>HARN Aluminum</strong> — Custom Aluminum Works & Gypsum Ceilings</p>
        <p>📞 Call or WhatsApp: <a href="https://wa.me/94775651281" target="_blank" rel="noopener">+94 77 565 1281</a></p>
        <p>📍 Serving Colombo & Surrounding Areas</p>
        <p>&copy; <?php echo date('Y'); ?> HARN Aluminum. All rights reserved.</p>
    </div>
</footer>

<?php 
$whatsapp_message = $whatsapp_message ?? 'Hello HARN Aluminum, I would like to inquire about your services.';
include __DIR__ . '/whatsapp.php'; 
?>

</body>
</html>