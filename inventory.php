<?php
    session_start();
    require 'conn.php'; // Include database connection


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
    

    $inventory = [];
    $categories = [];

    // Fetch categories from the database
    $sqlCategories = "SELECT category_id, category_name FROM categories";
    $resultCategories = $conn->query($sqlCategories);

    if ($resultCategories === false) {
        die("Error in SQL query: " . $conn->error);
    }

    if ($resultCategories->num_rows > 0) {
        while ($row = $resultCategories->fetch_assoc()) {
            $categories[] = $row;
        }
    }

    // Get selected category from the dropdown
    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

    // Fetch product inventory data with category filtering
    $sqlProducts = "
        SELECT p.product_id, p.product_name, c.category_name, p.stocks, p.price_id
        FROM products p
        INNER JOIN categories c ON p.category_id = c.category_id
    ";
    
    if ($selectedCategory !== 'all') {
        $sqlProducts .= " WHERE c.category_name = ?";
    }

    $stmt = $conn->prepare($sqlProducts);

    if ($selectedCategory !== 'all') {
        $stmt->bind_param("s", $selectedCategory);
    }

    $stmt->execute();
    $resultProducts = $stmt->get_result();

    if ($resultProducts === false) {
        die("Error in SQL query: " . $conn->error);
    }

    if ($resultProducts->num_rows > 0) {
        while ($row = $resultProducts->fetch_assoc()) {
            $inventory[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-md">
      <div class="p-4">
        <div class="flex items-center space-x-4">
          <img src="logo.png" alt="Logo" class="rounded-full" width="50" height="50"/>
          <h2 class="text-lg font-semibold">SevenDwarfs</h2>
        </div>
        <div class="mt-4">
          <div class="flex items-center space-x-4">
            <img src="newID.jpg" alt="Admin" class="rounded-full" width="40" height="40"/>
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
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 overflow-auto">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Inventory Management</h1>
      </div>

      <div class="bg-white p-4 rounded-b shadow-md mb-6">
        <form method="GET" action="inventory.php" class="flex items-center gap-2">
          <label for="category" class="font-medium text-sm">Category:</label>
          <select name="category" id="category" onchange="this.form.submit()" class="border rounded-md p-2 text-sm">
            <option value="all">All</option>
            <?php foreach ($categories as $category) { ?>
              <option value="<?php echo $category['category_name']; ?>" 
                <?php echo ($selectedCategory == $category['category_name']) ? 'selected' : ''; ?>>
                <?php echo $category['category_name']; ?>
              </option>
            <?php } ?>
          </select>
        </form>
      </div>

      <div class="overflow-x-auto">
  <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm text-sm">
    <thead class="bg-gray-100 text-gray-700">
      <tr>
        <th class="px-4 py-3 border text-left">Product ID</th>
        <th class="px-4 py-3 border text-left">Product Name</th>
        <th class="px-4 py-3 border text-left">Category</th>
        <th class="px-4 py-3 border text-left">Stocks</th>
        <th class="px-4 py-3 border text-left">Price</th>
        <th class="px-4 py-3 border text-left">Status</th>
        <th class="px-4 py-3 border text-left">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($inventory)) { 
        foreach ($inventory as $item) {
          $status = ($item['stocks'] > 20) ? "In Stock" : (($item['stocks'] > 0) ? "Low Stock" : "Out of Stock");
      ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2 border"><?php echo $item['product_id']; ?></td>
          <td class="px-4 py-2 border"><?php echo $item['product_name']; ?></td>
          <td class="px-4 py-2 border"><?php echo $item['category_name']; ?></td>
          <td class="px-4 py-2 border"><?php echo $item['stocks']; ?></td>
          <td class="px-4 py-2 border">₱<?php echo number_format($item['price_id'], 2); ?></td>
          <td class="px-4 py-2 border font-semibold capitalize 
            <?php 
              echo ($status === 'In Stock') ? 'text-green-600' : 
                   (($status === 'Low Stock') ? 'text-yellow-600' : 'text-red-600'); ?>">
            <?php echo $status; ?>
          </td>
          <td class="px-4 py-2 border">
            <a href="#" class="text-blue-500 hover:underline font-medium">Restock</a>
          </td>
        </tr>
      <?php } 
      } else { ?>
        <tr>
          <td colspan="7" class="text-center px-4 py-4 text-gray-500 border">No products found</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

</body>
</html>
