<?php
require 'conn.php'; // Include your database connection file

// Enable JSON content type
header('Content-Type: application/json');

// Get the JSON data sent from the JavaScript
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if ($data) {
    $admin_id = $data['admin_id'] ?? null;
    $total_amount = $data['total_amount'] ?? 0;
    $cash_given = $data['cash_given'] ?? 0;
    $change = $data['change'] ?? 0;
    $items = $data['items'] ?? [];

    // Start a transaction to ensure atomicity (all updates succeed or none)
    $conn->begin_transaction();
    $order_successful = true;
    $error_message = '';

    // 1. Record the order details
    $order_query = "INSERT INTO orders (admin_id, created_at, total_amount, cash_given, change_amount) VALUES (?, NOW(), ?, ?, ?)";    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("iddd", $admin_id, $total_amount, $cash_given, $change);
    if ($order_stmt->execute()) {
        $order_id = $conn->insert_id; // Get the ID of the newly inserted order

        // 2. Record the individual order items and update product stock
        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price']; // Assuming you send the selling price

            // Record order item
            $order_item_query = "INSERT INTO order_items (order_id, order_id, product_id, quantity, selling_price) VALUES (?, ?, ?, ?, ?)";
            $order_item_stmt = $conn->prepare($order_item_query);
            $order_item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            if (!$order_item_stmt->execute()) {
                $order_successful = false;
                $error_message = "Error recording order item for product ID: " . $product_id . " - " . $order_item_stmt->error;
                break;
            }
            $order_item_stmt->close();

            // Update product stock (assuming you have a 'stock_quantity' column in your 'products' table)
            $stock_update_query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $stock_update_stmt = $conn->prepare($stock_update_query);
            $stock_update_stmt->bind_param("ii", $quantity, $product_id);
            if (!$stock_update_stmt->execute()) {
                $order_successful = false;
                $error_message = "Error updating stock for product ID: " . $product_id . " - " . $stock_update_stmt->error;
                break;
            }
            $stock_update_stmt->close();
        }
        $order_stmt->close();

        if ($order_successful) {
            $conn->commit();
            echo 'success';
        } else {
            $conn->rollback();
            echo json_encode(['error' => $error_message]); // Send JSON error
        }

    } else {
        $conn->rollback();
        echo json_encode(['error' => "Error recording order: " . $order_stmt->error]); // Send JSON error
    }

} else {
    echo json_encode(['error' => "No data received."]); // Send JSON error
}

$conn->close();
?>