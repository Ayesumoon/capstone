<?php
require 'conn.php';

$preselectedCategoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

$category_query = "SELECT category_id, category_name FROM categories";
$category_result = $conn->query($category_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name'] ?? "");
    $price = floatval($_POST['price'] ?? 0);
    $supplier_price = floatval($_POST['supplier_price'] ?? 0);
    $category_id = intval($_POST['category'] ?? 0);
    $stocks = intval($_POST['stocks'] ?? 0);
    $supplier_name = trim($_POST['supplier'] ?? "");
    $sizes = $_POST['sizes'] ?? [];
    $colors = $_POST['colors'] ?? [];

    // Build description from selected sizes and colors
    $sizeText = implode(", ", $sizes);
    $colorText = implode(", ", $colors);
    $description = "Sizes: $sizeText | Colors: $colorText";

    if (empty($product_name) || $price <= 0 || $supplier_price <= 0 || $category_id <= 0 || $stocks < 0 || empty($supplier_name) || empty($sizes) || empty($colors)) {
        echo "<script>alert('All fields are required and must be valid!');</script>";
    } else {
        // Get or insert supplier
        $supplier_id = null;
        $stmt = $conn->prepare("SELECT supplier_id FROM suppliers WHERE supplier_name = ?");
        $stmt->bind_param("s", $supplier_name);
        $stmt->execute();
        $stmt->bind_result($supplier_id);
        $stmt->fetch();
        $stmt->close();

        if (!$supplier_id) {
            $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name) VALUES (?)");
            $stmt->bind_param("s", $supplier_name);
            $stmt->execute();
            $supplier_id = $stmt->insert_id;
            $stmt->close();
        }

        // Upload images
        $image_urls = [];
        if (!empty($_FILES['images']['name'][0])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            foreach ($_FILES['images']['name'] as $index => $filename) {
                $tmp_name = $_FILES['images']['tmp_name'][$index];
                $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed_types = ["jpg", "jpeg", "png", "gif"];

                if (in_array($imageFileType, $allowed_types)) {
                    $unique_name = uniqid() . "_" . basename($filename);
                    $target_file = $target_dir . $unique_name;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_urls[] = $target_file;
                    }
                }
            }
        }
        $image_urls_json = json_encode($image_urls);

        // Revenue
        $revenue = $price - $supplier_price;

        // Insert product
        $sql = "INSERT INTO products (product_name, description, price_id, supplier_price, revenue, category_id, stocks, image_url, supplier_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssdississ", $product_name, $description, $price, $supplier_price, $revenue, $category_id, $stocks, $image_urls_json, $supplier_id);
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

<!-- HTML STARTS HERE -->
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
        <h2 class="text-2xl font-bold text-pink-600 mb-6">Add New Product</h2>

        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-8">

            <!-- Product Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Product Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product Code</label>
                        <input type="text" name="product_name" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stocks</label>
                        <input type="number" name="stocks" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supplier Price</label>
                        <input type="number" step="0.01" name="supplier_price" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Selling Price</label>
                        <input type="number" step="0.01" name="price" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                </div>
            </div>

            <!-- Category and Supplier -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Category & Supplier</h3>

                <?php
                $category_name = '';
                if ($preselectedCategoryId) {
                    $stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
                    $stmt->bind_param("i", $preselectedCategoryId);
                    $stmt->execute();
                    $stmt->bind_result($category_name);
                    $stmt->fetch();
                    $stmt->close();
                }
                ?>
                <input type="hidden" name="category" value="<?php echo $preselectedCategoryId; ?>">
                <p class="p-2 bg-gray-50 border border-gray-200 rounded text-gray-700">
                    <strong>Category:</strong> <?php echo htmlspecialchars($category_name); ?>
                </p>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <input type="text" name="supplier" required
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
            </div>

            <!-- Media -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Product Images</h3>
                <input type="file" name="images[]" accept="image/*" multiple
                    class="block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-pink-500 file:text-white hover:file:bg-pink-600">
            </div>

            <!-- Sizes -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Available Sizes</h3>
                <div class="flex flex-wrap gap-3">
                    <?php
                    $sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'Free Size'];
                    foreach ($sizeOptions as $size) {
                        echo '
                        <label class="cursor-pointer">
                            <input type="checkbox" name="sizes[]" value="' . $size . '" class="hidden peer">
                            <div class="px-4 py-2 border rounded-md text-sm font-semibold 
                                        peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-600 transition-all">
                                ' . $size . '
                            </div>
                        </label>';
                    }
                    ?>
                </div>
            </div>

            <!-- Colors -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Available Colors</h3>
                <div class="flex flex-wrap gap-4">
                    <?php
                    $colorOptions = ['Red', 'Black', 'White', 'Pink', 'Blue', 'Green', 'Yellow', 'Purple'];
                    foreach ($colorOptions as $color) {
                        $hex = strtolower($color);
                        echo '
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="colors[]" value="' . $color . '" class="hidden peer">
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300 peer-checked:border-blue-500"
                                 style="background-color:' . $hex . ';"></div>
                        </label>';
                    }
                    ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 flex gap-4">
                <input type="submit" value="Add Product"
                    class="bg-pink-500 text-white px-6 py-2 rounded hover:bg-pink-600 transition-all cursor-pointer">
                <a href="products.php" class="text-pink-500 hover:underline self-center">Back to Products</a>
            </div>
        </form>
    </div>
</body>
</html>

