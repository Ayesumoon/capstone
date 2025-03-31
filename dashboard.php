<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php"><strong>Dashboard</strong></a></li>
                <li><a href="products.php">Products</a></li>
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
        <div class="header"><h2>Dashboard</h2></div>
        <div class="dashboard-cards">
            <div class="card new-orders">New Orders</div>
            <div class="card sales">Sales</div>
            <div class="card revenue">Revenue</div>
        </div>
        <div class="section">
            <h3>Recent Orders</h3>
        </div>
        <div class="section">
            <h3>Activities</h3>
        </div>
    </div>
</body>
</html>