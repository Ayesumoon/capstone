<?php
session_start();
$cartCount = 0;

if (isset($_SESSION['cart'])) {
  $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

echo json_encode(['cartCount' => $cartCount]);
?>
