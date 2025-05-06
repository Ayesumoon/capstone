<?php
session_start();
require 'conn.php'; // Database connection

$isLoggedIn = isset($_SESSION['customer_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_cart_ids'])) {
    $selectedCartIds = $_POST['selected_cart_ids']; // Array of selected cart IDs
} else {
    header("Location: cart.php"); // If no items are selected, redirect back to the cart page
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Seven Dwarfs Boutique</title>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">

<header class="bg-white shadow">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="text-2xl font-bold text-pink-600">
            <a href="homepage.php">Seven Dwarfs</a>
        </div>
        <ul class="flex flex-wrap justify-center space-x-6 text-sm md:text-base">
            <li><a href="homepage.php" class="hover:text-pink-500">Home</a></li>
            <li><a href="shop.php" class="hover:text-pink-500">Shop</a></li>
            <li><a href="cart.php" class="hover:text-pink-500">Cart</a></li>
        </ul>
        <div class="flex items-center gap-4 text-pink-600">
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="text-pink-600 hover:text-pink-500">Profile</a>
            <?php else: ?>
                <a href="login.php" class="text-pink-600 hover:text-pink-500">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="container mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6 text-pink-600">Checkout</h1>

    <?php
    if ($isLoggedIn) {
        $customer_id = $_SESSION['customer_id'];

        // Get the selected cart items from the database
        $inClause = implode(',', array_fill(0, count($selectedCartIds), '?'));
        $stmt = $conn->prepare("SELECT c.cart_id, c.product_id, c.quantity, p.product_name, p.price_id 
                                FROM carts c 
                                JOIN products p ON c.product_id = p.product_id 
                                WHERE c.customer_id = ? AND c.cart_status = 'active' AND c.cart_id IN ($inClause)");
        $stmt->bind_param(str_repeat('i', count($selectedCartIds) + 1), $customer_id, ...$selectedCartIds);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_items = $result->fetch_all(MYSQLI_ASSOC);

        if (!empty($cart_items)):
            // Calculate the total price for the selected items
            $grandTotal = 0;
            foreach ($cart_items as $item) {
                $total = $item['price_id'] * $item['quantity'];
                $grandTotal += $total;
            }
            ?>
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-gray-700 border-b">
                            <th class="pb-3">Product</th>
                            <th class="pb-3">Price</th>
                            <th class="pb-3">Quantity</th>
                            <th class="pb-3">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart_items as $item): 
                        $total = $item['price_id'] * $item['quantity']; ?>
                        <tr class="border-b hover:bg-pink-50">
                            <td class="py-3"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="py-3">₱<?php echo number_format($item['price_id'], 2); ?></td>
                            <td class="py-3"><?php echo $item['quantity']; ?></td>
                            <td class="py-3">₱<?php echo number_format($total, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="text-right mt-6">
                    <p class="text-lg font-semibold">Grand Total: <span class="text-pink-600">₱<?php echo number_format($grandTotal, 2); ?></span></p>
                </div>

                <!-- Payment Information Form -->
                <form action="process_checkout.php" method="POST">
                    <div class="mt-6">
                        <label for="payment_method" class="block text-gray-700">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="mt-2 w-full p-3 border border-gray-300 rounded-md">
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="cod">Cash on Delivery</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="inline-block bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">
                            Complete Purchase
                        </button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="text-center text-gray-500">
                <p>No items selected. Please select items from your cart to proceed.</p>
                <a href="cart.php" class="mt-4 inline-block bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">Go Back to Cart</a>
            </div>
        <?php endif;
    } else {
        echo "<div class='text-center text-gray-500'><p>Please log in to complete your purchase.</p></div>";
    }
    ?>
</main>

</body>
</html>
