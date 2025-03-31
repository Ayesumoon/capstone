<?php
    session_start();
    require 'conn.php'; // Include database connection

    $orders = [];
    
    // Fetch orders and product names by joining products table using product_id
    $sql = "SELECT o.order_id, o.customer_id, p.product_name AS products, 
                   o.total_amount, o.order_status_id, o.payment_method_id, o.created_at
            FROM orders o
            LEFT JOIN products p ON o.product_id = p.product_id"; // Assuming product_id is in orders table

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/orders.css">
    <title>Orders</title>
</head>
<body>
    <div class="sidebar">
        <h2>Orders</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><strong><a href="orders.php">Orders</a></strong></li>
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
        <div class="header">Orders</div>
        <br>
        <label for="status">Status:</label>
        <select id="status">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="shipped">Shipped</option>
        </select>
        <label for="date">Date:</label>
        <input type="date" id="date">
        
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Product(s)</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)) { 
                    foreach ($orders as $order) { ?>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['customer_id']; ?></td>
                            <td><?php echo $order['products']; ?></td>
                            <td><?php echo $order['total_amount']; ?></td>
                            <td class="status <?php echo strtolower($order['order_status_id']); ?>"><?php echo $order['order_status_id']; ?></td>
                            <td><?php echo $order['payment_method']; ?></td>
                            <td><?php echo $order['created_at']; ?></td>
                            <td class="actions">
                                <button class="view-button">View</button>
                                <button class="update-button">Update</button>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr><td colspan="8" style="text-align: center;">No orders found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
