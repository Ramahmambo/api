<?php
require_once '../includes/header.php';
?>

<section class="hero-section">
    <div class="hero-content">
        <h1>Welcome to Milan Booster</h1>
        <p>We provide safe and reliable social media boosting services</p>
        <?php if (!isLoggedIn()): ?>
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-login">Login</a>
                <a href="register.php" class="btn btn-signup">Sign Up</a>
                <!--<a href="admin_register.php" class="btn btn-signup">admin Sign Up</a>-->
            </div>
        <?php else: ?>
            <div class="hero-buttons">
                <a href="order.php" class="btn">Place an Order</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="features-section">
    <div class="section-title">
        <h2>Our Services</h2>
        <p>Boost your social media presence with our premium services</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fab fa-youtube"></i>
            </div>
            <h3>YouTube</h3>
            <p>Increase your views, subscribers and likes on YouTube</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fab fa-facebook"></i>
            </div>
            <h3>Facebook</h3>
            <p>Grow your Facebook page with more views, likes and followers</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fab fa-tiktok"></i>
            </div>
            <h3>TikTok</h3>
            <p>Boost your TikTok profile with views, likes and followers</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fab fa-instagram"></i>
            </div>
            <h3>Instagram</h3>
            <p>Get more Instagram followers, likes and views</p>
        </div>
    </div>
</section>

<section class="why-choose-us">
    <div class="section-title">
        <h2>Why Choose Us</h2>
        <p>We are committed to providing the best service</p>
    </div>
    <div class="benefits-grid">
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Safe & Secure</h3>
            <p>Our services are 100% safe and secure for your accounts</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h3>Fast Delivery</h3>
            <p>Get your orders completed quickly and efficiently</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-headset"></i>
            </div>
            <h3>24/7 Support</h3>
            <p>Our team is always available to assist you</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h3>Affordable Prices</h3>
            <p>Quality services at competitive prices</p>
        </div>
    </div>
</section>

<style>
    .hero-section {
        text-align: center;
        padding: 80px 0;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        border-radius: 8px;
        margin-bottom: 40px;
    }
    
    .hero-content h1 {
        font-size: 42px;
        margin-bottom: 20px;
    }
    
    .hero-content p {
        font-size: 18px;
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .hero-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .section-title h2 {
        color: var(--primary-color);
        font-size: 32px;
        margin-bottom: 10px;
    }
    
    .features-grid, .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 60px;
    }
    
    .feature-card, .benefit-card {
        background-color: var(--light-color);
        border-radius: 8px;
        padding: 25px;
        text-align: center;
        box-shadow: var(--box-shadow);
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover, .benefit-card:hover {
        transform: translateY(-5px);
    }
    
    .feature-icon, .benefit-icon {
        font-size: 40px;
        margin-bottom: 20px;
        color: var(--primary-color);
    }
    
    .feature-card h3, .benefit-card h3 {
        margin-bottom: 15px;
        font-size: 22px;
    }
    
    .why-choose-us {
        margin-bottom: 60px;
    }
    
    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 32px;
        }
        
        .features-grid, .benefits-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
require_once '../includes/footer.php';
?>