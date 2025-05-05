<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Initialize the cart if not yet set
    if (!isset($_SESSION['carts'])) {
        $_SESSION['carts'] = [];
    }

    // Check if product already exists in cart
    if (isset($_SESSION['carts'][$productId])) {
        // Update quantity if the product exists in the cart
        $_SESSION['carts'][$productId]['quantity'] += $quantity;
    } else {
        // Add new product to cart
        $_SESSION['carts'][$productId] = [
            'product_name' => $productName,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    header("Location: cart.php");
    exit();
}
?>
