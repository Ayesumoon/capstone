<?php
session_start();
require 'conn.php'; // your DB connection file

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

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
  <script>
    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function () {
        const output = document.getElementById('profilePreview');
        output.src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</head>
<body class="bg-pink-50 text-gray-800 font-sans">
  <div class="max-w-lg mx-auto mt-10 bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-3xl font-semibold text-pink-600 mb-8 text-center">My Profile</h2>
    
    <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
      <!-- Profile Picture -->
      <div class="flex flex-col items-center space-y-3">
        <img id="profilePreview" src="<?= htmlspecialchars($user['profile_picture'] ?? 'default-avatar.png') ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover border-4 border-pink-300">
        <input type="file" name="profile_picture" accept="image/*" onchange="previewImage(event)" class="text-sm text-gray-600 cursor-pointer">
      </div>

      <!-- Profile Info -->
      <div class="space-y-4">
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
          <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" id="first_name" class="w-full border p-3 rounded-md mt-1 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
        </div>
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
          <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" id="last_name" class="w-full border p-3 rounded-md mt-1 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" id="email" class="w-full border p-3 rounded-md mt-1 focus:outline-none focus:ring-2 focus:ring-pink-500" required>
        </div>
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" id="phone" class="w-full border p-3 rounded-md mt-1 focus:outline-none focus:ring-2 focus:ring-pink-500">
        </div>
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
          <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" id="address" class="w-full border p-3 rounded-md mt-1 focus:outline-none focus:ring-2 focus:ring-pink-500">
        </div>
      </div>

      <button type="submit" class="w-full bg-pink-600 text-white py-3 rounded-md hover:bg-pink-700 transition duration-200">Update Profile</button>
    </form>
  </div>
</body>
</html>
