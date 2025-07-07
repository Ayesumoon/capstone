<?php
session_start();
require 'conn.php'; // Include database connection

$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = "Admin";
$admin_role = "Admin";

// Get admin info
if ($admin_id) {
    $query = "
        SELECT 
            CONCAT(first_name, ' ', last_name) AS full_name, 
            r.role_name 
        FROM adminusers a
        LEFT JOIN roles r ON a.role_id = r.role_id
        WHERE a.admin_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $admin_name = $row['full_name'];
        $admin_role = $row['role_name'] ?? 'Admin';
    }
}

// Fetch order statuses from the database
$order_status_query = "SELECT * FROM order_status";
$status_result = $conn->query($order_status_query);

// Store all status options
$status_options = '';
while ($status_row = $status_result->fetch_assoc()) {
    $status_options .= "<option value='{$status_row['order_status_id']}'>{$status_row['order_status_name']}</option>";
}

$orders = [];

// Fetch only admin orders
$sql = "SELECT 
    o.order_id,
    o.admin_id AS order_by_id,
    p.product_name AS products,
    o.total_amount,
    os.order_status_name AS order_status,
    pm.payment_method_name AS payment_method,
    o.created_at
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.product_id
    LEFT JOIN order_status os ON o.order_status_id = os.order_status_id
    LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id
    WHERE o.admin_id IS NOT NULL";

$result = $conn->query($sql);
if ($result === false) {
    die("Error in SQL query: " . $conn->error);
}
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
  <!-- Sidebar -->
  <div class="w-64 bg-white shadow-md min-h-screen" x-data="{ userMenu: false, productMenu: true }">
    <div class="p-4">
      <div class="flex items-center space-x-4">
        <img src="logo.png" alt="Logo" class="rounded-full w-12 h-12" />
        <h2 class="text-lg font-semibold">SevenDwarfs</h2>
      </div>

      <!-- Admin Info -->
      <div class="mt-4 flex items-center space-x-4">
        <img src="newID.jpg" alt="Admin" class="rounded-full w-10 h-10" />
        <div>
          <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
          <p class="text-xs text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
        </div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-6">
      <ul class="space-y-1 text-sm text-gray-700">

        <li class="px-4 py-2 hover:bg-gray-200"><a href="dashboard.php" class="flex items-center"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a></li>

        <!-- User Management -->
        <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="userMenu = !userMenu">
          <div class="flex items-center justify-between">
            <span class="flex items-center"><i class="fas fa-users-cog mr-2"></i>User Management</span>
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': userMenu }"></i>
          </div>
        </li>
        <ul x-show="userMenu" x-transition class="pl-8 space-y-1">
          <li><a href="users.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-user mr-2"></i>User</a></li>
          <li><a href="user_types.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-id-badge mr-2"></i>Type</a></li>
          <li><a href="user_status.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-toggle-on mr-2"></i>Status</a></li>
          <li><a href="customers.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-users mr-2"></i>Customer</a></li>
        </ul>

        <!-- Product Management -->
        <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="productMenu = !productMenu">
          <div class="flex items-center justify-between">
            <span class="flex items-center"><i class="fas fa-box-open mr-2"></i>Product Management</span>
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': productMenu }"></i>
          </div>
        </li>
        <ul x-show="productMenu" x-transition class="pl-8 space-y-1">
          <li><a href="categories.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-tags mr-2"></i>Category</a></li>
          <li><a href="products.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-box mr-2"></i>Product</a></li>
          <li><a href="inventory.php" class="flex items-center py-1 hover:text-pink-600"><i class="fas fa-warehouse mr-2"></i>Inventory</a></li>
        </ul>

        <!-- Other Pages -->
        <li class="px-4 py-2 bg-pink-100 text-pink-600 rounded"><a href="orders.php" class="flex items-center"><i class="fas fa-shopping-cart mr-2"></i>Orders</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="payandtransac.php" class="flex items-center"><i class="fas fa-money-check-alt mr-2"></i>Payment & Transactions</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="storesettings.php" class="flex items-center"><i class="fas fa-cog mr-2"></i>Store Settings</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="logout.php" class="flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Log out</a></li>

      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 p-6">
    <div class="bg-pink-600 text-white p-4 rounded-t">
      <h1 class="text-xl font-bold">Admin Orders</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-b shadow-md mt-4 flex items-center space-x-4">
      <label for="status">Status:</label>
      <select id="status" class="border rounded-md p-2">
        <option value="all">All</option>
        <?php echo $status_options; ?>
      </select>

      <label for="date">Date:</label>
      <input type="date" id="date" class="border rounded-md p-2">
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto mt-6">
      <table class="min-w-full bg-white shadow-md table-auto border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 border-b">Order ID</th>
            <th class="px-4 py-2 border-b">Admin ID</th>
            <th class="px-4 py-2 border-b">Product(s)</th>
            <th class="px-4 py-2 border-b">Total Amount</th>
            <th class="px-4 py-2 border-b">Order Status</th>
            <th class="px-4 py-2 border-b">Payment Method</th>
            <th class="px-4 py-2 border-b">Order Date</th>
            <th class="px-4 py-2 border-b">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($orders)) {
            foreach ($orders as $order) { ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b"><?php echo $order['order_id']; ?></td>
                <td class="px-4 py-2 border-b"><?php echo $order['order_by_id']; ?></td>
                <td class="px-4 py-2 border-b"><?php echo $order['products']; ?></td>
                <td class="px-4 py-2 border-b"><?php echo $order['total_amount']; ?></td>
                <td class="px-4 py-2 border-b"><?php echo $order['order_status']; ?></td>
                <td class="px-4 py-2 border-b"><?php echo $order['payment_method']; ?></td>
                <td class="px-4 py-2 border-b"><?php echo $order['created_at']; ?></td>
                <td class="px-4 py-2 border-b">
                  <button class="bg-blue-500 text-white px-3 py-1 rounded mr-2">View</button>
                  <button class="bg-yellow-500 text-white px-3 py-1 rounded">Update</button>
                </td>
              </tr>
            <?php }
          } else { ?>
            <tr><td colspan="8" class="text-center px-4 py-4 text-gray-500">No orders found</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>