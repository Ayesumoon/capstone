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
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <h2 class="text-lg font-semibold">SevenDwarfs</h2>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center space-x-4">
                        <img alt="User profile picture" class="rounded-full" height="40" src="ID.jpg" width="40"/>
                        <div>
                            <h3 class="text-sm font-semibold">Aisha Cayago</h3>
                            <p class="text-xs text-gray-500">Admin</p>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="mt-6">
                <ul>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-tachometer-alt mr-2"></i><a href="dashboard.php">Dashboard</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-box mr-2"></i><a href="products.php">Products</a></li>
                    <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
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
        <h1 class="text-xl font-bold">Store Settings</h1>
      </div>
            <br>
            <div class="shadow-md p-4 bg-white rounded-lg">
                <label for="status" class="mr-2">Status:</label>
                <select id="status" class="border rounded-md p-2">
                    <option value="all">All</option>
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                </select>
                <label for="date" class="ml-4 mr-2">Date:</label>
                <input type="date" id="date" class="border rounded-md p-2">
            </div>
            
    <table class="order-table mt-6 w-full table-auto border-collapse bg-white shadow-md">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 border-b">Order ID</th>
                <th class="px-4 py-2 border-b">Customer ID</th>
                <th class="px-4 py-2 border-b">Product(s)</th>
                <th class="px-4 py-2 border-b">Total Amount</th>
                <th class="px-4 py-2 border-b">Order Status</th>
                <th class="px-4 py-2 border-b">Payment Method</th>
                <th class="px-4 py-2 border-b">Order Date</th>
                <th class="px-4 py-2 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)) { 
                foreach ($orders as $order) { ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b"><?php echo $order['order_id']; ?></td>
                        <td class="px-4 py-2 border-b"><?php echo $order['customer_id']; ?></td>
                        <td class="px-4 py-2 border-b"><?php echo $order['products']; ?></td>
                        <td class="px-4 py-2 border-b"><?php echo $order['total_amount']; ?></td>
                        <td class="px-4 py-2 border-b <?php echo strtolower($order['order_status_id']); ?>"><?php echo $order['order_status_id']; ?></td>
                        <td class="px-4 py-2 border-b"><?php echo $order['payment_method']; ?></td>
                        <td class="px-4 py-2 border-b"><?php echo $order['created_at']; ?></td>
                        <td class="px-4 py-2 border-b">
                            <button class="bg-blue-500 text-white px-4 py-2 rounded">View</button>
                            <button class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
                        </td>
                    </tr>
                <?php } 
            } else { ?>
                <tr><td colspan="8" class="text-center px-4 py-2 border-b">No orders found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>
