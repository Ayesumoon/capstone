<?php
require 'conn.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $address = $_POST['address'];
    $status_id = 1; // Default to 'Active'

    // Insert data into database (include status_id)
    $sql = "INSERT INTO customers (first_name, last_name, email, phone, password_hash, address, status_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $password, $address, $status_id);
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    }
     else {
        echo "Error: " . $stmt->error;
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
    <title>Sign Up</title>
</head>
    <link rel="stylesheet" href="css/signup.css">
</head>
</head>
<body>
    <img src="logo.png" alt="Seven Dwarfs Logo" class="w-50 h-50"/>
</head>
<body>
    <div class="maincontent">
    <h2>Sign Up</h2>
        <form method="post">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
            
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
            
            <label>Email:</label>
            <input type="email" name="email" required>
            
            <label>Phone:</label>
            <input type="text" name="phone" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <label>Address:</label>
            <textarea name="address" required></textarea>
            
            <input type="submit" value="Sign Up">

            <a href="login.php" class="loginbtn">Log In Here</a>
            
        </form>
    </div>
</body>
</html>

