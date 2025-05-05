<?php
session_start();

// Make sure a product_id is sent
if (!isset($_POST['product_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'No product ID provided']);
  exit();
}

$product_id = (int) $_POST['product_id'];

// Optional: fetch the product from the database to verify it exists
require 'conn.php';
$stmt = $conn->prepare("SELECT product_id, product_name, price_id, image_url FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
  echo json_encode(['status' => 'error', 'message' => 'Product not found']);
  exit();
}

// Save to session cart
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Check if already in cart, increase quantity if yes
if (isset($_SESSION['cart'][$product_id])) {
  $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
  $_SESSION['cart'][$product_id] = [
    'product_id' => $product['product_id'],
    'product_name' => $product['product_name'],
    'price' => $product['price_id'],
    'image_url' => $product['image_url'],
    'quantity' => 1
  ];
}

echo json_encode(['status' => 'success', 'cartCount' => array_sum(array_column($_SESSION['cart'], 'quantity'))]);
exit();
?>
