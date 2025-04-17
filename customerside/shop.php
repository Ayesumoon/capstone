<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "dbms");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch all products that are in stock
$sql = "SELECT product_name, description, price_id AS price, image_url 
        FROM products 
        WHERE stocks > 0 
        ORDER BY product_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shop | Seven Dwarfs Boutique</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-50 text-gray-800">

  <!-- Navbar -->
  <nav class="bg-pink-100 shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-pink-600">Seven Dwarfs Boutique</h1>
      <ul class="flex space-x-6">
        <li><a href="homepage.php" class="hover:text-pink-500">Home</a></li>
        <li><a href="shop.php" class="hover:text-pink-500 font-semibold">Shop</a></li>
        <li><a href="about.php" class="hover:text-pink-500">About</a></li>
        <li><a href="contact.php" class="hover:text-pink-500">Contact</a></li>
      </ul>
    </div>
  </nav>

  <!-- Shop Banner -->
  <section class="bg-pink-200 text-center py-16">
    <h2 class="text-4xl font-bold text-pink-800">Shop All Products</h2>
    <p class="text-lg text-pink-900 mt-2">Browse our magical collection 🌟</p>
  </section>

  <!-- Product Listing -->
  <section class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="bg-white rounded-xl shadow-md p-4">
            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="rounded-md mb-4 w-full h-48 object-cover">
            <h4 class="text-xl font-semibold text-pink-800"><?= htmlspecialchars($row['product_name']) ?></h4>
            <p class="text-sm text-gray-600"><?= htmlspecialchars($row['description']) ?></p>
            <p class="text-pink-600 mt-2 font-semibold">₱<?= number_format($row['price'], 2) ?></p>
            <button class="mt-4 w-full bg-pink-500 text-white py-2 rounded hover:bg-pink-600 transition">Add to Cart</button>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-gray-500 col-span-3">No products available.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-pink-100 text-center py-6">
    <p class="text-pink-700">&copy; 2025 Seven Dwarfs Boutique. All rights reserved.</p>
  </footer>

</body>
</html>

<?php $conn->close(); ?>
