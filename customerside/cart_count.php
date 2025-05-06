<?php
session_start();

$cartCount = 0;

if (isset($_SESSION['cart'])) {
    // Sum the quantity of all items in the cart
    $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

header('Content-Type: application/json');
echo json_encode(['cartCount' => $cartCount]);
?>
