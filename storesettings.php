<?php
session_start();
include 'conn.php'; // Include your database connection file

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
       <img alt="User profile picture" class="rounded-full" height="40" src="newID.jpg" width="40"/>
       <div>
       <h3 class="text-sm font-semibold">
        <?php echo htmlspecialchars($admin_name); ?>
        </h3>
        <p class="text-xs text-gray-500">
        <?php echo htmlspecialchars($admin_role); ?>
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
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-pink-600 text-white p-4 rounded-t">
        <h1 class="text-xl font-bold">Store Settings</h1>
      </div>
      <div class="bg-white p-6 rounded-b shadow-md space-y-6">
        
       <!-- General Store Information -->
<div class="border rounded shadow p-4">
  <h2 class="text-lg font-bold mb-4">General Store Information</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
      <label class="block text-gray-700">Store Name</label>
      <input class="w-full p-2 border rounded" type="text" readonly value="<?php echo htmlspecialchars($store_settings['store_name']); ?>">
    </div>
    <div>
      <label class="block text-gray-700">Store Email</label>
      <input class="w-full p-2 border rounded" type="email" readonly value="<?php echo htmlspecialchars($store_settings['store_email']); ?>">
    </div>
  </div>
  <div class="mb-4">
    <label class="block text-gray-700">Store Description</label>
    <textarea class="w-full p-2 border rounded" readonly rows="3"><?php echo htmlspecialchars($store_settings['store_description']); ?></textarea>
  </div>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
      <label class="block text-gray-700">Contact</label>
      <input class="w-full p-2 border rounded" type="text" readonly value="<?php echo htmlspecialchars($store_settings['contact']); ?>">
    </div>
    <div>
      <label class="block text-gray-700">Address</label>
      <input class="w-full p-2 border rounded" type="text" readonly value="<?php echo htmlspecialchars($store_settings['address']); ?>">
    </div>
  </div>
  <div class="mb-4">
    <label class="block text-gray-700">Timezone & Locale</label>
    <input class="w-full p-2 border rounded" type="text" readonly value="<?php echo htmlspecialchars($store_settings['timezone_locale']); ?>">
  </div>
  <a href="editstore.php" class="bg-pink-600 text-white px-4 py-2 rounded inline-block">Edit</a>
</div>


       <!-- Theme & Design -->
<div class="border rounded shadow p-4">
  <h2 class="text-lg font-bold mb-4">Theme & Design</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
      <label class="block text-gray-700">Current Theme</label>
      <input class="w-full p-2 border rounded" type="text" readonly value="<?php echo htmlspecialchars($store_settings['theme']); ?>">
    </div>
    <div>
      <label class="block text-gray-700">Homepage Layout</label>
      <input class="w-full p-2 border rounded" type="text" readonly value="<?php echo htmlspecialchars($store_settings['homepage_layout']); ?>">
    </div>
  </div>
  <a href="editstore.php" class="bg-pink-600 text-white px-4 py-2 rounded inline-block">Edit</a>
</div>


        <!-- Shipping & Delivery -->
<div class="border rounded shadow p-4">
  <h2 class="text-lg font-bold mb-4">Shipping & Delivery Settings</h2>
  <div class="grid grid-cols-1 gap-4 mb-4">
    <div class="relative">
      <label class="block text-gray-700 mb-1">Shipping Methods</label>
      <select class="w-full p-2 border rounded pr-10 appearance-none">
        <option value="Shipping Method 1" <?php echo $store_settings['shipping_method'] == 'Shipping Method 1' ? 'selected' : ''; ?>>Shipping Method 1</option>
        <option value="Shipping Method 2" <?php echo $store_settings['shipping_method'] == 'Shipping Method 2' ? 'selected' : ''; ?>>Shipping Method 2</option>
        <option value="Shipping Method 3" <?php echo $store_settings['shipping_method'] == 'Shipping Method 3' ? 'selected' : ''; ?>>Shipping Method 3</option>
      </select>
      <i class="fas fa-chevron-down absolute right-3 top-9 text-gray-400 pointer-events-none"></i>
    </div>

    <div class="relative">
      <label class="block text-gray-700 mb-1">Flat Rate Shipping</label>
      <select class="w-full p-2 border rounded pr-10 appearance-none">
        <option value="Flat Rate 1" <?php echo $store_settings['flat_rate_shipping'] == 'Flat Rate 1' ? 'selected' : ''; ?>>Flat Rate 1</option>
        <option value="Flat Rate 2" <?php echo $store_settings['flat_rate_shipping'] == 'Flat Rate 2' ? 'selected' : ''; ?>>Flat Rate 2</option>
        <option value="Flat Rate 3" <?php echo $store_settings['flat_rate_shipping'] == 'Flat Rate 3' ? 'selected' : ''; ?>>Flat Rate 3</option>
      </select>
      <i class="fas fa-chevron-down absolute right-3 top-9 text-gray-400 pointer-events-none"></i>
    </div>

    <div class="relative">
      <label class="block text-gray-700 mb-1">Delivery Time Estimates</label>
      <select class="w-full p-2 border rounded pr-10 appearance-none">
        <option value="1-3 Days" <?php echo $store_settings['delivery_time'] == '1-3 Days' ? 'selected' : ''; ?>>1-3 Days</option>
        <option value="4-7 Days" <?php echo $store_settings['delivery_time'] == '4-7 Days' ? 'selected' : ''; ?>>4-7 Days</option>
        <option value="7-14 Days" <?php echo $store_settings['delivery_time'] == '7-14 Days' ? 'selected' : ''; ?>>7-14 Days</option>
      </select>
      <i class="fas fa-chevron-down absolute right-3 top-9 text-gray-400 pointer-events-none"></i>
    </div>
  </div>
</div>




       <!-- User & Security Settings Section -->
<div class="section border rounded shadow p-4">
    <h2 class="text-lg font-bold mb-4">User & Security Settings</h2>
    <div class="settings-group mb-4">
        <label class="block text-gray-700">Two Factor Authentication:</label>
        <input type="checkbox" id="twoFactorAuth" <?php echo $store_settings['two_factor_auth'] ? 'checked' : ''; ?> onchange="toggleTwoFactorAuth()">
    </div>
    <div class="settings-group mb-4">
        <label class="block text-gray-700">Password Reset Options:</label>
        <button class="reset-btn bg-red-500 text-white px-4 py-2 rounded" onclick="resetPassword()">Reset Password</button>
    </div>
</div>

<script>
    // Function to toggle Two Factor Authentication status
    function toggleTwoFactorAuth() {
        const isChecked = document.getElementById('twoFactorAuth').checked;
        
        // Here you would typically make an AJAX request to save the state to the database.
        // For example:
        // fetch('/update-settings', {
        //     method: 'POST',
        //     body: JSON.stringify({ two_factor_auth: isChecked }),
        //     headers: { 'Content-Type': 'application/json' }
        // });
        
        console.log('Two Factor Authentication:', isChecked ? 'Enabled' : 'Disabled');
    }

    // Function to handle Password Reset
    function resetPassword() {
        // Here you can define the logic for resetting the password
        alert('Password reset link has been sent to your email.');
    }
</script>



<?php
// Close the database connection
mysqli_close($conn);
?>