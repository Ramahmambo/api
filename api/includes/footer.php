
        </div><!-- container end -->
    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Milan Booster</h3>
                    <p>We provide safe services</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="../pages/index.php">Home</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="../pages/order.php">Order</a></li>
                            <li><a href="../pages/order_details.php">Order Details</a></li>
                        <?php endif; ?>
                        <li><a href="../pages/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Contact Us</h4>
                    <p>
                        <a href="https://wa.me/0110664672" target="_blank" class="whatsapp-link">
                            <i class="fab fa-whatsapp"></i> WhatsApp: 0110664672
                        </a>
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Milan Booster. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/utils.js"></script>
    <?php if (isset($pageScripts) && !empty($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>