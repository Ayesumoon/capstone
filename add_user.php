<?php
session_start();
require 'conn.php'; // Database connection

$message = '';

// Fetch available roles
$roles = [];
$role_query = "SELECT role_id, role_name FROM roles";
$role_result = $conn->query($role_query);
while ($row = $role_result->fetch_assoc()) {
    $roles[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $status_id = $_POST['status_id'];
    $created_at = date("Y-m-d H:i:s");
    $role_id = $_POST['role_id']; // Now correctly using role_id

    // Check if username or email already exists
    $check_query = "SELECT admin_id FROM adminusers WHERE username = ? OR admin_email = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['message'] = "Error: Username or Email already exists!";
        header("Location: add_user.php");
        exit();
    } else {
        // Insert new user
        $sql = "INSERT INTO adminusers (username, admin_email, password_hash, role_id, status_id, created_at, first_name, last_name) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssissss", $username, $email, $password, $role_id, $status_id, $created_at, $first_name, $last_name);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User added successfully!";
            header("Location: users.php"); // Redirect to users page
            exit();
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            header("Location: add_user.php");
            exit();
        }
        $stmt->close();
    }
    $check_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="css/add_user.css">
</head>
<body>
    <div class="container">
        <h2>Add New User</h2>
        <?php if (isset($_SESSION['message'])) { 
            echo "<p style='color: red;'>" . $_SESSION['message'] . "</p>"; 
            unset($_SESSION['message']);
        } ?>
        <form action="add_user.php" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>First Name:</label>
            <input type="text" name="first_name" required>
            
            <label>Last Name:</label>
            <input type="text" name="last_name" required>

            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <label>Role:</label>
            <select name="role_id" required>
                <?php foreach ($roles as $role) { ?>
                    <option value="<?php echo $role['role_id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option>
                <?php } ?>
            </select>
            
            <label>Status:</label>
            <select name="status_id" required>
                <option value="1">Active</option>
                <option value="2">Inactive</option>
            </select>
            
            <button type="submit">Add User</button>
            <button type="button" onclick="window.location.href='users.php'">Cancel</button>
        </form>
    </div>
</body>
</html>
