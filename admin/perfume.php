<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';

// Lab requirement: Check if user is logged in
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit;
}

// Lab requirement: Check if user is admin, if not redirect to login
if (!is_admin()) {
    header('Location: ../login.php');
    exit;
}

$user = get_current_customer();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="../admin-styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <h1>ESSENCE</h1>
            <p class="admin-label">Admin Panel</p>
        </div>
        
        <nav class="admin-nav">
            <a href="../admin.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Dashboard</span>
            </a>
            
            <a href="perfume.php" class="admin-nav-item active">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Products</span>
            </a>
            
            <a href="bulk_upload.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>Bulk Upload</span>
            </a>
            
            <a href="category.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Categories</span>
            </a>
            
            <a href="brand.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <span>Brands</span>
            </a>
        </nav>
        
        <div class="admin-sidebar-footer">
            <a href="../index.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>View Store</span>
            </a>
            <a href="../actions/logout.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Bar -->
        <header class="admin-header">
            <div class="admin-header-left">
                <button class="admin-menu-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h2 class="admin-page-title">Product Management</h2>
            </div>
            
            <div class="admin-header-right">
                <button id="addProductBtn" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Product
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="admin-content product-layout">
            <!-- Left: Products Table -->
            <div class="products-table-section">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title" id="productsCount">All Products (0)</h3>
                    </div>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Notes</th>
                                    <th>Badge</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="perfumesTable">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Add Product Form -->
            <div class="add-product-form-section" id="addProductFormSection">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div>
                            <h3 class="admin-card-title">Add New Product</h3>
                            <p class="form-subtitle">Fill in the details to add a new perfume.</p>
                        </div>
                        <button class="modal-close" id="closeFormBtn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="addPerfumeForm" class="modal-form" enctype="multipart/form-data">
                        <!-- Lab requirement: Product ID is autogenerated (hidden) -->
                        <input type="hidden" id="productId" name="id" value="">
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col full-width">
                                <label for="name" class="modal-label">
                                    Product Title <span class="text-red-500">*</span>
                                    <input type="text" id="name" name="name" placeholder="e.g., Velvet Rose Noir" required class="modal-input">
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col full-width">
                                <label class="modal-label">
                                    Categories <span class="text-red-500">*</span>
                                    <div id="categoryCheckboxes" class="category-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #e5e5e5; border-radius: 4px; padding: 0.75rem; background: #f8f7f5;">
                                        <div style="text-align: center; color: #6b6b6b; padding: 1rem;">Loading categories...</div>
                                    </div>
                                    <small class="text-muted" style="display: block; margin-top: 0.5rem;">Select one or more categories for this product</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col">
                                <label for="brandSelect" class="modal-label">
                                    Brand <span class="text-red-500">*</span>
                                    <select id="brandSelect" name="brand_id" required class="modal-input modal-select">
                                        <option value="">Select brand</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col">
                                <label for="price" class="modal-label">
                                    Product Price ($) <span class="text-red-500">*</span>
                                    <input type="number" id="price" name="price" placeholder="128.00" step="0.01" min="0" required class="modal-input">
                                </label>
                            </div>
                            
                            <div class="modal-form-col">
                                <label for="original_price" class="modal-label">
                                    Original Price ($) (Optional)
                                    <input type="number" id="original_price" name="original_price" placeholder="195.00" step="0.01" min="0" class="modal-input">
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col full-width">
                                <label for="description" class="modal-label">
                                    Product Description
                                    <textarea id="description" name="description" rows="4" placeholder="Enter product description..." class="modal-input"></textarea>
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col">
                                <label for="notes" class="modal-label">
                                    Fragrance Notes (Optional)
                                    <input type="text" id="notes" name="notes" placeholder="e.g., Rose, Oud, Amber" class="modal-input">
                                </label>
                            </div>
                            
                            <div class="modal-form-col">
                                <label for="keyword" class="modal-label">
                                    Product Keyword (Optional)
                                    <input type="text" id="keyword" name="keyword" placeholder="e.g., luxury, premium" class="modal-input">
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col">
                                <label for="badge" class="modal-label">
                                    Badge (Optional)
                                    <input type="text" id="badge" name="badge" placeholder="e.g., Sale, New, Popular" class="modal-input">
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col full-width">
                                <label for="image" class="modal-label">
                                    Product Image <span class="text-red-500">*</span>
                                    <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="modal-input">
                                    <small class="text-muted">Upload image file (max 5MB). Image will be stored in uploads/ folder.</small>
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-form-row">
                            <div class="modal-form-col full-width">
                                <label for="imageUrl" class="modal-label">
                                    Or Image URL (Alternative)
                                    <input type="text" id="imageUrl" name="image_url" placeholder="/luxury-rose-perfume-bottle-elegant-black-design.jpg" class="modal-input">
                                </label>
                            </div>
                        </div>
                        
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary" id="cancelFormBtn">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitFormBtn">Add Product</button>
                        </div>
                    </form>
                    <div id="addMessage" class="hidden"></div>
                </div>
            </div>
        </div>
    </main>

    <script src="../public/js/perfume.js?v=<?php echo time(); ?>"></script>
    <script src="../admin-script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
