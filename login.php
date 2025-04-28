<?php
session_start();
require 'conn.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT admin_id, admin_email, password_hash, role_id, username FROM adminusers WHERE admin_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $db_email, $db_password, $role, $username);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $admin_id;
            $_SESSION["email"] = $db_email;
            $_SESSION["role"] = $role;
            $_SESSION["username"] = $username; // Store username in session

            // Update last_logged_in timestamp
            $updateLogin = "UPDATE adminusers SET last_logged_in = NOW() WHERE admin_id = ?";
            $stmtUpdate = $conn->prepare($updateLogin);
            $stmtUpdate->bind_param("i", $admin_id);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            header("Location: dashboard.php");
            exit;
        }
    }
    
    $stmt->close();

    // If not an admin, check if it's a customer
    $sql = "SELECT customer_id, email, password_hash FROM customers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($customer_id, $db_email, $db_password_hash);
        $stmt->fetch();

        // Verify password for customer
        if (password_verify($password, $db_password_hash)) { // Fixed variable
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $customer_id;
            $_SESSION["email"] = $db_email;
            $_SESSION["role"] = "Customer";

            // Reset last_logged_out when logging in
            $resetLogout = "UPDATE adminusers SET last_logged_out = NULL WHERE admin_id = ?";
            $stmtReset = $conn->prepare($resetLogout);
            $stmtReset->bind_param("i", $admin_id);
            $stmtReset->execute();
            $stmtReset->close();


            header("Location: customerside/homepage.php"); // Redirect to customer page
            exit;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<link rel="stylesheet" href="css/login.css">
<body>
<img src="logo.png" alt="Seven Dwarfs Logo" class="w-50 h-50"/>
<div class="maincontent">
    <h2>Admin Login</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Password:</label>
        <input type="password" name="password" required>
        <br>
        <br>
        <input type="submit" value="Login">
    </form>
    </a>
</body>
</html>