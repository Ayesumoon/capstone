<?php
    session_start();
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
            <div class="section">
                <h2>General</h2>
                <div class="settings-group">
                    <label>Store Name:</label>
                    <input type="text" value="Seven Dwarfs Boutique" disabled>
                </div>
                <div class="settings-group">
                    <label>Store Email:</label>
                    <input type="email" value="sevendwarfsboutique7@gmail.com" disabled>
                </div>
                <div class="settings-group">
                    <label>Contact:</label>
                    <input type="text" value="+63 123 456 789" disabled>
                </div>
                <div class="settings-group">
                    <label>Address:</label>
                    <input type="text" value="Bayambang, Pangasinan" disabled>
                </div>
                <button class="edit-btn">Edit</button>
            </div>

            <div class="section">
                <h2>Shipping & Delivery Settings</h2>
                <div class="settings-group">
                    <label>Shipping Methods:</label>
                    <select>
                        <option>Local Delivery</option>
                        <option>Express Shipping</option>
                    </select>
                </div>
                <div class="settings-group">
                    <label>Flat Rate Shipping:</label>
                    <select>
                        <option>$5.00</option>
                        <option>$10.00</option>
                    </select>
                </div>
                <div class="settings-group">
                    <label>Delivery Time Estimates:</label>
                    <select>
                        <option>1 - 3 Business Days</option>
                        <option>4 - 7 Business Days</option>
                    </select>
                </div>
            </div>

            <div class="section">
                <h2>User & Security Settings</h2>
                <div class="settings-group">
                    <label>Two Factor Authentication:</label>
                    <input type="checkbox">
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