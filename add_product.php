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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-pink-600 mb-4">Add New Product</h2>

        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-medium text-gray-700">Product Name:</label>
                <input type="text" name="product_name" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Description:</label>
                <textarea name="description" required rows="4"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400"></textarea>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Price:</label>
                <input type="number" step="0.01" name="price" required
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
                            echo "<option value='" . $row['category_id'] . "'>" . htmlspecialchars($row['category_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No categories available</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Stocks:</label>
                <input type="number" name="stocks" required
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-pink-400">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Product Image:</label>
                <input type="file" name="image" accept="image/*"
                    class="mt-1 block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-pink-500 file:text-white hover:file:bg-pink-600">
            </div>

            <div class="pt-4 flex gap-4">
                <input type="submit" value="Add Product"
                    class="bg-pink-500 text-white px-6 py-2 rounded hover:bg-pink-600 transition-all cursor-pointer">
                <a href="products.php" class="text-pink-500 hover:underline">Back to Products</a>
            </div>
        </form>
    </div>

</body>
</html>
