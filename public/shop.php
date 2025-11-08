<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/perfume_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';

$perfumeController = new PerfumeController();
$categoryController = new CategoryController();

$categories = $categoryController->getAll();
$selected_category = $_GET['category'] ?? null;

if ($selected_category) {
    $perfumes = $perfumeController->getByCategory($selected_category);
} else {
    $perfumes = $perfumeController->getAll();
}

// Get base path for error handling
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);
if ($base_path === '/' || $base_path === '.' || empty($base_path)) {
    $real_script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $real_doc_root = $_SERVER['DOCUMENT_ROOT'];
    $relative_path = str_replace($real_doc_root, '', $real_script_dir);
    $base_path = rtrim($relative_path, '/') ?: '';
} else {
    // Go up to find project root
    $script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    $project_root = '';
    $current_dir = $script_dir;
    while ($current_dir !== $doc_root && $current_dir !== dirname($current_dir)) {
        if (file_exists($current_dir . '/index.php')) {
            $project_root = str_replace($doc_root, '', $current_dir);
            break;
        }
        $current_dir = dirname($current_dir);
    }
    $base_path = rtrim($project_root, '/') ?: '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Essence</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .shop-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .shop-header {
            margin-bottom: 2rem;
        }
        .shop-title {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            font-weight: 400;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        .shop-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 2rem;
        }
        .categories-sidebar {
            background: var(--color-surface);
            padding: 1.5rem;
            border-radius: 8px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .categories-title {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }
        .category-link-item {
            display: block;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            color: var(--color-text);
            text-decoration: none;
            border-radius: 4px;
            transition: var(--transition);
        }
        .category-link-item:hover {
            background: var(--color-border);
        }
        .category-link-item.active {
            background: var(--color-secondary);
            color: white;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .product-card {
            background: var(--color-background);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            overflow: hidden;
            transition: var(--transition);
        }
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .product-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            background: var(--color-surface);
        }
        .product-image-placeholder {
            width: 100%;
            height: 300px;
            background: var(--color-surface);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-text-light);
        }
        .product-info {
            padding: 1.5rem;
        }
        .product-name {
            font-family: var(--font-heading);
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }
        .product-brand {
            font-size: 0.875rem;
            color: var(--color-text-light);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .product-description {
            font-size: 0.875rem;
            color: var(--color-text-light);
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .product-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .product-price {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--color-secondary);
        }
        .product-stock {
            font-size: 0.875rem;
            color: var(--color-text-light);
        }
        .product-btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: block;
            text-align: center;
        }
        .product-btn-primary {
            background: var(--color-primary);
            color: white;
        }
        .product-btn-primary:hover {
            background: var(--color-secondary);
        }
        .product-btn-disabled {
            background: var(--color-border);
            color: var(--color-text-light);
            cursor: not-allowed;
        }
        .product-btn-secondary {
            background: var(--color-border);
            color: var(--color-text);
        }
        .product-btn-secondary:hover {
            background: var(--color-text-light);
            color: white;
        }
        @media (max-width: 968px) {
            .shop-layout {
                grid-template-columns: 1fr;
            }
            .categories-sidebar {
                position: static;
            }
        }
    </style>
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

    <!-- Shop Content -->
    <div class="shop-container">
        <div class="shop-header">
            <h1 class="shop-title">Our Collection</h1>
        </div>

        <div class="shop-layout">
            <!-- Categories Sidebar -->
            <aside class="categories-sidebar">
                <h3 class="categories-title">Categories</h3>
                <a href="shop.php" class="category-link-item <?php echo !$selected_category ? 'active' : ''; ?>">
                    All Perfumes
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="shop.php?category=<?php echo $cat['id']; ?>" 
                       class="category-link-item <?php echo $selected_category == $cat['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </aside>

            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (empty($perfumes)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                        <p style="color: var(--color-text-light); font-size: 1.125rem;">No products found.</p>
                        <a href="shop.php" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">View All Products</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($perfumes as $perfume): ?>
                        <div class="product-card">
                            <?php if (!empty($perfume['image'])): ?>
                                <?php 
                                $raw_path = $perfume['image'];
                                // If it's already a full URL (http/https), use as-is
                                // Otherwise prepend '../' for relative paths
                                if (preg_match('/^https?:\/\//i', $raw_path)) {
                                    $normalized_path = $raw_path;
                                } else {
                                    $normalized_path = '../' . $raw_path;
                                }
                                // Debug: uncomment to see paths
                                // echo "<!-- Raw: " . htmlspecialchars($raw_path) . " | Normalized: " . htmlspecialchars($normalized_path) . " -->";
                                ?>
                                <img src="<?php echo htmlspecialchars($normalized_path); ?>" 
                                     alt="<?php echo htmlspecialchars($perfume['name']); ?>" 
                                     class="product-image"
                                     onerror="console.error('Image failed to load:', '<?php echo htmlspecialchars($normalized_path); ?>'); this.onerror=null; this.src='<?php echo htmlspecialchars($base_path); ?>/placeholder.svg?height=400&width=300';">
                            <?php else: ?>
                                <div class="product-image-placeholder">
                                    <span>No image</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($perfume['name']); ?></h3>
                                <p class="product-brand"><?php echo htmlspecialchars($perfume['brand'] ?? 'Unbranded'); ?></p>
                                
                                <?php if (!empty($perfume['description'])): ?>
                                    <p class="product-description">
                                        <?php echo htmlspecialchars(substr($perfume['description'], 0, 100)); ?>
                                        <?php echo strlen($perfume['description']) > 100 ? '...' : ''; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="product-price-row">
                                    <span class="product-price">$<?php echo number_format($perfume['price'], 2); ?></span>
                                    <span class="product-stock"><?php echo $perfume['stock'] ?? 0; ?> in stock</span>
                                </div>
                                
                                <?php if (is_logged_in() && ($perfume['stock'] ?? 0) > 0): ?>
                                    <button onclick="addToCart(<?php echo $perfume['id']; ?>)" 
                                            class="product-btn product-btn-primary">
                                        Add to Cart
                                    </button>
                                <?php elseif (!is_logged_in()): ?>
                                    <a href="../login.php" class="product-btn product-btn-secondary">
                                        Login to Buy
                                    </a>
                                <?php else: ?>
                                    <button disabled class="product-btn product-btn-disabled">
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

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
    <script>
        async function addToCart(perfumeId) {
            const formData = new FormData();
            formData.append('perfume_id', perfumeId);
            formData.append('quantity', 1);

            try {
                const response = await fetch('../actions/add_to_cart_action.php', {
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
                    alert('Added to cart!');
                } else {
                    alert(data.message || 'Failed to add to cart');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                alert('Error adding to cart');
            }
        }
    </script>
</body>
</html>
