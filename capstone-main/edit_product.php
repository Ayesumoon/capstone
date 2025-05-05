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

// Fetch product details with supplier price
$sql = "SELECT p.*, p.supplier_price FROM products p
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        WHERE p.product_id = ?";
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

// Fetch suppliers with ID and name
$supplier_query = "SELECT supplier_id, supplier_name FROM suppliers";
$supplier_result = $conn->query($supplier_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price_id = floatval($_POST['price']);
    $category_id = intval($_POST['category']);
    $stocks = intval($_POST['stocks']);
    $supplier_id = intval($_POST['supplier']); // Now using supplier ID
    $supplier_price = floatval($_POST['supplier_price']);

    // Image handling
    $image_url = $product['image_url']; // Keep existing image by default
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
    $update_sql = "UPDATE products SET product_name=?, description=?, price_id=?, category_id=?, stocks=?, image_url=?, supplier_id=?, supplier_price=? WHERE product_id=?";
    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param("ssdissdii", $product_name, $description, $price_id, $category_id, $stocks, $image_url, $supplier_id, $supplier_price, $product_id);
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

<!-- HTML part -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-pink-600 mb-4">Edit Product</h2>

        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            
            <div>
                <label class="block font-medium text-gray-700">Product Name:</label>
                <input type="text" name="product_name" required
                    value="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Description:</label>
                <textarea name="description" rows="4" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div>
    <label class="block font-medium text-gray-700">Supplier Price:</label>
    <input type="number" step="0.01" name="supplier_price" required
           value="<?php echo $product['supplier_price']; ?>"
           class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
</div>


<div>
    <label class="block font-medium text-gray-700">Price:</label>
    <input type="number" step="0.01" name="price" required
        value="<?php echo htmlspecialchars($product['price_id']); ?>"
        class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
</div>


            <div>
                <label class="block font-medium text-gray-700">Category:</label>
                <select name="category" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
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
            </div>

            <div>
                <label class="block font-medium text-gray-700">Stock Quantity:</label>
                <input type="number" name="stocks" required
                    value="<?php echo $product['stocks']; ?>"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Supplier:</label>
                <select name="supplier" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
                    <option value="">Select Supplier</option>
                    <?php
                    if ($supplier_result->num_rows > 0) {
                        while ($row = $supplier_result->fetch_assoc()) {
                            $selected = ($product['supplier_id'] == $row['supplier_id']) ? "selected" : "";
                            echo "<option value='" . $row['supplier_id'] . "' $selected>" . htmlspecialchars($row['supplier_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No suppliers available</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Product Image:</label>
                <input type="file" name="image" accept="image/*"
                    class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-pink-500 file:text-white hover:file:bg-pink-600">
                
                <?php if (!empty($product['image_url'])): ?>
                    <p class="mt-2 text-sm text-gray-600">Current Image:</p>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product Image" class="mt-1 w-24 h-24 rounded border">
                <?php endif; ?>
            </div>

            <div class="flex gap-4 pt-4">
                <input type="submit" value="Update Product"
                    class="bg-pink-500 text-white px-6 py-2 rounded hover:bg-pink-600 transition-all cursor-pointer">
                <a href="products.php"
                    class="text-pink-500 hover:underline self-center">Back to Products</a>
            </div>
        </form>
    </div>

</body>
</html>