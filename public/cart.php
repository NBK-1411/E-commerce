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
    <title>Shopping Cart - Perfume Store</title>
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
                <a href="cart.php" class="text-slate-700 hover:text-amber-600 font-semibold">Cart</a>
                <a href="../actions/logout.php" class="text-slate-700 hover:text-amber-600">Logout</a>
            </nav>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-serif font-bold text-slate-900 mb-8">Shopping Cart</h1>

        <?php if (empty($items)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-slate-600 text-lg mb-4">Your cart is empty</p>
                <a href="shop.php" class="inline-block px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="col-span-2">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-slate-100 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Product</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Price</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Quantity</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Total</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($item['name']); ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">$<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="px-6 py-4">
                                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 px-2 py-1 border border-slate-300 rounded" onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                        </td>
                                        <td class="px-6 py-4 font-semibold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td class="px-6 py-4">
                                            <button onclick="removeFromCart(<?php echo $item['product_id']; ?>)" class="text-red-600 hover:text-red-800 font-semibold">Remove</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow p-6 h-fit">
                    <h3 class="text-xl font-semibold text-slate-900 mb-4">Order Summary</h3>
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
                    <div class="flex justify-between mb-6">
                        <span class="text-lg font-semibold text-slate-900">Total</span>
                        <span class="text-lg font-bold text-amber-600">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="block w-full px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold text-center transition">
                        Proceed to Checkout
                    </a>
                    <a href="shop.php" class="block w-full mt-3 px-6 py-3 border border-slate-300 text-slate-900 rounded-lg font-semibold text-center hover:bg-slate-50 transition">
                        Continue Shopping
                    </a>
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
                }
            } catch (error) {
                alert('Error updating quantity');
            }
        }
    </script>
</body>
</html>
