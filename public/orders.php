<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/order_controller.php';

require_login();
$customer_id = get_current_customer()['customer_id'];
$orderController = new OrderController();
$orders = $orderController->getByCustomer($customer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Perfume Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/index.php" class="text-2xl font-serif font-bold text-slate-900">Perfume Shop</a>
            <nav class="flex gap-6 items-center">
                <a href="/public/shop.php" class="text-slate-700 hover:text-amber-600">Shop</a>
                <span class="text-slate-600">Welcome, <?php echo htmlspecialchars(get_current_customer()['customer_name'] ?? 'User'); ?></span>
                <a href="/public/cart.php" class="text-slate-700 hover:text-amber-600">Cart</a>
                <a href="../actions/logout.php" class="text-slate-700 hover:text-amber-600">Logout</a>
            </nav>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-4xl font-serif font-bold text-slate-900 mb-8">My Orders</h1>

        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-slate-600 text-lg mb-4">You haven't placed any orders yet</p>
                <a href="/public/shop.php" class="inline-block px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): 
                    // Get order details to calculate total (matching dbforlab.sql schema)
                    $orderDetails = $orderController->getOrderDetails($order['order_id']);
                    $total = 0;
                    foreach ($orderDetails as $detail) {
                        $total += $detail['product_price'] * $detail['qty'];
                    }
                ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">Order #<?php echo $order['order_id']; ?></h3>
                                <p class="text-sm text-slate-600">Invoice #<?php echo $order['invoice_no']; ?></p>
                                <p class="text-sm text-slate-600"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-amber-600">$<?php echo number_format($total, 2); ?></p>
                                <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-semibold <?php echo $order['order_status'] === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </div>
                        </div>
                        <?php if (!empty($orderDetails)): ?>
                            <div class="mt-4 border-t pt-4">
                                <h4 class="font-semibold text-slate-900 mb-2">Items:</h4>
                                <ul class="space-y-1">
                                    <?php foreach ($orderDetails as $detail): ?>
                                        <li class="text-sm text-slate-600">
                                            <?php echo htmlspecialchars($detail['product_title']); ?> 
                                            x<?php echo $detail['qty']; ?> 
                                            @ $<?php echo number_format($detail['product_price'], 2); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
