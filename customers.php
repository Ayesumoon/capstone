<?php
    session_start();
    require 'conn.php'; // Database connection

    $customers = [];

    // Fetch customers with status names
    $sql = "
        SELECT c.customer_id, 
               CONCAT(c.first_name, ' ', c.last_name) AS name, 
               c.email, 
               c.phone, 
               s.status_name, 
               c.created_at 
        FROM customers c
        INNER JOIN status s ON c.status_id = s.status_id
    ";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Error in SQL query: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/customers.css">
    <title>Customers</title>
</head>
<body>
    <div class="sidebar">
        <h2>Customers</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><strong><a href="customers.php">Customers</a></strong></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="payandtransac.php">Payment & Transactions</a></li>
                <li><a href="storesettings.php">Store Settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <div class="header">Customers</div>
        <br>
        <label for="status">Status:</label>
        <select id="status">
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        
        <table class="customer-table">
            <thead>
                <tr>
                    <th><i class="fas fa-trash-alt"></i></th>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($customers)) { 
                    foreach ($customers as $customer) { ?>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><?php echo $customer['customer_id']; ?></td>
                            <td><?php echo $customer['name']; ?></td>
                            <td><?php echo $customer['email']; ?></td>
                            <td><?php echo $customer['phone']; ?></td>
                            <td class="status <?php echo strtolower($customer['status_name']); ?>">
                                <?php echo $customer['status_name']; ?>
                            </td>
                            <td><?php echo $customer['created_at']; ?></td>
                            <td class="actions">
                                <a href="#" class="view">View</a>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr><td colspan="8" style="text-align: center;">No customers found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
