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
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
<div class="w-64 bg-white shadow-md min-h-screen" x-data="{ userMenu: false, productMenu: false }">
  <!-- Header -->
  <div class="p-4">
    <div class="flex items-center space-x-4">
      <img src="logo.png" alt="Logo" class="rounded-full w-12 h-12" />
      <h2 class="text-lg font-semibold">SevenDwarfs</h2>
    </div>
    <div class="mt-4 flex items-center space-x-4">
      <img src="newID.jpg" alt="Admin" class="rounded-full w-10 h-10" />
      <div>
        <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
      </div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="mt-6">
    <ul>
      <!-- Dashboard -->
      <li class="px-4 py-2 hover:bg-gray-200">
        <a href="dashboard.php" class="flex items-center">
          <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
      </li>

      <!-- User Management -->
      <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="userMenu = !userMenu">
        <div class="flex items-center justify-between">
          <span class="flex items-center">
            <i class="fas fa-users-cog mr-2"></i>User Management
          </span>
          <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': userMenu }"></i>
        </div>
      </li>
      <ul x-show="userMenu" x-transition class="pl-8 text-sm text-gray-700 space-y-1">
        <li class="py-1 bg-pink-100 text-pink-600 rounded"><a href="users.php" class="flex items-center"><i class="fas fa-user mr-2"></i>User</a></li>
        <li class="py-1 hover:text-pink-600"><a href="user_types.php" class="flex items-center"><i class="fas fa-id-badge mr-2"></i>Type</a></li>
        <li class="py-1 hover:text-pink-600"><a href="user_status.php" class="flex items-center"><i class="fas fa-toggle-on mr-2"></i>Status</a></li>
        <li class="py-1">
    <a href="customers.php" class="flex items-center space-x-2 hover:text-pink-600">
      <i class="fas fa-users"></i>
      <span>Customer</span>
    </a>
  </li>
      </ul>

      <!-- Product Management -->
      <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="productMenu = !productMenu">
        <div class="flex items-center justify-between">
          <span class="flex items-center">
            <i class="fas fa-box-open mr-2"></i>Product Management
          </span>
          <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': productMenu }"></i>
        </div>
      </li>
      <ul x-show="productMenu" x-transition class="pl-8 text-sm text-gray-700 space-y-1">
        <li class="py-1 hover:text-pink-600"><a href="categories.php" class="flex items-center"><i class="fas fa-tags mr-2"></i>Category</a></li>
        <li class="py-1 hover:text-pink-600"><a href="products.php" class="flex items-center"><i class="fas fa-box mr-2"></i>Product</a></li>
        <li class="py-1 hover:text-pink-600"><a href="inventory.php" class="flex items-center"><i class="fas fa-warehouse mr-2"></i>Inventory</a></li>
      </ul>

      <!-- Other Pages -->
      <li class="px-4 py-2 hover:bg-gray-200">
        <a href="orders.php" class="flex items-center">
          <i class="fas fa-shopping-cart mr-2"></i>Orders
        </a>
      </li>
      <li class="px-4 py-2 hover:bg-gray-200">
        <a href="pos.php" class="flex items-center">
          <i class="fas fa-cash-register mr-2"></i>Point of Sale
        </a>
      </li>
      <li class="px-4 py-2 hover:bg-gray-200">
        <a href="payandtransac.php" class="flex items-center">
          <i class="fas fa-money-check-alt mr-2"></i>Payment & Transactions
        </a>
      </li>
      <li class="px-4 py-2 hover:bg-gray-200">
        <a href="storesettings.php" class="flex items-center">
          <i class="fas fa-cog mr-2"></i>Store Settings
        </a>
      </li>
      <li class="px-4 py-2 hover:bg-gray-200">
        <a href="logout.php" class="flex items-center">
          <i class="fas fa-sign-out-alt mr-2"></i>Log out
        </a>
      </li>
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
