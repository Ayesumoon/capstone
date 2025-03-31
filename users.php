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
</head>
<body>
    <div class="sidebar">
        <h2>Dashboard</h2>
        <p>Welcome back, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?>!</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="users.php"><strong>Users</strong></a></li>
                <li><a href="payandtransac.php">Payment & Transactions</a></li>
                <li><a href="storesettings.php">Store Settings</a></li>
                <li><a href="logout.php">Log out</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <h2>Users</h2>
        <div class="filters">
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
