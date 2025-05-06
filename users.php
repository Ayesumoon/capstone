<?php
session_start();
require 'conn.php'; // Include database connection


$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = "Admin";
$admin_role = "Admin";

if ($admin_id) {
    $query = "
        SELECT 
            CONCAT(first_name, ' ', last_name) AS full_name, 
            r.role_name 
        FROM adminusers a
        LEFT JOIN roles r ON a.role_id = r.role_id
        WHERE a.admin_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $admin_name = $row['full_name'];
        $admin_role = $row['role_name'] ?? 'Admin';
    }
}


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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-md">
      <div class="p-4">
        <div class="flex items-center space-x-4">
          <img src="logo.png" alt="Logo" class="rounded-full" width="50" height="50" />
          <h2 class="text-lg font-semibold">SevenDwarfs</h2>
        </div>
        <div class="mt-4">
          <div class="flex items-center space-x-4">
            <img src="newID.jpg" alt="Admin" class="rounded-full" width="40" height="40" />
            <div>
              <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
              <p class="text-xs text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
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
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cash-register mr-2"></i><a href="pos.php">Point of Sale</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 overflow-auto">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Users</h1>
      </div>

      <div class="bg-white p-4 rounded-b shadow-md mb-6">
        <div class="flex justify-between items-center mb-4">
          <form method="GET" action="users.php" class="flex items-center gap-2">
            <label for="status" class="text-sm font-medium">Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="border rounded-md p-2 text-sm">
              <option value="">All</option>
              <option value="active" <?php echo ($status_filter == "active") ? 'selected' : ''; ?>>Active</option>
              <option value="inactive" <?php echo ($status_filter == "inactive") ? 'selected' : ''; ?>>Inactive</option>
            </select>
          </form>
          <a href="add_user.php" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700 text-sm font-medium">
            <i class="fas fa-plus mr-1"></i>Add User
          </a>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full table-auto border border-gray-200 shadow-md text-sm">
            <thead class="bg-gray-100 text-left text-gray-600">
              <tr>
                <th class="px-4 py-3 border">User ID</th>
                <th class="px-4 py-3 border">Username</th>
                <th class="px-4 py-3 border">Full Name</th>
                <th class="px-4 py-3 border">Email</th>
                <th class="px-4 py-3 border">Role</th>
                <th class="px-4 py-3 border">Status</th>
                <th class="px-4 py-3 border">Last Login</th>
                <th class="px-4 py-3 border">Last Logout</th>
                <th class="px-4 py-3 border">Actions</th>
              </tr>
            </thead>
            <tbody class="text-gray-700">
              <?php if (!empty($users)) { 
                foreach ($users as $user) { ?>
                <tr class="hover:bg-gray-50">
                
                  <td class="px-4 py-2 border"><?php echo $user['admin_id']; ?></td>
                  <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['username']); ?></td>
                  <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['full_name']); ?></td>
                  <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['admin_email']); ?></td>
                  <td class="px-4 py-2 border"><?php echo htmlspecialchars($user['role_name']); ?></td>
                  <td class="px-4 py-2 border font-semibold <?php echo strtolower($user['status']) === 'active' ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo ucfirst($user['status']); ?>
                  </td>
                  <td class="px-4 py-2 border"><?php echo $user['last_logged_in']; ?></td>
                  <td class="px-4 py-2 border"><?php echo $user['last_logged_out']; ?></td>
                  <td class="px-4 py-2 border">
                    <a href="edit_user.php?id=<?php echo $user['admin_id']; ?>" class="text-blue-500 hover:underline">Edit</a> |
                    <a href="delete_user.php?id=<?php echo $user['admin_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="text-red-500 hover:underline">Delete</a>
                  </td>
                </tr>
              <?php } 
              } else { ?>
                <tr>
                  <td colspan="10" class="text-center px-4 py-4 text-gray-500 border">No users found</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
