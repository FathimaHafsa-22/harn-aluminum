<?php

// WhatsApp Contact Button
// Change $whatsapp_number to the owner's real number
// Format: country code + number, no + or spaces
// Example Kenya: 254712345678


$whatsapp_number = '254712345678';

$whatsapp_message = $whatsapp_message ?? 'Hello HARN Aluminum, I would like to inquire about your services.';
$encoded_message = urlencode($whatsapp_message);
?>

<a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $encoded_message; ?>"
   class="whatsapp-float"
   target="_blank"
   rel="noopener"
   title="Chat with us on WhatsApp">
   💬
</a>