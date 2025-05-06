<?php
session_start();
require 'conn.php'; // Make sure this connects to your database properly
$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = "Admin";
$admin_role = "Admin";

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Payment & Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-md">
      <div class="p-4">
        <div class="flex items-center space-x-4">
          <img src="logo.png" alt="Logo" class="rounded-full" width="50" height="50" />
          <h2 class="text-lg font-semibold">SevenDwarfs</h2>
        </div>
        <div class="mt-4">
          <div class="flex items-center space-x-4">
            <img src="newID.jpg" alt="Admin" class="rounded-full" width="40" height="40" />
            <div>
              <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
              <p class="text-xs text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
            </div>
          </div>
        </div>
      </div>
      <nav class="mt-6">
        <ul>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-tachometer-alt mr-2"></i><a href="dashboard.php">Dashboard</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-box mr-2"></i><a href="products.php">Products</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cash-register mr-2"></i><a href="pos.php">Point of Sale</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 overflow-auto">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Payment & Transactions</h1>
      </div>

      <div class="bg-white p-6 rounded-b shadow-md">
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto border border-gray-200 shadow-md text-sm">
            <thead class="bg-gray-100 text-gray-600 text-left">
              <tr>
                <th class="px-4 py-3 border">Transaction ID</th>
                <th class="px-4 py-3 border">Order ID</th>
                <th class="px-4 py-3 border">Customer Name</th>
                <th class="px-4 py-3 border">Payment Method</th>
                <th class="px-4 py-3 border">Total</th>
                <th class="px-4 py-3 border">Payment Status</th>
                <th class="px-4 py-3 border">Date & Time</th>
                <th class="px-4 py-3 border">Actions</th>
              </tr>
            </thead>
            <tbody class="text-gray-700">
              <?php
              $sql = "
                  SELECT 
                      t.transaction_id,
                      t.order_id,
                      CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                      pm.payment_method_name,
                      t.total,
                      os.order_status_name,
                      t.date_time
                  FROM transactions t
                  LEFT JOIN customers c ON t.customer_id = c.customer_id
                  LEFT JOIN payment_methods pm ON t.payment_method_id = pm.payment_method_id
                  LEFT JOIN order_status os ON t.order_status_id = os.order_status_id
                  ORDER BY t.date_time DESC
              ";
              $result = $conn->query($sql);

              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr class='hover:bg-gray-50'>
                          <td class='px-4 py-2 border'>{$row['transaction_id']}</td>
                          <td class='px-4 py-2 border'>{$row['order_id']}</td>
                          <td class='px-4 py-2 border'>{$row['customer_name']}</td>
                          <td class='px-4 py-2 border'>{$row['payment_method_name']}</td>
                          <td class='px-4 py-2 border'>$" . number_format($row['total'], 2) . "</td>
                          <td class='px-4 py-2 border font-semibold text-blue-600'>{$row['order_status_name']}</td>
                          <td class='px-4 py-2 border'>{$row['date_time']}</td>
                          <td class='px-4 py-2 border'>
                            <a href='transaction_details.php?id={$row['transaction_id']}' class='text-blue-500 hover:underline'>View Details</a>
                          </td>
                      </tr>";
                  }
              } else {
                  echo "<tr><td colspan='8' class='text-center px-4 py-4 text-gray-500 border'>No transactions found.</td></tr>";
              }
              $conn->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
