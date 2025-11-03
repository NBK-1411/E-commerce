<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/settings/db_cred.php';
require_once __DIR__ . '/settings/core.php';

// Check if user is logged in and is an admin (Lab requirement)
if (!is_logged_in()) {
    header('Location: login.php?error=Please login first');
    exit();
}

if (!is_admin()) {
    header('Location: index.php?error=Admin access required');
    exit();
}

// Get database connection
require_once __DIR__ . '/settings/db_class.php';
$db = new Database();
$db->connect();
$conn = $db->getConnection();

// Fetch statistics from database
$stats = [
    'total_sales' => 0,
    'total_orders' => 0,
    'total_products' => 0,
    'total_customers' => 0
];

// Get total customers (user_role = 2)
$customer_result = $conn->query("SELECT COUNT(*) as count FROM customer WHERE user_role = 2");
if ($customer_result) {
    $customer_row = $customer_result->fetch_assoc();
    $stats['total_customers'] = $customer_row['count'];
}

// Get total products (using products table from dbforlab.sql)
$products_result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($products_result) {
    $products_row = $products_result->fetch_assoc();
    $stats['total_products'] = $products_row['count'];
}

$recent_orders = [
    ['id' => '#ORD-1234', 'customer' => 'Sarah Johnson', 'product' => 'Midnight Rose', 'amount' => 145.00, 'status' => 'Completed', 'date' => '2025-10-15'],
    ['id' => '#ORD-1233', 'customer' => 'Michael Chen', 'product' => 'Ocean Breeze', 'amount' => 165.00, 'status' => 'Processing', 'date' => '2025-10-15'],
    ['id' => '#ORD-1232', 'customer' => 'Emma Williams', 'product' => 'Noir Elegance', 'amount' => 185.00, 'status' => 'Shipped', 'date' => '2025-10-14'],
    ['id' => '#ORD-1231', 'customer' => 'James Brown', 'product' => 'Velvet Amber', 'amount' => 155.00, 'status' => 'Completed', 'date' => '2025-10-14'],
    ['id' => '#ORD-1230', 'customer' => 'Olivia Davis', 'product' => 'Midnight Rose', 'amount' => 145.00, 'status' => 'Completed', 'date' => '2025-10-13']
];

// Fetch products from database (using products table from dbforlab.sql)
$products = [];
$products_query = $conn->query("SELECT p.product_id as id, p.product_title as name, 
                                        COALESCE(c.cat_name, 'Uncategorized') as category, 
                                        p.product_price as price, 
                                        'Active' as status 
                                 FROM products p 
                                 LEFT JOIN categories c ON p.product_cat = c.cat_id 
                                 ORDER BY p.product_id DESC 
                                 LIMIT 10");
if ($products_query) {
    while ($row = $products_query->fetch_assoc()) {
        $products[] = $row;
    }
}

// Calculate totals from sample data
$stats['total_orders'] = count($recent_orders);
$stats['total_sales'] = array_sum(array_column($recent_orders, 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Essence</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin-styles.css">
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
            <a href="admin.php" class="admin-nav-item active">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Dashboard</span>
            </a>
            
            <a href="admin/perfume.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Products</span>
            </a>
            <a href="admin/category.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Categories</span>
            </a>
            
            <a href="admin/brand.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <span>Brands</span>
            </a>
            
            <a href="#orders" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span>Orders</span>
            </a>
            
            <a href="#customers" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Customers</span>
            </a>
            
            <a href="#analytics" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="20" x2="12" y2="10"></line>
                    <line x1="18" y1="20" x2="18" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="16"></line>
                </svg>
                <span>Analytics</span>
            </a>
            
            <a href="#settings" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"></path>
                </svg>
                <span>Settings</span>
            </a>
        </nav>
        
        <div class="admin-sidebar-footer">
            <a href="index.php" class="admin-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>View Store</span>
            </a>
            <a href="actions/logout.php" class="admin-nav-item">
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
                <h2 class="admin-page-title">Dashboard Overview</h2>
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
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(212, 175, 55, 0.1);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total Sales</p>
                        <h3 class="stat-value">$<?php echo number_format($stats['total_sales'], 2); ?></h3>
                        <span class="stat-change positive">+12.5% from last month</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(139, 115, 85, 0.1);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-secondary)" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total Orders</p>
                        <h3 class="stat-value"><?php echo $stats['total_orders']; ?></h3>
                        <span class="stat-change positive">+8.2% from last month</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(26, 26, 26, 0.1);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total Products</p>
                        <h3 class="stat-value"><?php echo $stats['total_products']; ?></h3>
                        <span class="stat-change neutral">No change</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: rgba(139, 115, 85, 0.1);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-secondary)" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <p class="stat-label">Total Customers</p>
                        <h3 class="stat-value"><?php echo $stats['total_customers']; ?></h3>
                        <span class="stat-change positive">+15.3% from last month</span>
                    </div>
                </div>
            </div>

            <!-- Recent Orders & Products -->
            <div class="admin-grid">
                <!-- Recent Orders -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Recent Orders</h3>
                        <a href="#all-orders" class="admin-link">View All</a>
                    </div>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($order['id']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['customer']); ?></td>
                                    <td><?php echo htmlspecialchars($order['product']); ?></td>
                                    <td>$<?php echo number_format($order['amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Product Management -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Product Inventory</h3>
                        <a href="admin/perfume.php" class="btn btn-primary btn-sm">Manage Products</a>
                    </div>
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>N/A</td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $product['status'])); ?>">
                                            <?php echo htmlspecialchars($product['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="window.location.href='admin/perfume.php?edit=<?php echo $product['id']; ?>';" 
                                                    class="action-btn" aria-label="Edit" title="Edit Product">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <button onclick="if(confirm('Are you sure you want to delete this product?')) { deleteProductFromDashboard(<?php echo $product['id']; ?>); }" 
                                                    class="action-btn" aria-label="Delete" title="Delete Product">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="admin-script.js"></script>
    <script>
        async function deleteProductFromDashboard(productId) {
            const formData = new FormData();
            formData.append('id', productId);

            try {
                const response = await fetch('actions/delete_perfume_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    alert('Product deleted successfully!');
                    location.reload(); // Reload page to show updated list
                } else {
                    alert(data.message || 'Failed to delete product');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('Error deleting product');
            }
        }
    </script>
</body>
</html>
