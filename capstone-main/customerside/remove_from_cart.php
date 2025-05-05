<?php
session_start();
require 'conn.php'; // your DB connection

if (!isset($_POST['product_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'No product ID provided']);
  exit();
}

$product_id = intval($_POST['product_id']);
$customer_id = $_SESSION['customer_id'] ?? null;

if (!$customer_id) {
  echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
  exit();
}

// Remove item from cart table
$stmt = $conn->prepare("DELETE FROM cart WHERE customer_id = ? AND product_id = ?");
$stmt->bind_param("ii", $customer_id, $product_id);

if ($stmt->execute()) {
  // Get new cart count
  $count_stmt = $conn->prepare("SELECT COUNT(*) AS count FROM cart WHERE customer_id = ?");
  $count_stmt->bind_param("i", $customer_id);
  $count_stmt->execute();
  $count_result = $count_stmt->get_result()->fetch_assoc();
  $cartCount = $count_result['count'];

  echo json_encode(['status' => 'success', 'cartCount' => $cartCount]);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
}
?>
