<?php
session_start();
require 'conn.php'; // Database connection

$isLoggedIn = isset($_SESSION['customer_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart - Seven Dwarfs Boutique</title>
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
            <li><a href="shop.php" class="hover:text-pink-500 font-semibold">Shop</a></li>
        </ul>
        <div class="flex items-center gap-4 text-pink-600">
            <a href="cart.php" class="hover:text-pink-500 relative" title="Cart">
                <?php
                if ($isLoggedIn) {
                    $customer_id = $_SESSION['customer_id'];
                    $stmt = $conn->prepare("SELECT SUM(quantity) as total_items FROM carts WHERE customer_id = ? AND cart_status = 'active'");
                    $stmt->bind_param("i", $customer_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $cart_info = $result->fetch_assoc();
                    $total_items = $cart_info['total_items'] ?? 0;
                    echo "<span class='absolute top-0 right-0 text-white bg-pink-600 rounded-full text-xs px-2 py-1'>$total_items</span>";
                }
                ?>
            </a>
            <div class="relative">
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="text-pink-600 hover:text-pink-500">Profile</a>
                <?php else: ?>
                    <a href="login.php" class="text-pink-600 hover:text-pink-500">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main class="container mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6 text-pink-600">My Cart</h1>

    <?php
    if ($isLoggedIn) {
        $customer_id = $_SESSION['customer_id'];
        $stmt = $conn->prepare("SELECT c.cart_id, c.product_id, c.quantity, p.product_name, p.price_id FROM carts c JOIN products p ON c.product_id = p.product_id WHERE c.customer_id = ? AND c.cart_status = 'active'");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_items = $result->fetch_all(MYSQLI_ASSOC);

        if (!empty($cart_items)): ?>
            <form action="checkout.php" method="POST">
                <div class="bg-white shadow rounded-lg p-6">
                    <table class="w-full text-left">
                        <thead>
                        <tr class="text-gray-700 border-b">
                            <th class="pb-3">Select</th>
                            <th class="pb-3">Product</th>
                            <th class="pb-3">Price</th>
                            <th class="pb-3">Quantity</th>
                            <th class="pb-3">Total</th>
                            <th class="pb-3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $grandTotal = 0;
                        foreach ($cart_items as $item):
                            $total = $item['price_id'] * $item['quantity'];
                            ?>
                            <tr class="border-b hover:bg-pink-50">
                                <td class="py-3">
                                    <input type="checkbox" name="selected_cart_ids[]" value="<?php echo $item['cart_id']; ?>" class="w-4 h-4 text-pink-600">
                                </td>
                                <td class="py-3"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td class="py-3">₱<?php echo number_format($item['price_id'], 2); ?></td>
                                <td class="py-3"><?php echo $item['quantity']; ?></td>
                                <td class="py-3">₱<?php echo number_format($total, 2); ?></td>
                                <td class="py-3">
                                    <form action="remove_from_cart.php" method="POST">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="text-right mt-6">
                        <button type="submit" class="inline-block bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="text-center text-gray-500">
                <p>Your cart is currently empty.</p>
                <a href="shop.php" class="mt-4 inline-block bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">Go to Shop</a>
            </div>
        <?php endif;
    } else {
        echo "<div class='text-center text-gray-500'><p>Please log in to view your cart.</p></div>";
    }
    ?>
</main>

</body>
</html>
