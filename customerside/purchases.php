<?php
// purchases.php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$isLoggedIn = isset($_SESSION['customer_id']);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Purchases</title>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-pink-50 text-gray-800" x-data="{ showLogin: false, showSignup: false, cartCount: 0 }" @keydown.escape.window="showLogin = false; showSignup = false">

<!-- Navbar -->
<nav class="bg-pink-100 shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
    
    <!-- Logo & Brand -->
    <div class="flex items-center space-x-4">
      <img src="logo.png" alt="Logo" class="rounded-full" width="60" height="50">
      <h1 class="text-2xl font-bold text-pink-600">Seven Dwarfs Boutique</h1>
    </div>

    <!-- Search Bar -->
    <form action="shop.php" method="get" class="flex-1 max-w-sm">
      <input type="text" name="search" placeholder="Search products..." 
        class="w-full px-3 py-2 border border-pink-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-400 text-sm">
    </form>

    <!-- Navigation + Icons -->
    <div class="flex items-center gap-6">
      <!-- Nav Links -->
      <ul class="flex space-x-4 text-sm md:text-base">
        <li><a href="homepage.php" class="hover:text-pink-500">Home</a></li>
        <li><a href="shop.php" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-full transition">Shop</a></li>
        <li><a href="about.php" class="hover:text-pink-500">About</a></li>
        <li><a href="contact.php" class="hover:text-pink-500">Contact</a></li>
      </ul>

      <!-- Cart Icon -->
      <a href="cart.php" class="hover:text-pink-500" title="Cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h11.1a1 1 0 001-.8l1.4-5.2H7zm0 0l-1-4H4" />
        </svg>
      </a>

      <!-- Profile -->
      <div class="relative" x-data="{ open: false }">
        <?php if ($isLoggedIn): ?>
        <button @click="open = !open" class="hover:text-pink-500" title="Profile">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 0112 14a4 4 0 016.879 3.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
          <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-100">My Profile</a>
          <a href="purchases.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-100">My Purchases</a>
          <form action="logout.php" method="POST">
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-pink-100">Logout</button>
          </form>
        </div>
        <?php else: ?>
        <button @click="showLogin = true" class="hover:text-pink-500" title="Profile">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 0112 14a4 4 0 016.879 3.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Purchases Content -->
<div class="max-w-4xl mx-auto mt-8 bg-white rounded-lg shadow p-6" x-data="{ tab: 'pay' }">
  <h1 class="text-2xl font-bold mb-4 text-pink-600">My Purchases</h1>

  <!-- Tabs -->
  <div class="flex space-x-4 mb-6 border-b pb-2">
    <button @click="tab = 'pay'" :class="tab === 'pay' ? 'text-pink-600 border-b-2 border-pink-600' : 'text-gray-600'" class="pb-2 font-medium">To Pay</button>
    <button @click="tab = 'ship'" :class="tab === 'ship' ? 'text-pink-600 border-b-2 border-pink-600' : 'text-gray-600'" class="pb-2 font-medium">To Ship</button>
    <button @click="tab = 'receive'" :class="tab === 'receive' ? 'text-pink-600 border-b-2 border-pink-600' : 'text-gray-600'" class="pb-2 font-medium">To Receive</button>
    <button @click="tab = 'rate'" :class="tab === 'rate' ? 'text-pink-600 border-b-2 border-pink-600' : 'text-gray-600'" class="pb-2 font-medium">To Rate</button>
    <button @click="tab = 'history'" :class="tab === 'history' ? 'text-pink-600 border-b-2 border-pink-600' : 'text-gray-600'" class="pb-2 font-medium">History</button>
  </div>

  <!-- Tab Content -->
  <div x-show="tab === 'pay'" class="space-y-4">
    <p class="text-gray-700">You have no orders to pay at the moment.</p>
  </div>

  <div x-show="tab === 'ship'" class="space-y-4">
    <p class="text-gray-700">You have no items to be shipped.</p>
  </div>

  <div x-show="tab === 'receive'" class="space-y-4">
    <p class="text-gray-700">You have no items to receive.</p>
  </div>

  <div x-show="tab === 'rate'" class="space-y-4">
    <p class="text-gray-700">You have no items to rate.</p>
  </div>

  <div x-show="tab === 'history'" class="space-y-4">
    <p class="text-gray-700">Your past purchase history will appear here.</p>
  </div>
</div>

</body>
</html>
