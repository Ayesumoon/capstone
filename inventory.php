<?php
    session_start();
    require 'conn.php'; // Include database connection

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/inventory.css">
    <title>Inventory</title>
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
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-tachometer-alt mr-2"></i><a href="dashboard.php">Dashboard</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-box mr-2"></i><a href="products.php">Products</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="log.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Inventory Management</h1>
      </div>
        <div class="filters">
            <form method="GET" action="inventory.php">
                <label>Category: 
                    <select name="category" onchange="this.form.submit()">
                        <option value="all">All</option>
                        <?php foreach ($categories as $category) { ?>
                            <option value="<?php echo $category['category_name']; ?>" 
                                <?php echo ($selectedCategory == $category['category_name']) ? 'selected' : ''; ?>>
                                <?php echo $category['category_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>
            </form>
        </div>
        <table class="inventory-table">
            <thead>
                <tr>
                    <th><i class="fas fa-trash-alt"></i></th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stocks</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inventory)) { 
                    foreach ($inventory as $item) {
                        // Determine stock status
                        $status = ($item['stocks'] > 20) ? "In Stock" : (($item['stocks'] > 0) ? "Low Stock" : "Out of Stock");
                ?>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><?php echo $item['product_id']; ?></td>
                            <td><?php echo $item['product_name']; ?></td>
                            <td><?php echo $item['category_name']; ?></td>
                            <td><?php echo $item['stocks']; ?></td>
                            <td>₱<?php echo number_format($item['price_id'], 2); ?></td>
                            <td><?php echo $status; ?></td>
                            <td class="actions"><a href="#">Restock</a></td>
                        </tr>
                <?php } 
                } else { ?>
                    <tr><td colspan="8" style="text-align: center;">No products found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
