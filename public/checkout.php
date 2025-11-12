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
    <title>Checkout - Perfume Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-serif font-bold text-slate-900">Perfume Shop</a>
            <nav class="flex gap-6 items-center">
                <a href="shop.php" class="text-slate-700 hover:text-amber-600">Shop</a>
                <span class="text-slate-600">Welcome, <?php echo htmlspecialchars(get_current_customer()['customer_name'] ?? 'User'); ?></span>
                <a href="../actions/logout.php" class="text-slate-700 hover:text-amber-600">Logout</a>
            </nav>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-serif font-bold text-slate-900 mb-8">Checkout</h1>

        <div class="grid grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-6">Shipping Information</h2>
                    <form id="checkoutForm" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="first_name" placeholder="First Name" required class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                            <input type="text" name="last_name" placeholder="Last Name" required class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        <input type="text" name="address" placeholder="Street Address" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="city" placeholder="City" required class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                            <input type="text" name="postal_code" placeholder="Postal Code" required class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>

                        <h2 class="text-2xl font-semibold text-slate-900 mt-8 mb-6">Payment Information</h2>
                        <input type="text" name="card_name" placeholder="Cardholder Name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        <input type="text" name="card_number" placeholder="Card Number" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="expiry" placeholder="MM/YY" required class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                            <input type="text" name="cvv" placeholder="CVV" required class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>

                        <div id="message" class="hidden p-4 rounded-lg text-sm"></div>

                        <button type="submit" class="w-full px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold transition">
                            Complete Purchase
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6 h-fit">
                <h3 class="text-xl font-semibold text-slate-900 mb-4">Order Summary</h3>
                <div class="space-y-3 mb-6 pb-6 border-b">
                    <?php foreach ($items as $item): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600"><?php echo htmlspecialchars($item['name']); ?> x<?php echo $item['quantity']; ?></span>
                            <span class="font-semibold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="space-y-3 mb-6 pb-6 border-b">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Shipping</span>
                        <span class="font-semibold">$0.00</span>
                    </div>
                </div>
                <div class="flex justify-between">
                    <span class="text-lg font-semibold text-slate-900">Total</span>
                    <span class="text-lg font-bold text-amber-600">$<?php echo number_format($total, 2); ?></span>
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
                messageDiv.classList.remove('hidden');

                if (data.success) {
                    messageDiv.className = 'p-4 rounded-lg text-sm bg-green-100 text-green-700';
                    messageDiv.textContent = 'Order placed successfully! Redirecting...';
                    setTimeout(() => {
                        window.location.href = 'orders.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700';
                    messageDiv.textContent = data.message;
                }
            } catch (error) {
                messageDiv.classList.remove('hidden');
                messageDiv.className = 'p-4 rounded-lg text-sm bg-red-100 text-red-700';
                messageDiv.textContent = 'An error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>
