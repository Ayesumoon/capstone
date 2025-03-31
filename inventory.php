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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory.css">
    <title>Inventory</title>
</head>
<body>
    <div class="sidebar">
        <h2>Inventory</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="inventory.php"><strong>Inventory</strong></a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="payandtransac.php">Payment & Transactions</a></li>
                <li><a href="storesettings.php">Store Settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <h2>Inventory Management</h2>
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
