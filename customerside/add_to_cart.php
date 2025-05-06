<?php
session_start();
require 'conn.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['price']) && isset($_POST['quantity'])) {
    $customer_id = $_SESSION['customer_id'];
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = (int)$_POST['quantity'];

    // Check if the item is already in the cart
    $stmt = $conn->prepare("SELECT cart_id, quantity FROM carts WHERE customer_id = ? AND product_id = ? AND cart_status = 'active'");
    $stmt->bind_param("ii", $customer_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_cart_item = $result->fetch_assoc();

    if ($existing_cart_item) {
        // Update quantity if item exists
        $new_quantity = $existing_cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE cart_id = ?");
        $stmt->bind_param("ii", $new_quantity, $existing_cart_item['cart_id']);
        if (!$stmt->execute()) {
            error_log("Error updating cart: " . $stmt->error);
            // Redirect to cart on error
            header('Location: cart.php');
            exit;
        }
        // Redirect to cart on success
        header('Location: cart.php');
        exit;

    } else {
        // Insert new item into cart
        $stmt = $conn->prepare("INSERT INTO carts (customer_id, product_id, quantity, created_at, cart_status) VALUES (?, ?, ?, NOW(), 'active')");
        $stmt->bind_param("iii", $customer_id, $product_id, $quantity);
        if (!$stmt->execute()) {
            error_log("Error adding to cart: " . $stmt->error);
            // Redirect to cart on error
            header('Location: cart.php');
            exit;
        }
        // Redirect to cart on success
        header('Location: cart.php');
        exit;
    }

} else {
    // Invalid request
    // Redirect to cart on invalid request
    header('Location: cart.php');
    exit;
}
?>