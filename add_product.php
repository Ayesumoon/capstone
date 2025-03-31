<?php
require 'conn.php'; // Include database connection

// Fetch categories from the database safely
$category_query = "SELECT category_id, category_name FROM categories";
$category_result = $conn->query($category_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if fields exist before accessing
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : "";
    $description = isset($_POST['description']) ? trim($_POST['description']) : "";
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $category_id = isset($_POST['category']) ? intval($_POST['category']) : 0;
    $stocks = isset($_POST['stocks']) ? intval($_POST['stocks']) : 0;

    // Validate required fields
    if (empty($product_name) || empty($description) || $price <= 0 || $category_id <= 0 || $stocks < 0) {
        echo "<script>alert('All fields are required and must be valid!');</script>";
    } else {
        // Handle image upload
        $image_url = "";
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $target_dir = "uploads/"; // Folder where images are stored
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Create directory if not exists
            }

            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type (allow only JPG, JPEG, PNG, GIF)
            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowed_types)) {
                echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            } else {
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                $image_url = $target_file;
            }
        }

        // Insert into database
        $sql = "INSERT INTO products (product_name, description, price_id, category_id, stocks, image_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssdiss", $product_name, $description, $price, $category_id, $stocks, $image_url);
            if ($stmt->execute()) {
                echo "<script>alert('Product added successfully!'); window.location.href='products.php';</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add_product.css">
    <title>Add Product</title>
</head>
<body>
    <div class="main-content">
        <h2>Add New Product</h2>
        <br>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <label>Product Name:</label>
            <input type="text" name="product_name" required>
            <br>

            <label>Description:</label>
            <textarea name="description" required></textarea>
            <br>

            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>
            <br>

            <label>Category:</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <?php
                if ($category_result->num_rows > 0) {
                    while ($row = $category_result->fetch_assoc()) {
                        echo "<option value='" . $row['category_id'] . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories available</option>";
                }
                ?>
            </select>
            <br>

            <label>Stocks:</label>
            <input type="number" name="stocks" required>
            <br>

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <br>

            <input type="submit" value="Add Product">
        </form>
        <br>
        <a href="products.php">Back to Products</a>
    </div>
</body>
</html>
