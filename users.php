<?php
session_start();
require 'conn.php'; // Include database connection

$users = [];
$status_filter = "";

// Check if a status is selected
if (isset($_GET['status']) && ($_GET['status'] == "active" || $_GET['status'] == "inactive")) {
    $status_filter = $_GET['status'];
    $status_id = ($status_filter == "active") ? 1 : 2;
}

// Fetch users and roles from the database
$sql = "
    SELECT u.admin_id, u.username, u.admin_email, 
           CASE 
               WHEN u.first_name IS NULL OR u.first_name = '' THEN 'Unknown' 
               ELSE u.first_name 
           END AS first_name, 
           COALESCE(NULLIF(u.last_name, ''), '') AS last_name, 
           COALESCE(NULLIF(r.role_name, ''), 'No Role Assigned') AS role_name, 
           u.status_id, u.created_at, u.last_logged_in, u.last_logged_out 
    FROM adminusers u
    LEFT JOIN roles r ON u.role_id = r.role_id";


// Apply status filter if selected
if (!empty($status_filter)) {
    $sql .= " WHERE u.status_id = ?";
}

$stmt = $conn->prepare($sql);

// Bind the parameter if filtering by status
if (!empty($status_filter)) {
    $stmt->bind_param("i", $status_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ensure first and last name are not null or empty
        $first_name = !empty($row['first_name']) ? $row['first_name'] : 'Unknown';
        $last_name = !empty($row['last_name']) ? $row['last_name'] : '';

        // Combine first and last name
        $row['full_name'] = trim($first_name . ' ' . $last_name);

        // Format timestamps
        $row['last_logged_in'] = (!empty($row['last_logged_in'])) ? date("F j, Y g:i A", strtotime($row['last_logged_in'])) : 'Never';
        $row['last_logged_out'] = (!empty($row['last_logged_out'])) ? date("F j, Y g:i A", strtotime($row['last_logged_out'])) : 'N/A';

        // Convert status_id to a readable name
        $row['status'] = ($row['status_id'] == 1) ? "Active" : "Inactive";

        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/users.css">
    <title>Users</title>
    <html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Dashboard
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
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="log.php">Log out</a></li>
        </ul>
      </nav>
    </div>
    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Users</h1>
      </div>
      <div class="flex items-center justify-between mb-4">
        
            <form method="GET" action="users.php" class="status">
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Status</option> 
                    <option value="active" <?php echo ($status_filter == "active") ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($status_filter == "inactive") ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <noscript><input type="submit" value="Filter"></noscript>
            </form>
            <a href="add_user.php"class="add_user"> Add User</a>
        </div>
        <table class="users-table">
            <thead>
                <tr>
                    <th><i class="fas fa-trash-alt"></i></th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Logged In</th>
                    <th>Last Logged Out</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)) { 
                    foreach ($users as $user) { ?>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td><?php echo $user['admin_id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['full_name']; ?></td>
                            <td><?php echo $user['admin_email']; ?></td>
                            <td><?php echo htmlspecialchars($user['role_name']); ?></td>  <!-- Display role name -->
                            <td class="<?php echo strtolower($user['status']); ?>"><?php echo $user['status']; ?></td>
                            <td><?php echo $user['last_logged_in']; ?></td>
                            <td><?php echo $user['last_logged_out']; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['admin_id']; ?>">Edit</a> |
                                <a href="delete_user.php?id=<?php echo $user['admin_id']; ?>" 
                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone!')">Delete</a>
                            </td>
                        </tr>
                <?php } 
                } else { ?>
                    <tr><td colspan="10" style="text-align: center;">No users found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
