<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

require_login();
$customer_id = get_current_customer()['customer_id'];
$cartController = new CartController($customer_id);
$items = $cartController->getItems();
$total = $cartController->getTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Essence</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .cart-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .cart-title {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            font-weight: 400;
            color: var(--color-primary);
            margin-bottom: 2rem;
        }
        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .cart-items {
            background: var(--color-surface);
            border-radius: 8px;
            overflow: hidden;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-table thead {
            background: var(--color-border);
        }
        .cart-table th {
            padding: 1rem;
            text-align: left;
            font-family: var(--font-heading);
            font-weight: 500;
            color: var(--color-primary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cart-table td {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--color-border);
        }
        .cart-table tbody tr:last-child td {
            border-bottom: none;
        }
        .product-name {
            font-weight: 500;
            color: var(--color-text);
            margin-bottom: 0.25rem;
        }
        .product-price {
            color: var(--color-text);
            font-size: 1rem;
        }
        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid var(--color-border);
            border-radius: 4px;
            text-align: center;
            font-size: 1rem;
        }
        .remove-btn {
            background: none;
            border: none;
            color: var(--color-error, #dc2626);
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: underline;
            padding: 0;
        }
        .remove-btn:hover {
            color: var(--color-error, #b91c1c);
        }
        .order-summary {
            background: var(--color-surface);
            border-radius: 8px;
            padding: 2rem;
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .summary-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--color-primary);
            margin-bottom: 1.5rem;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--color-border);
        }
        .summary-row:last-of-type {
            border-bottom: 2px solid var(--color-primary);
            margin-bottom: 1rem;
        }
        .summary-label {
            color: var(--color-text);
        }
        .summary-value {
            font-weight: 500;
            color: var(--color-text);
        }
        .summary-total {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-primary);
        }
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 1rem;
        }
        .checkout-btn:hover {
            background: var(--color-primary-dark, #b45309);
        }
        .continue-shopping {
            display: block;
            width: 100%;
            padding: 1rem;
            background: transparent;
            color: var(--color-text);
            border: 1px solid var(--color-border);
            border-radius: 4px;
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
        }
        .continue-shopping:hover {
            background: var(--color-border);
        }
        .empty-cart {
            background: var(--color-surface);
            border-radius: 8px;
            padding: 4rem 2rem;
            text-align: center;
        }
        .empty-cart-message {
            font-size: 1.25rem;
            color: var(--color-text-light);
            margin-bottom: 2rem;
        }
        @media (max-width: 968px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }
            .order-summary {
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
                    <a href="../index.php" style="text-decoration: none; color: inherit;">
                        <h1>ESSENCE</h1>
                        <p class="tagline">Luxury Fragrances</p>
                    </a>
                </div>
                
                <div class="nav-right">
                    <button class="icon-btn" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                    <a href="cart.php" class="icon-btn cart-btn" aria-label="Cart">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="cart-count"><?php echo count($items); ?></span>
                    </a>
                    <a href="../login.php" class="icon-btn" aria-label="Account" title="<?php echo htmlspecialchars(get_current_customer()['customer_name'] ?? 'User'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
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

    <div class="cart-container">
        <h1 class="cart-title">Shopping Cart</h1>

        <?php if (empty($items)): ?>
            <div class="empty-cart">
                <p class="empty-cart-message">Your cart is empty</p>
                <a href="shop.php" class="checkout-btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <!-- Cart Items -->
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="product-price">$<?php echo number_format($item['price'], 2); ?></div>
                                    </td>
                                    <td>
                                        <input type="number" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input" onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                    </td>
                                    <td>
                                        <div class="product-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                    </td>
                                    <td>
                                        <button onclick="removeFromCart(<?php echo $item['product_id']; ?>)" class="remove-btn">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <h3 class="summary-title">Order Summary</h3>
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label summary-total">Total</span>
                        <span class="summary-value summary-total">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                    <a href="shop.php" class="continue-shopping">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        async function removeFromCart(productId) {
            if (!confirm('Remove this item from cart?')) return;

            const formData = new FormData();
            formData.append('product_id', productId);

            try {
                const response = await fetch('../actions/remove_from_cart_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Error removing item');
            }
        }

        async function updateQuantity(productId, quantity) {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            try {
                const response = await fetch('../actions/update_cart_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (!data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    location.reload();
                }
            } catch (error) {
                alert('Error updating quantity');
                location.reload();
            }
        }
    </script>
</body>
</html>
