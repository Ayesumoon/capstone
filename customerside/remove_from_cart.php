<?php
session_start();
require 'conn.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $stmt = $conn->prepare("UPDATE carts SET cart_status = 'removed' WHERE cart_id = ? AND customer_id = ?"); // Update cart_status instead of deleting
    $stmt->bind_param("ii", $cart_id, $_SESSION['customer_id']); // Ensure only the user can remove their own items

    if (!$stmt->execute()) {
        error_log("Error removing from cart: " . $stmt->error);
        // Redirect to cart on error
        header('Location: cart.php');
        exit;
    }

    // Redirect to cart on success
    header('Location: cart.php');
    exit;

} else {
    // Invalid request
    // Redirect to cart on invalid request
    header('Location: cart.php');
    exit;
}
?>