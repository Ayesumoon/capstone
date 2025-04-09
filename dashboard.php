<?php
session_start();
include 'conn.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Query to get the count of new orders
$new_orders_query = "SELECT COUNT(*) AS new_orders FROM orders WHERE order_status = 'new'";
$new_orders_result = mysqli_query($conn, $new_orders_query);
$new_orders_data = mysqli_fetch_assoc($new_orders_result);
$new_orders_count = $new_orders_data['new_orders'];

// Query to get total sales
$sales_query = "SELECT SUM(total_amount) AS total_sales FROM orders WHERE order_status = 'completed'";
$sales_result = mysqli_query($conn, $sales_query);
$sales_data = mysqli_fetch_assoc($sales_result);
$total_sales = $sales_data['total_sales'];

// Query to get total revenue
$revenue_query = "SELECT SUM(amount_paid) AS total_revenue FROM transactions WHERE transaction_status = 'completed'";
$revenue_result = mysqli_query($conn, $revenue_query);
$revenue_data = mysqli_fetch_assoc($revenue_result);
$total_revenue = $revenue_data['total_revenue'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css">
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
            <div class="card new-orders">New Orders: <?php echo $new_orders_count; ?></div>
            <div class="card sales">Sales: $<?php echo number_format($total_sales, 2); ?></div>
            <div class="card revenue">Revenue: $<?php echo number_format($total_revenue, 2); ?></div>
        </div>
        <div class="section">
            <h3>Recent Orders</h3>
            <!-- Add code here to display recent orders if needed -->
        </div>
        <div class="section">
            <h3>Activities</h3>
            <!-- Add code here to display recent activities if needed -->
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
