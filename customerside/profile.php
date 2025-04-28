<?php
session_start();
require 'conn.php'; // your DB connection file

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['customer_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone, address, profile_picture FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 text-gray-800 font-sans">
  <div class="max-w-lg mx-auto mt-10 bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-3xl font-semibold text-pink-600 mb-8 text-center">My Profile</h2>
    
    <!-- Profile Details -->
    <div class="space-y-6">
      <!-- Profile Picture -->
      <div class="flex justify-center mb-4">
        <img id="profilePreview" src="<?= htmlspecialchars($user['profile_picture'] ?? 'default-avatar.png') ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover border-4 border-pink-300">
      </div>

      <!-- Profile Info -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">First Name</label>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['first_name']) ?></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Last Name</label>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['last_name']) ?></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['email']) ?></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Phone</label>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['phone']) ?></p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Address</label>
          <p class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['address']) ?></p>
        </div>
      </div>

      <!-- Edit Profile Button -->
      <div class="mt-6 text-center">
        <a href="edit_profile.php" class="w-full bg-pink-600 text-white py-3 rounded-md hover:bg-pink-700 transition duration-200 inline-block">Edit Profile</a>
      </div>

      <!-- Back Button -->
      <div class="mt-4 text-center">
        <a href="shop.php" class="w-full bg-gray-300 text-gray-800 py-3 rounded-md hover:bg-gray-400 transition duration-200 inline-block">Back to Shop</a>
      </div>
    </div>
  </div>
</body>
</html>
