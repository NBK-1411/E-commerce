<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/perfume_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';

$perfumeController = new PerfumeController();
$categoryController = new CategoryController();

// Get all categories (including those without images)
$allCategories = $categoryController->getAll();
if (!is_array($allCategories)) {
    $allCategories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - Essence</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="nav-left">
                    <button class="menu-toggle" aria-label="Toggle menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
                
                <div class="logo">
                    <h1>ESSENCE</h1>
                    <p class="tagline">Luxury Fragrances</p>
                </div>
                
                <div class="nav-right">
                    <button class="icon-btn" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                    <?php if (is_logged_in()): ?>
                        <a href="cart.php" class="icon-btn cart-btn" aria-label="Cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span class="cart-count">0</span>
                        </a>
                        <a href="../login.php" class="icon-btn" aria-label="Account" title="<?php echo htmlspecialchars(get_current_customer()['customer_name'] ?? 'User'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </a>
                    <?php else: ?>
                        <a href="../login.php" class="icon-btn" aria-label="Account">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </a>
                        <a href="cart.php" class="icon-btn cart-btn" aria-label="Cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span class="cart-count">0</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="nav-links">
                <a href="shop.php">Shop</a>
                <a href="../index.php#collections">Collections</a>
                <a href="../index.php#featured">Bestsellers</a>
                <a href="../index.php#about">About</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="../actions/logout.php">Logout</a>
                        <a href="../admin/category.php">Category</a>
                        <a href="../admin/brand.php">Brand</a>
                        <a href="../admin/perfume.php">Add Product</a>
                    <?php else: ?>
                        <a href="../actions/logout.php">Logout</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="../signup.php">Register</a>
                    <a href="../login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Categories Section -->
    <section class="categories" style="padding: 4rem 0;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">All Categories</h2>
                <p class="section-subtitle">Browse our complete collection of fragrance categories</p>
            </div>
            
            <?php if (empty($allCategories)): ?>
                <div style="text-align: center; padding: 3rem;">
                    <p style="color: #6b7280; font-size: 1.125rem;">No categories available at the moment.</p>
                    <a href="../index.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Home</a>
                </div>
            <?php else: ?>
                <div class="category-grid">
                    <?php foreach ($allCategories as $category): ?>
                        <?php
                        // Get products for this category
                        $categoryProducts = $perfumeController->getByCategory($category['id']);
                        $productCount = is_array($categoryProducts) ? count($categoryProducts) : 0;
                        ?>
                        <a href="shop.php?category=<?php echo $category['id']; ?>" class="category-card">
                            <div class="category-image">
                                <?php if (!empty($category['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php else: ?>
                                    <img src="/placeholder.svg?height=400&width=350" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="category-info">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p style="color: #6b7280; font-size: 0.875rem; margin: 0.5rem 0;">
                                    <?php echo $productCount; ?> <?php echo $productCount === 1 ? 'product' : 'products'; ?>
                                </p>
                                <span class="category-link">Explore Collection →</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="section-footer" style="text-align: center; margin-top: 3rem;">
                <a href="../index.php" class="btn btn-outline">Back to Home</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 class="footer-title">ESSENCE</h3>
                    <p class="footer-description">Crafting luxury fragrances since 1985. Each scent is a masterpiece designed to evoke emotion and create lasting memories.</p>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-heading">Shop</h4>
                    <ul class="footer-links">
                        <li><a href="shop.php">All Perfumes</a></li>
                        <li><a href="shop.php">New Arrivals</a></li>
                        <li><a href="../index.php#featured">Bestsellers</a></li>
                        <li><a href="../index.php#collections">Collections</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-heading">Support</h4>
                    <ul class="footer-links">
                        <li><a href="#contact">Contact Us</a></li>
                        <li><a href="#shipping">Shipping Info</a></li>
                        <li><a href="#returns">Returns</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-heading">Connect</h4>
                    <ul class="footer-links">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#stores">Store Locator</a></li>
                        <li><a href="#careers">Careers</a></li>
                        <li><a href="#sustainability">Sustainability</a></li>
                    </ul>
                    <div class="social-links">
                        <a href="#instagram" aria-label="Instagram">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="none" stroke="white" stroke-width="2"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="white" stroke-width="2"></line>
                            </svg>
                        </a>
                        <a href="#facebook" aria-label="Facebook">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <a href="#pinterest" aria-label="Pinterest">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 12c0-2.21 1.79-4 4-4 1.11 0 2 .89 2 2 0 1.5-1 2-1 3.5" fill="none" stroke="white" stroke-width="2"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Essence Luxury Fragrances. All rights reserved.</p>
                <div class="footer-legal">
                    <a href="#privacy">Privacy Policy</a>
                    <a href="#terms">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="../script.js"></script>
</body>
</html>

