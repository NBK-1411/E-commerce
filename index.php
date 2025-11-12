<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
// Don't display errors on live server, but log them
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any errors
ob_start();

try {
    require_once __DIR__ . '/settings/db_cred.php';
    require_once __DIR__ . '/settings/core.php';
    require_once __DIR__ . '/controllers/perfume_controller.php';
    require_once __DIR__ . '/controllers/category_controller.php';
} catch (Exception $e) {
    error_log("Error loading required files in index.php: " . $e->getMessage());
    ob_end_clean();
    die("Error loading page. Please try again later.");
} catch (Error $e) {
    error_log("Fatal error loading required files in index.php: " . $e->getMessage());
    ob_end_clean();
    die("Fatal error loading page. Please contact support.");
}

// Determine the base path for this project (relative to document root)
// This ensures image URLs work correctly regardless of where the project is located
function get_base_path() {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_path = dirname($script_name);
    // If we're at root level, base_path will be '/' or empty, so we need to get the project folder
    if ($base_path === '/' || $base_path === '.' || empty($base_path)) {
        // Get the project folder name from the script directory
        $real_script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
        $real_doc_root = $_SERVER['DOCUMENT_ROOT'];
        $relative_path = str_replace($real_doc_root, '', $real_script_dir);
        return rtrim($relative_path, '/') ?: '';
    }
    return rtrim($base_path, '/') ?: '';
}

$base_path = get_base_path();

try {
    $perfumeController = new PerfumeController();
    $categoryController = new CategoryController();
} catch (Exception $e) {
    error_log("Error creating controllers in index.php: " . $e->getMessage());
    ob_end_clean();
    die("Error initializing page. Please try again later.");
} catch (Error $e) {
    error_log("Fatal error creating controllers in index.php: " . $e->getMessage());
    ob_end_clean();
    die("Fatal error initializing page. Please contact support.");
}

// Safely get data, with error handling
try {
    $featured = $perfumeController->getFeatured(6);
    if (!is_array($featured)) {
        $featured = [];
    }
} catch (Exception $e) {
    $featured = [];
    error_log("Error fetching featured products: " . $e->getMessage());
}

try {
    $allCategories = $categoryController->getAll();
    if (!is_array($allCategories)) {
        $allCategories = [];
    }
    
    // Featured categories with their images - these will be featured on the index page
    $featuredCategoryMap = [
        'Unisex Fragrances' => [
            'image' => 'public/modern-unisex-perfume-bottles-minimalist-design.jpg',
            'search_names' => ['Unisex Fragrances', 'Unisex fragrances', 'Unisex']
        ],
        'Male Fragrances' => [
            'image' => 'public/sophisticated-men-s-cologne-bottles-with-woody-ele.jpg',
            'search_names' => ['Male Fragrances', 'Men\'s Fragrances', 'Men Fragrances', 'Male']
        ],
        'Female Fragrances' => [
            'image' => 'public/elegant-women-s-perfume-bottles-with-floral-elemen.jpg',
            'search_names' => ['Female Fragrances', 'Women\'s Fragrances', 'Women Fragrances', 'Female']
        ]
    ];
    
    // Build featured categories array by matching database categories
    $categories = [];
    foreach ($featuredCategoryMap as $displayName => $config) {
        $found = false;
        foreach ($allCategories as $cat) {
            $catName = $cat['name'] ?? '';
            foreach ($config['search_names'] as $searchName) {
                // Case-insensitive comparison
                if (stripos($catName, $searchName) !== false || stripos($searchName, $catName) !== false) {
                    $categories[] = [
                        'id' => $cat['id'] ?? 0,
                        'name' => $displayName,
                        'image' => $config['image']
                    ];
                    $found = true;
                    break 2; // Break out of both loops
                }
            }
        }
        
        // If category not found in database, still show it with the image (will link to all products)
        if (!$found) {
            $categories[] = [
                'id' => 0,
                'name' => $displayName,
                'image' => $config['image']
            ];
        }
    }
    
} catch (Exception $e) {
    $categories = [];
    $allCategories = [];
    error_log("Error fetching categories: " . $e->getMessage());
}

// Handle welcome message
$welcomeMessage = '';
if (isset($_GET['welcome']) && $_GET['welcome'] == 1 && is_logged_in()) {
    $welcomeMessage = 'Welcome to Essence! Your account has been created successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essence - Luxury Perfume Collection</title>
    <link rel="stylesheet" href="styles.css">
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
                        <a href="public/cart.php" class="icon-btn cart-btn" aria-label="Cart">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span class="cart-count">0</span>
                        </a>
                        <a href="login.php" class="icon-btn" aria-label="Account" title="<?php echo htmlspecialchars(get_current_customer()['customer_name'] ?? 'User'); ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="icon-btn" aria-label="Account">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </a>
                        <a href="public/cart.php" class="icon-btn cart-btn" aria-label="Cart">
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
                <a href="public/shop.php">Shop</a>
                <a href="#collections">Collections</a>
                <a href="#featured">Bestsellers</a>
                <a href="#about">About</a>
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="actions/logout.php">Logout</a>
                        <a href="admin/category.php">Category</a>
                        <a href="admin/brand.php">Brand</a>
                        <a href="admin/perfume.php">Add Product</a>
                    <?php else: ?>
                        <a href="actions/logout.php">Logout</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="signup.php">Register</a>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <?php if ($welcomeMessage): ?>
        <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; color: #065f46; padding: 1rem; max-width: 1400px; margin: 1rem auto; border-radius: 4px;">
            <div style="display: flex; align-items: center;">
                <svg style="width: 20px; height: 20px; margin-right: 0.75rem; color: #10b981;" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p style="margin: 0; font-weight: 500;"><?php echo htmlspecialchars($welcomeMessage); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h2 class="hero-title">Discover Your Signature Scent</h2>
                <p class="hero-description">Explore our curated collection of luxury fragrances crafted from the finest ingredients around the world</p>
                <div class="hero-buttons">
                    <a href="public/shop.php" class="btn btn-primary">Shop Collection</a>
                    <a href="#featured" class="btn btn-secondary">Discover More</a>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="public/luxury-perfume-bottles-on-elegant-marble-surface-w.jpg" alt="Luxury perfume collection">
        </div>
    </section>

    <!-- Categories Section -->
    <section id="about" class="categories">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Find the perfect fragrance for every occasion</p>
            </div>
            
            <div class="category-grid">
                <?php if (empty($categories)): ?>
                    <p class="text-center text-muted" style="grid-column: 1 / -1; padding: 2rem;">No categories with images available at the moment.</p>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <?php 
                        // Build link - if category ID is 0, link to shop page with category name search
                        $categoryLink = $category['id'] > 0 
                            ? "public/shop.php?category=" . $category['id']
                            : "public/shop.php?search=" . urlencode($category['name']);
                        ?>
                        <a href="<?php echo $categoryLink; ?>" class="category-card">
                            <div class="category-image">
                                <?php if (!empty($category['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php else: ?>
                                    <img src="/placeholder.svg?height=400&width=350" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="category-info">
                                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                                <span class="category-link">Explore Collection →</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($allCategories) && count($allCategories) > count($categories)): ?>
                <div class="section-footer" style="text-align: center; margin-top: 2rem;">
                    <a href="public/categories.php" class="btn btn-outline">View More Categories</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Bestsellers</h2>
                <p class="section-subtitle">Our most loved fragrances</p>
            </div>
            
            <div class="product-grid">
                <?php foreach ($featured as $perfume): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($perfume['image'])): ?>
                                <img src="<?php echo htmlspecialchars(normalize_image_path($perfume['image'])); ?>" 
                                     alt="<?php echo htmlspecialchars($perfume['name']); ?>" 
                                     onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($base_path); ?>/placeholder.svg?height=400&width=300';"
                                     style="width: 100%; height: 300px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($base_path); ?>/placeholder.svg?height=400&width=300" 
                                     alt="<?php echo htmlspecialchars($perfume['name']); ?>"
                                     style="width: 100%; height: 300px; object-fit: cover;">
                            <?php endif; ?>
                            <?php if (is_logged_in() && $perfume['stock'] > 0): ?>
                                <button class="quick-add" onclick="addToCart(<?php echo $perfume['id']; ?>)">Quick Add</button>
                            <?php elseif (!is_logged_in()): ?>
                                <a href="login.php" class="quick-add" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($perfume['brand']); ?></span>
                            <h3 class="product-name"><?php echo htmlspecialchars($perfume['name']); ?></h3>
                            <p class="product-price">$<?php echo number_format($perfume['price'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="section-footer">
                <a href="public/shop.php" class="btn btn-outline">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Promotional Banner -->
    <section class="promo-banner">
        <div class="promo-content">
            <div class="promo-text">
                <span class="promo-label">Limited Edition</span>
                <h2 class="promo-title">The Art of Fragrance</h2>
                <p class="promo-description">Introducing our exclusive collection inspired by timeless elegance. Each scent tells a unique story crafted by master perfumers.</p>
                <a href="public/shop.php" class="btn btn-light">Explore Collection</a>
            </div>
            <div class="promo-image">
                <img src="public/luxury-rose-perfume-bottle-elegant-black-design.jpg" alt="Limited Edition Collection">
            </div>
        </div>
    </section>

    <!-- Collections Showcase -->
    <section id="collections" class="collections">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Signature Collections</h2>
                <p class="section-subtitle">Curated selections for every personality</p>
            </div>
            
            <div class="collections-grid">
                <div class="collection-card large">
                    <img src="public/perfume-pictures/floral-symphony-perfume.jpg" alt="Floral Collection">
                    <div class="collection-overlay">
                        <h3>Floral Symphony</h3>
                        <p>Delicate blooms and fresh petals</p>
                        <a href="public/shop.php" class="collection-btn">Discover</a>
                    </div>
                </div>
                
                <div class="collection-card">
                    <img src="public/perfume-pictures/oriental-nights-perfume.jpg" alt="Oriental Collection">
                    <div class="collection-overlay">
                        <h3>Oriental Nights</h3>
                        <p>Rich, warm, and mysterious</p>
                        <a href="public/shop.php" class="collection-btn">Discover</a>
                    </div>
                </div>
                
                <div class="collection-card">
                    <img src="public/perfume-pictures/fresh-citrus-perfume-bottle-modern-minimalist-desi.jpg" alt="Citrus Collection">
                    <div class="collection-overlay">
                        <h3>Citrus Fresh</h3>
                        <p>Bright and invigorating</p>
                        <a href="public/shop.php" class="collection-btn">Discover</a>
                    </div>
                </div>
                
                <div class="collection-card large">
                    <img src="public/perfume-pictures/woody-essence-perfume.jpg" alt="Woody Collection">
                    <div class="collection-overlay">
                        <h3>Woody Essence</h3>
                        <p>Earthy and sophisticated</p>
                        <a href="public/shop.php" class="collection-btn">Discover</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2 class="newsletter-title">Join Our Fragrance Community</h2>
                <p class="newsletter-description">Subscribe to receive exclusive offers, new arrivals, and fragrance tips</p>
                <form class="newsletter-form" method="post" action="subscribe.php">
                    <input type="email" name="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
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
                        <li><a href="public/shop.php">All Perfumes</a></li>
                        <li><a href="public/shop.php">New Arrivals</a></li>
                        <li><a href="#featured">Bestsellers</a></li>
                        <li><a href="#collections">Collections</a></li>
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

    <script src="script.js"></script>
    <script>
        async function addToCart(perfumeId) {
            const formData = new FormData();
            formData.append('perfume_id', perfumeId);
            formData.append('quantity', 1);

            try {
                const response = await fetch('actions/add_to_cart_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        const currentCount = parseInt(cartCount.textContent) || 0;
                        cartCount.textContent = currentCount + 1;
                    }
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
            }
        }
    </script>
</body>
</html>
