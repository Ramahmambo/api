<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if not logged in
requireLogin();

$pageTitle = 'Contact Us';
require_once '../includes/header.php';
?>

<div class="page-title">
    <h1>Contact Us</h1>
    <p>We're here to help! Reach out to us through WhatsApp</p>
</div>

<div class="contact-options">
    <a href="https://wa.me/0110664672" target="_blank" class="contact-option">
        <div class="contact-icon">
            <i class="fab fa-whatsapp"></i>
        </div>
        <div class="contact-details">
            <h3>WhatsApp Support</h3>
            <p>Chat with us directly: 0110664672</p>
        </div>
    </a>
</div>

<?php
require_once '../includes/footer.php';
?>