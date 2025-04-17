<?php
$conn = new mysqli("localhost", "root", "", "dbms");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT product_name, description, price_id AS price, image_url 
        FROM products 
        WHERE stocks > 0 
        ORDER BY product_id DESC 
        LIMIT 6";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seven Dwarfs Boutique</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 text-gray-800">

  <!-- Navbar -->
  <nav class="bg-pink-100 shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-pink-600">Seven Dwarfs Boutique</h1>
      <ul class="flex space-x-6">
        <li><a href="homepage.php" class="hover:text-pink-500">Home</a></li>
        <li><a href="shop.php" class="hover:text-pink-500">Shop</a></li>
        <li><a href="#" class="hover:text-pink-500">About</a></li>
        <li><a href="#" class="hover:text-pink-500">Contact</a></li>
      </ul>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="bg-pink-200 text-center py-20">
    <h2 class="text-4xl font-bold text-pink-800 mb-4">Welcome to Seven Dwarfs Boutique</h2>
    <p class="text-lg text-pink-900 mb-6">Where fashion meets fairytales ✨</p>
    <a href="shop.php" class="bg-pink-600 text-white px-6 py-3 rounded-full hover:bg-pink-700 transition">Shop Now</a>
  </section>

  <!-- Products Section -->
  <section class="max-w-7xl mx-auto px-4 py-16">
  <h3 class="text-3xl font-bold text-center text-pink-700 mb-12">Featured Products</h3>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="bg-white rounded-xl shadow-md p-4">
        <img src="<?= $row['image_url'] ?>" alt="<?= $row['product_name'] ?>" class="rounded-md mb-4 w-full h-48 object-cover">
        <h4 class="text-xl font-semibold text-pink-800"><?= $row['product_name'] ?></h4>
        <p class="text-sm text-gray-600"><?= $row['description'] ?></p>
        <p class="text-pink-600 mt-2 font-semibold">₱<?= number_format($row['price'], 2) ?></p>
        <button class="mt-4 w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600 transition">Add to Cart</button>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<?php $conn->close(); ?>
  <!-- Footer -->
  <footer class="bg-pink-100 text-center py-6">
    <p class="text-pink-700">&copy; 2025 Seven Dwarfs Boutique. All rights reserved.</p>
  </footer>

</body>
</html>
