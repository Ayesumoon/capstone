<?php
include 'conn.php'; // make sure this connects to your DB

// Get metrics
$newOrders = $totalSales = $totalRevenue = 0;
$weeklyOrders = [];

$ordersQuery = $conn->query("SELECT COUNT(*) AS new_orders FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
if ($row = $ordersQuery->fetch_assoc()) $newOrders = $row['new_orders'];

$salesQuery = $conn->query("SELECT COUNT(*) AS total_sales FROM transactions");
if ($row = $salesQuery->fetch_assoc()) $totalSales = $row['total_sales'];

$revenueQuery = $conn->query("SELECT SUM(total) AS revenue FROM transactions");
if ($row = $revenueQuery->fetch_assoc()) $totalRevenue = $row['revenue'];

// Get recent orders
$recentOrders = $conn->query("SELECT o.order_id, o.total_amount, o.created_at,CONCAT(c.first_name, ' ', c.last_name) AS customer_name 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.customer_id 
    ORDER BY o.created_at DESC LIMIT 5");

// Weekly data for chart
$chartQuery = $conn->query("
    SELECT DATE(created_at) AS order_date, COUNT(*) AS count
    FROM orders 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
");

$chartLabels = $chartData = [];
while ($row = $chartQuery->fetch_assoc()) {
    $chartLabels[] = $row['order_date'];
    $chartData[] = $row['count'];
}
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
    <div class="bg-purple-500 text-white p-4 rounded-md text-center">
        <h2 class="text-lg font-semibold">New Orders</h2>
        <p class="text-2xl"><?= $newOrders ?></p>
    </div>
    <div class="bg-green-500 text-white p-4 rounded-md text-center">
        <h2 class="text-lg font-semibold">Sales</h2>
        <p class="text-2xl"><?= $totalSales ?></p>
    </div>
    <div class="bg-yellow-500 text-white p-4 rounded-md text-center">
        <h2 class="text-lg font-semibold">Revenue</h2>
        <p class="text-2xl">₱<?= number_format($totalRevenue, 2) ?></p>
    </div>
</div>

<!-- Filter -->
<div class="mt-6">
    <form method="GET" class="mb-4">
        <select name="filter" class="border p-2 rounded">
            <option value="week" <?= $_GET['filter'] === 'week' ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= $_GET['filter'] === 'month' ? 'selected' : '' ?>>This Month</option>
        </select>
        <button type="submit" class="ml-2 bg-pink-500 text-white px-4 py-2 rounded">Filter</button>
    </form>
</div>

<!-- Recent Orders -->
<div class="bg-white p-4 rounded-md shadow-md">
    <h2 class="text-lg font-semibold mb-4">Recent Orders</h2>
    <table class="w-full table-auto">
        <thead>
            <tr>
                <th class="text-left">Order ID</th>
                <th class="text-left">Customer</th>
                <th class="text-left">Amount</th>
                <th class="text-left">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $recentOrders->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['order_id'] ?></td>
                    <td><?= $row['customer_name'] ?></td>
                    <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Chart.js -->
<div class="mt-6 bg-white p-4 rounded-md shadow-md">
    <h2 class="text-lg font-semibold mb-2">Order Trends (Last 7 Days)</h2>
    <canvas id="ordersChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Orders',
                data: <?= json_encode($chartData) ?>,
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script>
 </body>
</html>
