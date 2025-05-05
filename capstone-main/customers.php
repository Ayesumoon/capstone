<?php
    session_start();
    require 'conn.php'; // Database connection

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

    $customers = [];

    // Fetch customers with status names
    $sql = "
        SELECT c.customer_id, 
               CONCAT(c.first_name, ' ', c.last_name) AS name, 
               c.email, 
               c.phone, 
               s.status_name, 
               c.created_at 
        FROM customers c
        INNER JOIN status s ON c.status_id = s.status_id
    ";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Error in SQL query: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customers</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
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
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 overflow-auto">

      <!-- Header -->
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Customers</h1>
      </div>

      <!-- Filter Section -->
      <div class="bg-white p-4 shadow-md rounded-b mb-6">
        <form method="GET" action="customers.php" class="flex justify-between items-center flex-wrap gap-2">
          <div>
            <label for="status" class="text-sm font-medium mr-2">Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="border rounded-md p-2 text-sm">
              <option value="all">All</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </form>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-200 shadow-md text-sm bg-white">
          <thead class="bg-gray-100 text-left text-gray-600">
            <tr>
              <th class="px-4 py-3 border">Customer ID</th>
              <th class="px-4 py-3 border">Name</th>
              <th class="px-4 py-3 border">Email</th>
              <th class="px-4 py-3 border">Phone</th>
              <th class="px-4 py-3 border">Status</th>
              <th class="px-4 py-3 border">Registered</th>
              <th class="px-4 py-3 border">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <?php if (!empty($customers)) { 
              foreach ($customers as $customer) { ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border"><?php echo $customer['customer_id']; ?></td>
                <td class="px-4 py-2 border"><?php echo htmlspecialchars($customer['name']); ?></td>
                <td class="px-4 py-2 border"><?php echo htmlspecialchars($customer['email']); ?></td>
                <td class="px-4 py-2 border"><?php echo htmlspecialchars($customer['phone']); ?></td>
                <td class="px-4 py-2 border font-semibold capitalize <?php echo strtolower($customer['status_name']) === 'active' ? 'text-green-600' : 'text-red-600'; ?>">
                  <?php echo $customer['status_name']; ?>
                </td>
                <td class="px-4 py-2 border"><?php echo $customer['created_at']; ?></td>
                <td class="px-4 py-2 border">
                  <a href="#" class="text-blue-500 hover:underline font-medium">View</a>
                </td>
              </tr>
            <?php } 
            } else { ?>
              <tr>
                <td colspan="8" class="text-center px-4 py-4 text-gray-500 border">No customers found</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- Optional Custom Styles -->
  <style>
    .active { color: #16a34a; }
    .inactive { color: #dc2626; }
  </style>
</body>
</html>
