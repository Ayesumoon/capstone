<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['customer_id']);

// Example structure of $_SESSION['cart']:
// $_SESSION['cart'] = [
//   1 => ['product_name' => 'Red Dress', 'price' => 499.99, 'quantity' => 2],
//   5 => ['product_name' => 'Blue Blouse', 'price' => 299.99, 'quantity' => 1],
// ];
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

<!-- Navigation -->
<header class="bg-white shadow">
  <div class="container mx-auto px-4 py-3 flex justify-between items-center">
    <!-- Logo -->
    <div class="text-2xl font-bold text-pink-600">
      <a href="homepage.php">Seven Dwarfs</a>
    </div>

    <!-- Center Links -->
    <ul class="flex flex-wrap justify-center space-x-6 text-sm md:text-base">
      <li><a href="homepage.php" class="hover:text-pink-500">Home</a></li>
      <li><a href="shop.php" class="hover:text-pink-500 font-semibold">Shop</a></li>
    </ul>

    <!-- Right Icons -->
    <div class="flex items-center gap-4 text-pink-600">
      <a href="cart.php" class="hover:text-pink-500" title="Cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h11.1a1 1 0 001-.8l1.4-5.2H7zm0 0l-1-4H4" />
        </svg>
      </a>

      <!-- Profile Icon -->
      <div class="relative">
        <?php if ($isLoggedIn): ?>
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="hover:text-pink-500" title="Profile">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0112 14a4 4 0 016.879 3.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </button>

            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
              <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-100">My Profile</a>
              <form action="logout.php" method="POST">
                <button type="submit" class="w-full text-left px-4 py-2 text-red-500 hover:bg-pink-100">Logout</button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <button @click="showLogin = true" class="hover:text-pink-500" title="Profile">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0112 14a4 4 0 016.879 3.804M15 11a3 3 0 11-6 0 3 3 0 616 0z" />
            </svg>
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<!-- Cart Content -->
<main class="container mx-auto px-4 py-10">
  <h1 class="text-2xl font-bold mb-6 text-pink-600">My Cart</h1>

  <?php if (!empty($_SESSION['cart'])): ?>
    <div class="bg-white shadow rounded-lg p-6">
      <table class="w-full text-left">
        <thead>
          <tr class="text-gray-700 border-b">
            <th class="pb-3">Product</th>
            <th class="pb-3">Price</th>
            <th class="pb-3">Quantity</th>
            <th class="pb-3">Total</th>
            <th class="pb-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $grandTotal = 0; ?>
          <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
            <?php
              $total = $item['price'] * $item['quantity'];
              $grandTotal += $total;
            ?>
            <tr class="border-b hover:bg-pink-50">
              <td class="py-3"><?php echo htmlspecialchars($item['product_name']); ?></td>
              <td class="py-3">₱<?php echo number_format($item['price'], 2); ?></td>
              <td class="py-3"><?php echo $item['quantity']; ?></td>
              <td class="py-3">₱<?php echo number_format($total, 2); ?></td>
              <td class="py-3">
                <form action="remove_from_cart.php" method="POST">
                  <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                  <button type="submit" class="text-red-500 hover:text-red-700">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="text-right mt-6">
        <p class="text-lg font-semibold">Grand Total: <span class="text-pink-600">₱<?php echo number_format($grandTotal, 2); ?></span></p>
        <a href="checkout.php" class="inline-block mt-4 bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">Proceed to Checkout</a>
      </div>
    </div>
  <?php else: ?>
    <div class="text-center text-gray-500">
      <p>Your cart is currently empty.</p>
      <a href="shop.php" class="mt-4 inline-block bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">Go to Shop</a>
    </div>
  <?php endif; ?>

</main>

</body>
</html>
