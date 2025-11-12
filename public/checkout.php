<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';

require_login();
$customer_id = get_current_customer()['customer_id'];
$cartController = new CartController($customer_id);
$items = $cartController->getItems();
$total = $cartController->getTotal();

if (empty($items)) {
    header('Location: cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Essence</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .checkout-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .checkout-title {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            font-weight: 400;
            color: var(--color-primary);
            margin-bottom: 2rem;
        }
        .checkout-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .checkout-form-section {
            background: var(--color-surface);
            border-radius: 8px;
            padding: 2rem;
        }
        .section-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--color-primary);
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-border);
            border-radius: 4px;
            font-family: var(--font-body);
            font-size: 1rem;
            color: var(--color-text);
            background: white;
            transition: var(--transition);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.1);
        }
        .form-input::placeholder {
            color: var(--color-text-light);
        }
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }
        .submit-btn:hover {
            background: var(--color-primary-dark, #b45309);
        }
        .message {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: none;
        }
        .message.success {
            background: #d1fae5;
            color: #065f46;
            display: block;
        }
        .message.error {
            background: #fee2e2;
            color: #991b1b;
            display: block;
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
        .summary-items {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--color-border);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--color-text);
            margin-bottom: 0.5rem;
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
        @media (max-width: 968px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }
            .order-summary {
                position: static;
            }
            .form-row {
                grid-template-columns: 1fr;
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

    <div class="checkout-container">
        <h1 class="checkout-title">Checkout</h1>

        <div class="checkout-layout">
            <!-- Checkout Form -->
            <div class="checkout-form-section">
                <h2 class="section-title">Shipping Information</h2>
                <form id="checkoutForm">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="first_name" placeholder="First Name" required class="form-input">
                        </div>
                        <div class="form-group">
                            <input type="text" name="last_name" placeholder="Last Name" required class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" required class="form-input">
                    </div>
                    <div class="form-group">
                        <input type="text" name="address" placeholder="Street Address" required class="form-input">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="city" placeholder="City" required class="form-input">
                        </div>
                        <div class="form-group">
                            <input type="text" name="postal_code" placeholder="Postal Code" required class="form-input">
                        </div>
                    </div>

                    <h2 class="section-title" style="margin-top: 2rem;">Payment Information</h2>
                    <div class="form-group">
                        <input type="text" name="card_name" placeholder="Cardholder Name" required class="form-input">
                    </div>
                    <div class="form-group">
                        <input type="text" name="card_number" placeholder="Card Number" required class="form-input">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="expiry" placeholder="MM/YY" required class="form-input">
                        </div>
                        <div class="form-group">
                            <input type="text" name="cvv" placeholder="CVV" required class="form-input">
                        </div>
                    </div>

                    <div id="message" class="message"></div>

                    <button type="submit" class="submit-btn">Complete Purchase</button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-items">
                    <?php foreach ($items as $item): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
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
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const messageDiv = document.getElementById('message');

            try {
                const response = await fetch('../actions/checkout_action.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                messageDiv.classList.remove('hidden', 'success', 'error');

                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = 'Order placed successfully! Redirecting...';
                    setTimeout(() => {
                        window.location.href = 'orders.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = data.message || 'An error occurred. Please try again.';
                }
            } catch (error) {
                messageDiv.classList.remove('hidden', 'success', 'error');
                messageDiv.className = 'message error';
                messageDiv.textContent = 'An error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>
