<?php
session_start();
include 'conn.php'; // Include your database connection file

// Check if user is logged in (optional check)
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch store settings data from the database
$sql = "SELECT * FROM store_settings WHERE id = 1"; // Assuming you have one record for store settings
$result = mysqli_query($conn, $sql);

$store_settings = mysqli_fetch_assoc($result); // Fetch the data as an associative array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Settings</title>
    <link rel="stylesheet" href="css/storesettings.css">
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
                <li><a href="users.php">Users</a></li>
                <li><a href="payandtransac.php">Payment & Transactions</a></li>
                <li><strong><a href="storesettings.php">Store Settings</a></strong></li>
                <li><a href="login.php">Log out</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <h1>Store Settings</h1>
        <div class="settings-container">
            <!-- General Store Information Section -->
            <div class="section">
                <h2>General Store Information</h2>
                <div class="settings-group">
                    <label>Store Name:</label>
                    <input type="text" value="<?php echo htmlspecialchars($store_settings['store_name']); ?>" disabled>
                </div>
                <div class="settings-group">
                    <label>Store Description:</label>
                    <textarea disabled><?php echo htmlspecialchars($store_settings['store_description']); ?></textarea>
                </div>
                <div class="settings-group">
                    <label>Store Email:</label>
                    <input type="email" value="<?php echo htmlspecialchars($store_settings['store_email']); ?>" disabled>
                </div>
                <div class="settings-group">
                    <label>Contact:</label>
                    <input type="text" value="<?php echo htmlspecialchars($store_settings['contact']); ?>" disabled>
                </div>
                <div class="settings-group">
                    <label>Address:</label>
                    <input type="text" value="<?php echo htmlspecialchars($store_settings['address']); ?>" disabled>
                </div>
                <div class="settings-group">
                    <label>Timezone & Locale:</label>
                    <input type="text" value="<?php echo htmlspecialchars($store_settings['timezone_locale']); ?>" disabled>
                </div>
                <button class="edit-btn"><a href="editstore.php">Edit</a></button>
            </div>

            <!-- Theme & Design Section -->
            <div class="section">
                <h2>Theme & Design</h2>
                <div class="settings-group">
                    <label>Current Theme:</label>
                    <input type="text" value="<?php echo htmlspecialchars($store_settings['theme']); ?>" disabled>
                </div>
                <div class="settings-group">
                    <label>Homepage Layout:</label>
                    <input type="text" value="<?php echo htmlspecialchars($store_settings['homepage_layout']); ?>" disabled>
                </div>
    
                <button class="edit-btn"><a href="editstore.php">Edit</a></button>
            </div>

            <!-- Shipping & Delivery Section -->
            <div class="section">
                <h2>Shipping & Delivery Settings</h2>
                <div class="settings-group">
                    <label>Shipping Methods:</label>
                    <select>
                        <option><?php echo htmlspecialchars($store_settings['shipping_method']); ?></option>
                    </select>
                </div>
                <div class="settings-group">
                    <label>Flat Rate Shipping:</label>
                    <select>
                        <option><?php echo htmlspecialchars($store_settings['flat_rate_shipping']); ?></option>
                    </select>
                </div>
                <div class="settings-group">
                    <label>Delivery Time Estimates:</label>
                    <select>
                        <option><?php echo htmlspecialchars($store_settings['delivery_time']); ?></option>
                    </select>
                </div>
            </div>

            <!-- User & Security Settings Section -->
            <div class="section">
                <h2>User & Security Settings</h2>
                <div class="settings-group">
                    <label>Two Factor Authentication:</label>
                    <input type="checkbox" <?php echo $store_settings['two_factor_auth'] ? 'checked' : ''; ?>>
                </div>
                <div class="settings-group">
                    <label>Password Reset Options:</label>
                    <button class="reset-btn">Reset Password</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
