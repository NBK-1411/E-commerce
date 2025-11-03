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
    <title>Manage Brands - Admin</title>
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
            
            <a href="perfume.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Products</span>
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
            
            <a href="brand.php" class="admin-nav-item active">
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
                <button class="admin-menu-toggle" id="sidebarToggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <h2 class="admin-page-title">Brand Management</h2>
            </div>
            
            <div class="admin-header-right">
                <div class="admin-search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text" placeholder="Search...">
                </div>
                
                <button class="admin-icon-btn" aria-label="Notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notification-badge">3</span>
                </button>
                
                <div class="admin-user">
                    <img src="/placeholder.svg?height=40&width=40" alt="Admin">
                    <div class="admin-user-info">
                        <span class="admin-user-name"><?php echo htmlspecialchars(get_current_customer()['customer_name'] ?? 'Admin'); ?></span>
                        <span class="admin-user-role">Administrator</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="admin-content">
            <!-- Add Brand Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Add New Brand</h3>
                </div>
                <div class="admin-card-body">
                    <form id="addBrandForm" class="admin-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="brandName" class="form-label">Brand Name</label>
                                <input type="text" id="brandName" name="name" placeholder="Brand name" required class="form-input">
                                <small class="form-help-text">New brands will be available for all categories</small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Add Brand</button>
                        </div>
                    </form>
                    <div id="addMessage" class="hidden mt-4"></div>
                </div>
            </div>

            <!-- Edit Brand Form (Lab requirement: UPDATE form) -->
            <div id="editBrandForm" class="admin-card hidden">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Edit Brand</h3>
                </div>
                <div class="admin-card-body">
                    <form id="updateBrandForm" class="admin-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <input type="hidden" id="editBrandId" name="id">
                                <label for="editBrandName" class="form-label">Brand Name</label>
                                <input type="text" id="editBrandName" name="name" placeholder="Brand name" required class="form-input">
                                <small class="form-help-text">Note: Brand categories cannot be changed after creation</small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Brand</button>
                            <button type="button" onclick="cancelEdit()" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                    <div id="updateMessage" class="hidden mt-4"></div>
                </div>
            </div>

            <!-- Brands Table (Lab requirement: RETRIEVE - organized by categories) -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Brands (Organized by Category)</h3>
                </div>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Brand Name</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="brandsTable">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="../public/js/brand.js"></script>
    <script src="../admin-script.js"></script>
</body>
</html>

