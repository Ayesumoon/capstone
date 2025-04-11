<?php
    session_start();
?>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Dashboard
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
 </head>
 <body class="bg-gray-100">
  <div class="flex h-screen">
   <!-- Sidebar -->
   <div class="w-64 bg-white shadow-md">
    <div class="p-4">
     <div class="flex items-center space-x-4">
      <img alt="User profile picture" class="rounded-full" height="50" src="logo.png" width="50"/>
      <div>
       <h2 class="text-lg font-semibold">
        SevenDwarfs
       </h2>
      </div>
     </div>
     <div class="mt-4">
      <div class="flex items-center space-x-4">
       <img alt="User profile picture" class="rounded-full" height="40" src="ID.jpg" width="40"/>
       <div>
        <h3 class="text-sm font-semibold">
         Aisha Cayago
        </h3>
        <p class="text-xs text-gray-500">
         Admin
        </p>
       </div>
      </div>
     </div>
    </div>
    <nav class="mt-6">
     <ul>
     <li class="px-4 py-2 hover:bg-gray-200">
  <a href="dashboard.php" class="flex items-center space-x-2">
  <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-tachometer-alt"></i>
    <span>Dashboard</strong></span>
  </a>
</li>
<li class="px-4 py-2 hover:bg-gray-200">
  <a href="products.php" class="flex items-center space-x-2">
    <i class="fas fa-box"></i>
    <span>Products</span>
  </a>
</li>
<li class="px-4 py-2 hover:bg-gray-200">
  <a href="orders.php" class="flex items-center space-x-2">
    <i class="fas fa-shopping-cart"></i>
    <span>Orders</span>
  </a>
</li>
<li class="px-4 py-2 hover:bg-gray-200">
  <a href="customers.php" class="flex items-center space-x-2">
    <i class="fas fa-users"></i>
    <span>Customers</span>
  </a>
</li>

<li class="px-4 py-2 hover:bg-gray-200">
  <a href="inventory.php" class="flex items-center space-x-2">
    <i class="fas fa-warehouse"></i>
    <span>Inventory</span>
  </a>
</li>

<li class="px-4 py-2 hover:bg-gray-200">
  <a href="users.php" class="flex items-center space-x-2">
    <i class="fas fa-user"></i>
    <span>Users</span>
  </a>
</li>

<li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>

<li class="px-4 py-2 hover:bg-gray-200">
  <a href="storesettings.php" class="flex items-center space-x-2">
    <i class="fas fa-cog"></i>
    <span>Store Settings</span>
  </a>
</li>

<li class="px-4 py-2 hover:bg-gray-200">
  <a href="logout.php" class="flex items-center space-x-2">
    <i class="fas fa-sign-out-alt"></i>
    <span>Log out</span>
  </a>
</li>

     </ul>
    </nav>
   </div>
   <!-- Main Content -->
   <div class="flex-1 flex flex-col">
    <!-- Header -->
     
    <header class="bg-pink-500 p-4 flex items-center justify-between">
     <div class="flex items-center space-x-4">
      <button class="text-white text-2xl">
      <h1 class="text-xl font-bold">Dashboard</h1>
       </i>
     </div>
     <div class="flex items-center space-x-4">
      <button class="text-white text-xl">
       <i class="fas fa-envelope">
       </i>
      </button>
      <button class="text-white text-xl">
       <i class="fas fa-bell">
       </i>
      </button>
     </div>
    </header>
    <!-- Dashboard Content -->
    
     <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-purple-500 text-white p-4 rou text-center w-30 h-48 flex items-center justify-center">   
       <h2 class="text-lg font-semibold">
        New Orders
       </h2>
      </div>
      <div class="bg-green-500 text-white p-4 rounded-md text-center w-30 h-48 flex items-center justify-center">
       <h2 class="text-lg font-semibold">
        Sales
       </h2>
      </div>
      <div class="bg-yellow-500 text-white p-4 rounded-md text-center w-30 h-48 flex items-center justify-center">
       <h2 class="text-lg font-semibold">
        Revenue
       </h2>
      </div>
     </div>
     <div class="mt-6">
      <div class="bg-white p-4 rounded-md shadow-md">
       <h2 class="text-lg font-semibold">
        Recent Orders
       </h2>
      </div>
     </div>
     <div class="mt-6">
      <div class="bg-white p-4 rounded-md shadow-md">
       <h2 class="text-lg font-semibold">
        Activities
       </h2>
      </div>
     </div>
    </main>
   </div>
  </div>
 </body>
</html>
