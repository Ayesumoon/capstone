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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="products.css">
    <title>Products</title>
</head>
<body>
    <div class="sidebar">
        <h2>Products</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><strong><a href="products.php">Products</a></strong></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="payandtransac.php">Payment & Transactions</a></li>
                <li><a href="storesettings.php">Store Settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <h2>Products</h2>
        <br>
        <button class="button" onclick="location.href='add_product.php'">+ Create New Product</button>

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

        <table class="product-table">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Product ID</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stocks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)) { 
                    foreach ($products as $product) { ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" style="width:50px; height:50px;"></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td><?php echo $product['product_id']; ?></td>
                            <td>₱<?php echo number_format($product['price_id'], 2); ?></td> <!-- Fixed price column -->
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><?php echo $product['stocks']; ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>">
                                    <button class="edit-button">Edit</button>
                                </a>
                                <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">
                                    <button class="delete-button">Delete</button>
                                </a>
                            </td>
                        </tr>
                <?php } 
                } else { ?>
                    <tr><td colspan="8" style="text-align: center;">No products available</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
