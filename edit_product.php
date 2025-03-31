<?php
require 'conn.php'; // Database connection
session_start();

// Check if product_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid product ID!'); window.location.href='products.php';</script>";
    exit;
}

$product_id = intval($_GET['id']);

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Product not found!'); window.location.href='products.php';</script>";
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Fetch categories
$category_query = "SELECT category_id, category_name FROM categories";
$category_result = $conn->query($category_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category']);
    $stocks = intval($_POST['stocks']);
    
    // Image handling
    $image_url = $product['image_url']; // Keep the existing image by default
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowed_types)) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $image_url = $target_file; // Update image path
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        }
    }

    // Update product details
    $update_sql = "UPDATE products SET product_name=?, description=?, price_id=?, category_id=?, stocks=?, image_url=? WHERE product_id=?";
    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param("ssdissi", $product_name, $description, $price, $category_id, $stocks, $image_url, $product_id);
        if ($stmt->execute()) {
            echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
        } else {
            echo "Error updating product: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_product.css">
    <title>Edit Product</title>
</head>
<body>
    <div class="main-content">
        <h2>Edit Product</h2>
        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            <br>

            <label>Description:</label>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            <br>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" value="<?php echo $product['price_id']; ?>" required>
            <br>

            <label>Category:</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <?php
                if ($category_result->num_rows > 0) {
                    while ($row = $category_result->fetch_assoc()) {
                        $selected = ($product['category_id'] == $row['category_id']) ? "selected" : "";
                        echo "<option value='" . $row['category_id'] . "' $selected>" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories available</option>";
                }
                ?>
            </select>
            <br>

            <label>Stock Quantity:</label>
            <input type="number" name="stocks" value="<?php echo $product['stocks']; ?>" required>
            <br>

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <br>
            <?php if (!empty($product['image_url'])): ?>
                <p>Current Image:</p>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" style="width:100px; height:100px;">
            <?php endif; ?>
            <br>

            <input type="submit" value="Update Product">
        </form>
        <br>
        <a href="products.php">Back to Products</a>
    </div>
</body>
</html>
