
<?php
require_once '../includes/header.php';
?>

<div class="not-found-container">
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>The page you are looking for does not exist.</p>
    <a href="../pages/index.php" class="btn">Return to Home</a>
</div>

<style>
    .not-found-container {
        text-align: center;
        padding: 60px 0;
    }
    
    .not-found-container h1 {
        font-size: 120px;
        margin-bottom: 0;
        color: var(--primary-color);
    }
    
    .not-found-container h2 {
        font-size: 36px;
        margin-bottom: 20px;
    }
    
    .not-found-container p {
        font-size: 18px;
        margin-bottom: 30px;
        color: #666;
    }
</style>

<?php
require_once '../includes/footer.php';
?>