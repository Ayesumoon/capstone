<?php
session_start();
require 'conn.php'; // Database connection

$categories = [];
$products = [];

// Fetch categories from the database
$sqlCategories = "SELECT category_id, category_name FROM categories";
$resultCategories = $conn->query($sqlCategories);

if ($resultCategories === false) {
    die("Error fetching categories: " . $conn->error);
}

if ($resultCategories->num_rows > 0) {
    while ($row = $resultCategories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get selected category from the dropdown
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

// Fetch products with category filtering
$sqlProducts = "
    SELECT p.*, c.category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
";

if ($selectedCategory !== 'all') {
    $sqlProducts .= " WHERE p.category_id = ?";
}

$stmt = $conn->prepare($sqlProducts);

if ($selectedCategory !== 'all') {
    $stmt->bind_param("i", $selectedCategory); // Use category_id instead
}

$stmt->execute();
$resultProducts = $stmt->get_result();

if ($resultProducts->num_rows > 0) {
    while ($row = $resultProducts->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();
$conn->close();
?>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Products
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
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-box mr-2"></i><a href="products.php">Products</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="log.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Products</h1>
      </div>
      <div class="bg-white p-6 rounded-b shadow-md space-y-6">
        <div class="filters">
            <form method="GET" action="products.php">
            <label class="category">Category: 
    <select name="category" onchange="this.form.submit()">
        <option value="all">All</option>
        <?php foreach ($categories as $category) { ?>
            <option value="<?php echo $category['category_id']; ?>" 
                <?php echo ($selectedCategory == $category['category_id']) ? 'selected' : ''; ?>>
                <?php echo $category['category_name']; ?>
            </option>
        <?php } ?>
    </select>
</label>
            </form>
        </div>

        <table class="table-auto w-full border-collapse">
    <thead>
        <tr class="bg-gray-100 text-left">
            <th class="px-4 py-2 border-b">Product Image</th>
            <th class="px-4 py-2 border-b">Product Name</th>
            <th class="px-4 py-2 border-b">Description</th>
            <th class="px-4 py-2 border-b">Product ID</th>
            <th class="px-4 py-2 border-b">Price</th>
            <th class="px-4 py-2 border-b">Category</th>
            <th class="px-4 py-2 border-b">Stocks</th>
            <th class="px-4 py-2 border-b">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($products)) { 
            foreach ($products as $product) { ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b"><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" style="width:50px; height:50px;"></td>
                    <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($product['description']); ?></td>
                    <td class="px-4 py-2 border-b"><?php echo $product['product_id']; ?></td>
                    <td class="px-4 py-2 border-b">₱<?php echo number_format($product['price_id'], 2); ?></td>
                    <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td class="px-4 py-2 border-b"><?php echo $product['stocks']; ?></td>
                    <td class="px-4 py-2 border-b">
                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">
                            <button class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</button>
                        </a>
                        <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">
                            <button class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                        </a>
                    </td>
                </tr>
        <?php } 
        } else { ?>
            <tr><td colspan="8" class="text-center px-4 py-2 border-b">No products available</td></tr>
        <?php } ?>
        <!-- Sample Data for Rows -->
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border-b"><img src="product_image_1.jpg" alt="Product Image" style="width:50px; height:50px;"></td>
            <td class="px-4 py-2 border-b">Pink Dress</td>
            <td class="px-4 py-2 border-b">Size: M - L</td>
            <td class="px-4 py-2 border-b">1</td>
            <td class="px-4 py-2 border-b">₱780.00</td>
            <td class="px-4 py-2 border-b">Dress</td>
            <td class="px-4 py-2 border-b">20</td>
            <td class="px-4 py-2 border-b">
                <a href="edit_product.php?id=1">
                    <button class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</button>
                </a>
                <a href="delete_product.php?id=1" onclick="return confirm('Are you sure you want to delete this product?')">
                    <button class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                </a>
            </td>
        </tr>
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border-b"><img src="product_image_2.jpg" alt="Product Image" style="width:50px; height:50px;"></td>
            <td class="px-4 py-2 border-b">White Blouse</td>
            <td class="px-4 py-2 border-b">Size: S - L</td>
            <td class="px-4 py-2 border-b">2</td>
            <td class="px-4 py-2 border-b">₱500.00</td>
            <td class="px-4 py-2 border-b">Blouse</td>
            <td class="px-4 py-2 border-b">20</td>
            <td class="px-4 py-2 border-b">
                <a href="edit_product.php?id=2">
                    <button class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</button>
                </a>
                <a href="delete_product.php?id=2" onclick="return confirm('Are you sure you want to delete this product?')">
                    <button class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                </a>
            </td>
        </tr>
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border-b"><img src="product_image_3.jpg" alt="Product Image" style="width:50px; height:50px;"></td>
            <td class="px-4 py-2 border-b">Test1</td>
            <td class="px-4 py-2 border-b">Size: M - L</td>
            <td class="px-4 py-2 border-b">3</td>
            <td class="px-4 py-2 border-b">₱200.00</td>
            <td class="px-4 py-2 border-b">Shoes</td>
            <td class="px-4 py-2 border-b">30</td>
            <td class="px-4 py-2 border-b">
                <a href="edit_product.php?id=3">
                    <button class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</button>
                </a>
                <a href="delete_product.php?id=3" onclick="return confirm('Are you sure you want to delete this product?')">
                    <button class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                </a>
            </td>
        </tr>
    </tbody>
</table>
