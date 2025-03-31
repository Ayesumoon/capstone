<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="payandtransac.css">
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
    // Generate options for the last 7 days
    for ($i = 0; $i < 7; $i++) {
        $date = date("Y-m-d", strtotime("-$i days")); // Generate past dates
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
                <tr>
                    <td>1</td>
                    <td>101</td>
                    <td>John Doe</td>
                    <td>Credit Card</td>
                    <td>$50.00</td>
                    <td>Completed</td>
                    <td>2024-03-24 14:30</td>
                    <td><a href="#">View Details</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
