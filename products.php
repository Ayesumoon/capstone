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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_category') {
  require 'conn.php';
  $name = trim($_POST['category_name']);

  if ($name !== '') {
      $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
      $stmt->bind_param("s", $name);
      if ($stmt->execute()) {
          $newId = $stmt->insert_id;
          echo json_encode(['success' => true, 'category_id' => $newId, 'category_name' => $name]);
      } else {
          echo json_encode(['success' => false, 'message' => 'DB insert failed']);
      }
      $stmt->close();
  } else {
      echo json_encode(['success' => false, 'message' => 'Empty category name']);
  }
  $conn->close();
  exit;
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
       <img alt="User profile picture" class="rounded-full" height="40" src="newID.jpg" width="40"/>
       <div>
       <h3 class="text-sm font-semibold">
        <?php echo htmlspecialchars($admin_name); ?>
        </h3>
        <p class="text-xs text-gray-500">
        <?php echo htmlspecialchars($admin_role); ?>
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
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Products</h1>
      </div>
      <div class="bg-white p-6 rounded-b shadow-md space-y-6">
      <div class="filters flex items-center gap-4">
  <!-- Category Dropdown -->
  <form method="GET" action="products.php" id="categoryForm" class="flex items-center">
    <label class="border rounded-md p-2 flex items-center gap-2">Category: 
      <select name="category" onchange="document.getElementById('categoryForm').submit()" class="p-1 border rounded">
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

  <div class="flex gap-2 items-center">
  <input type="text" id="newCategoryName" placeholder="New Category" required class="border p-2 rounded">
  <button onclick="addCategory()" class="bg-green-600 text-white px-3 py-2 rounded shadow hover:bg-green-700">
    <i class="fas fa-plus mr-1"></i>Category
  </button>
</div>

<script>
  function addCategory() {
    const name = document.getElementById('newCategoryName').value.trim();
    if (!name) return;

    fetch('products.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=add_category&category_name=' + encodeURIComponent(name),
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const dropdown = document.querySelector('select[name="category"]');
        const newOption = document.createElement('option');
        newOption.value = data.category_id;
        newOption.text = data.category_name;
        dropdown.appendChild(newOption);
        dropdown.value = data.category_id;
        dropdown.dispatchEvent(new Event('change'));
        document.getElementById('newCategoryName').value = '';
      } else {
        alert(data.message || 'Failed to add category.');
      }
    })
    .catch(err => alert('Error adding category.'));
  }
</script>


  <!-- Conditionally Show Add Product Button -->
  <?php if ($selectedCategory !== 'all') { ?>
    <a href="add_product.php?category_id=<?php echo $selectedCategory; ?>">
      <button class="bg-pink-600 text-white px-4 py-2 rounded shadow hover:bg-pink-700">
        <i class="fas fa-plus mr-2"></i>Add Product
      </button>
    </a>
  <?php } ?>
</div>



        </div>
        <div class="overflow-x-auto">
  <table class="min-w-full bg-white border border-gray-200 shadow rounded-lg">
    <thead class="bg-gray-100 text-gray-700">
      <tr>
        <th class="px-4 py-3 border">Product Image</th>
        <th class="px-4 py-3 border">Product Code</th>
        <th class="px-4 py-3 border">Description</th>
        <th class="px-4 py-3 border">Product ID</th>
        <th class="px-4 py-3 border">Price</th>
        <th class="px-4 py-3 border">Category</th>
        <th class="px-4 py-3 border">Stocks</th>
        <th class="px-4 py-3 border">Actions</th>
      </tr>
    </thead>
    <tbody class="text-gray-700">
      <?php if (!empty($products)) { 
        foreach ($products as $product) { ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 border text-center">
              <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" class="w-12 h-12 object-cover rounded">
            </td>
            <td class="px-4 py-3 border"><?php echo htmlspecialchars($product['product_name']); ?></td>
            <td class="px-4 py-3 border"><?php echo htmlspecialchars($product['description']); ?></td>
            <td class="px-4 py-3 border"><?php echo $product['product_id']; ?></td>
            <td class="px-4 py-3 border">₱<?php echo number_format($product['price_id'], 2); ?></td>
            <td class="px-4 py-3 border"><?php echo htmlspecialchars($product['category_name']); ?></td>
            <td class="px-4 py-3 border text-center"><?php echo $product['stocks']; ?></td>
            <td class="px-4 py-3 border">
            <div class="flex justify-center mt-4">
  <div class="flex gap-2">
    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">
      Edit
    </a>
    <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
      Delete
    </a>
  </div>
</div>

            </td>
          </tr>
      <?php } 
      } else { ?>
        <tr>
          <td colspan="8" class="text-center px-4 py-6 text-gray-500 border">No products available</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

