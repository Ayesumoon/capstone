<?php
$conn = new mysqli("localhost", "root", "", "dbms");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get category and sort filter from query string
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Base SQL
$sql = "SELECT p.product_name, p.description, p.price_id AS price, p.image_url, c.category_name
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.stocks > 0";

// Add category filter if selected
if ($selectedCategory) {
  $sql .= " AND c.category_name = '" . $conn->real_escape_string($selectedCategory) . "'";
}

// Add sorting logic
switch ($sort) {
  case 'low_to_high':
    $sql .= " ORDER BY p.price_id ASC";
    break;
  case 'high_to_low':
    $sql .= " ORDER BY p.price_id DESC";
    break;
  default:
    $sql .= " ORDER BY p.product_id DESC"; // latest
}

// ✅ Execute the query
$result = $conn->query($sql);
if (!$result) {
  die("Query failed: " . $conn->error);
}

// Define category list for sidebar
$categories = ['Blouse', 'Dress', 'Shorts', 'Skirt', 'Trouser', 'Pants', 'Coordinates', 'Shoes', 'Perfume'];

$searchQuery = isset($_GET['search']) ? $_GET['search'] : null;

if ($searchQuery) {
  $sql .= " AND p.product_name LIKE '%" . $conn->real_escape_string($searchQuery) . "%'";
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shop | Seven Dwarfs Boutique</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-pink-50 text-gray-800" x-data="{ showLogin: false, showSignup: false }" @keydown.escape.window="showLogin = false; showSignup = false">

<!-- Navbar -->
<nav class="bg-pink-100 shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
    <!-- Left: Logo + Search -->
    <div class="flex flex-1 items-center gap-4">
      <h1 class="text-2xl font-bold text-pink-600 whitespace-nowrap">Seven Dwarfs Boutique</h1>
      <form action="shop.php" method="get" class="flex flex-1 max-w-sm">
        <input type="text" name="search" placeholder="Search products..." 
               class="w-full px-3 py-2 border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-400 text-sm">
      </form>
    </div>

    <!-- Center: Navigation Links -->
    <ul class="flex flex-wrap justify-center space-x-4 text-sm md:text-base">
      <li><a href="homepage.php" class="hover:text-pink-500">Home</a></li>
      <li><a href="shop.php" class="hover:text-pink-500 font-semibold">Shop</a></li>
      <li><a href="about.php" class="hover:text-pink-500">About</a></li>
      <li><a href="contact.php" class="hover:text-pink-500">Contact</a></li>
    </ul>

    <!-- Right: Icons -->
    <div class="flex items-center gap-4 text-pink-600">
      <!-- Cart Icon -->
      <a href="cart.php" class="hover:text-pink-500" title="Cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h11.1a1 1 0 001-.8l1.4-5.2H7zm0 0l-1-4H4" />
        </svg>
      </a>

      <!-- Profile Icon opens login modal directly -->
      <div class="relative">
        <button @click="showLogin = true" class="hover:text-pink-500" title="Profile">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0112 14a4 4 0 016.879 3.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- Login Modal -->
<div x-show="showLogin" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold mb-4 text-pink-600">Login</h2>
    <form action="login_handler.php" method="POST">
      <input type="email" name="email" placeholder="Email" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <input type="password" name="password" placeholder="Password" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <button type="submit" class="w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600 transition">Log In</button>
    </form>
    <p class="text-sm text-center mt-4">
      Don't have an account? 
      <button @click="showLogin = false; showSignup = true" class="text-pink-600 hover:underline">Sign up here</button>
    </p>
  </div>
</div>

<!-- Signup Modal -->
<div x-show="showSignup" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-md">
    <h2 class="text-lg font-semibold mb-4 text-pink-600">Sign Up</h2>
    <form action="signup_handler.php" method="POST">
      <input type="text" name="first_name" placeholder="First Name" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <input type="text" name="last_name" placeholder="Last Name" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <input type="email" name="email" placeholder="Email" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <input type="text" name="phone" placeholder="Phone" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <input type="text" name="address" placeholder="Address" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <input type="password" name="password" placeholder="Password" required class="w-full border border-gray-300 p-2 rounded mb-3 focus:ring-2 focus:ring-pink-400">
      <button type="submit" class="w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600 transition">Sign Up</button>
    </form>
  </div>
</div>
<!-- Banner -->
<section class="bg-pink-200 text-center py-16">
  <h2 class="text-4xl font-bold text-pink-800">Shop All Products</h2>
  <p class="text-lg text-pink-900 mt-2">
    <?= $selectedCategory ? "Showing: " . htmlspecialchars($selectedCategory) : "Browse our magical collection 🌟" ?>
  </p>
</section>

<!-- Main Content -->
<section class="max-w-7xl mx-auto px-4 py-12 flex gap-10">
  
  <!-- Sidebar -->
  <aside class="w-64 bg-white rounded-xl shadow-md p-6" x-data="{ open: true }">
    <h3 class="text-lg font-bold mb-4 text-pink-700">Filters</h3>

    <ul class="space-y-2">
      <li><a href="shop.php" class="text-gray-700 hover:text-pink-600">✨ New Arrivals</a></li>
      <li><a href="#" class="text-gray-700 hover:text-pink-600">🔥 On Sale</a></li>
    </ul>

    <div class="mt-6" x-data="{ open: true }">
      <button @click="open = !open" class="w-full flex justify-between items-center font-semibold text-pink-600">
        Categories
        <svg :class="{'rotate-180': open}" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <ul x-show="open" x-transition class="mt-3 pl-2 text-sm text-gray-700 space-y-1">
        <?php foreach ($categories as $cat): ?>
          <li>
            <a href="shop.php?category=<?= urlencode($cat) ?>" 
              class="<?= $selectedCategory === $cat ? 'text-pink-600 font-semibold' : 'hover:text-pink-500' ?>">
              <?= $cat ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </aside>

  <!-- Product Grid -->
  <div class="flex-1">
    <!-- Sort Dropdown -->
    <div class="flex justify-end mb-6">
      <form method="get" class="flex items-center gap-2">
        <?php if ($selectedCategory): ?>
          <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
        <?php endif; ?>
        <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()" 
                class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400">
          <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Latest</option>
          <option value="low_to_high" <?= $sort === 'low_to_high' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="high_to_low" <?= $sort === 'high_to_low' ? 'selected' : '' ?>>Price: High to Low</option>
        </select>
      </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="bg-white rounded-xl shadow-md p-4">
            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="rounded-md mb-4 w-full h-48 object-cover">
            <h4 class="text-xl font-semibold text-pink-800"><?= htmlspecialchars($row['product_name']) ?></h4>
            <p class="text-sm text-gray-600"><?= htmlspecialchars($row['description']) ?></p>
            <p class="text-pink-600 mt-2 font-semibold">₱<?= number_format($row['price'], 2) ?></p>
            <button class="mt-4 w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600 transition">Add to Cart</button>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-gray-500 col-span-1 sm:col-span-2 md:col-span-3">No products found for this category.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-pink-100 text-center py-6">
  <p class="text-pink-700">&copy; 2025 Seven Dwarfs Boutique. All rights reserved.</p>
</footer>

</body>
</html>


<?php $conn->close(); ?>
