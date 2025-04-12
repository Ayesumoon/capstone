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
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Customers
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
       <img alt="User profile picture" class="rounded-full" height="40" src="ID.jpg" width="40"/>
       <div>
        <h3 class="text-sm font-semibold">
         Aisha Cayago
        </h3>
        <p class="text-xs text-gray-500">
         Admin
        </p>
       </div>
      </div>
     </div>
    </div>
    <nav class="mt-6">
     <ul>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-tachometer-alt mr-2"></i><a href="dashboard.php">Dashboard</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-box mr-2"></i><a href="products.php">Products</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="log.php">Log out</a></li>
        </ul>
      </nav>
    </div>
    <div class="main-content">
    <div class="flex-1 p-6">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Customers</h1>
      </div>
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
