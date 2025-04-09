<?php
session_start();
require 'conn.php'; // Make sure this connects to your database properly
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/payandtransac.css">
    <title>Payment & Transactions</title>
</head>
<body>
    <div class="sidebar">
        <h2>Payment & Transaction</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="users.php">Users</a></li>
                <li><strong><a href="payandtransac.php">Payment & Transactions</a></strong></li>
                <li><a href="storesettings.php">Store Settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <h1>Payment & Transactions</h1>
        <div class="filters">
            <select class="date">
                <option value="">Select Date</option>
                <?php
                for ($i = 0; $i < 7; $i++) {
                    $date = date("Y-m-d", strtotime("-$i days"));
                    echo "<option value='$date'>$date</option>";
                }
                ?>
            </select>
        </div>

        <table class="pay-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Payment Method</th>
                    <th>Total</th>
                    <th>Payment Status</th>
                    <th>Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
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
                        echo "<tr>
                            <td>{$row['transaction_id']}</td>
                            <td>{$row['order_id']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['payment_method_name']}</td>
                            <td>\${$row['total']}</td>
                            <td>{$row['order_status_name']}</td>
                            <td>{$row['date_time']}</td>
                            <td><a href='transaction_details.php?id={$row['transaction_id']}'>View Details</a></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No transactions found.</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
