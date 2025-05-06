
<?php
session_start();
require 'conn.php';

$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = "Admin";
$admin_role = "Admin";

// Fetch admin details
if ($admin_id) {
    $query = "
        SELECT CONCAT(first_name, ' ', last_name) AS full_name, r.role_name
        FROM adminusers a
        LEFT JOIN roles r ON a.role_id = r.role_id
        WHERE a.admin_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $admin_name = $row['full_name'];
        $admin_role = $row['role_name'] ?? 'Admin';
    }
}

// Fetch products
$productQuery = "SELECT product_id, product_name, price_id, description, category_id, image_url FROM products";
$productResult = $conn->query($productQuery);
if (!$productResult) {
    echo "Error fetching products: " . $conn->error;
    exit();
}
$products = [];
while ($row = $productResult->fetch_assoc()) {
    $products[] = $row;
}

// Fetch categories
$categoryQuery = "SELECT category_id, category_name FROM categories";
$categoryResult = $conn->query($categoryQuery);
if (!$categoryResult) {
    echo "Error fetching categories: " . $conn->error;
    exit();
}
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>POS - Seven Dwarfs Boutique</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-md">
            <div class="p-4">
                <div class="flex items-center space-x-4">
                    <img src="logo.png" alt="Logo" width="50" height="50" class="rounded-full">
                    <h2 class="text-lg font-semibold">SevenDwarfs</h2>
                </div>
                <div class="mt-4 flex items-center space-x-4">
                    <img src="newID.jpg" alt="Admin" width="40" height="40" class="rounded-full">
                    <div>
                        <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
                    </div>
                </div>
            </div>
            <nav class="mt-6">
                <ul>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-tachometer-alt mr-2"></i><a href="dashboard.php">Dashboard</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="products.php">Products</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
                    <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-cash-register mr-2"></i><a href="pos.php">Point of Sale</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
                    <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-4xl font-bold mb-8 text-center text-blue-800">🛒 Seven Dwarfs Boutique</h1>

                <div class="mb-6 flex items-center gap-3">
                    <label class="font-semibold text-lg text-gray-700">Filter by Category:</label>
                    <select id="categoryFilter" onchange="filterProducts()" class="p-2 border border-gray-300 rounded-lg shadow-sm">
                        <option value="all">All</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_id']; ?>"><?= htmlspecialchars($category['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="product-list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
                </div>

                <div class="bg-pink-50 border-2 border-pink-500 p-6 shadow-xl rounded-xl">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-700">🧺 Cart</h2>
                    <ul id="cart-items" class="space-y-4"></ul>
                    <div class="mt-6 text-lg font-bold text-right text-green-700">
                        Total: ₱<span id="total">0.00</span>
                    </div>
                    <button onclick="openPaymentModal()" class="mt-6 bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 w-full font-semibold text-lg shadow">
                        🧾 Checkout
                    </button>
                </div>
            </div>
        </main>
  </div>

  <script>
    //  Data should come from PHP (or an external source), but I'll keep the example data for now.
    const products = <?= json_encode($products ?? []); ?>; // Use ?? to avoid errors if $products is not set
    let filteredProducts = [...products];
    const cart = [];

    //  Utility function to escape HTML to prevent XSS vulnerabilities
    function escapeHTML(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
    function renderProducts() {
    const productList = document.getElementById('product-list');
    if (!productList) {
        console.error("Product list element not found!");
        return;
    }
    productList.innerHTML = '';

    filteredProducts.forEach(product => {
        const imageUrl = product.image_url || 'default-image.jpg';
        const productName = escapeHTML(product.product_name);
        const description = escapeHTML(product.description);
        const price = parseFloat(product.price_id);

        productList.innerHTML += `
            <div class="bg-white p-4 shadow-xl rounded-lg border h-full flex flex-col">
                <img src="${imageUrl}" onerror="this.src='default-image.jpg'" alt="${productName}" class="w-full h-32 object-contain mb-2 rounded-md">
                <p class="font-medium text-sm truncate">${productName}</p>
                <p class="text-xs text-gray-500 truncate">${description}</p>
                <p class="font-semibold text-blue-500 text-sm">₱${price.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>
                <button
                    onclick="addToCart('${product.product_id}')"
                    class="mt-auto bg-green-400 text-white px-2 py-1 rounded hover:bg-green-500 text-xs"
                >
                    Add
                </button>
            </div>
        `;
    });
}

    function filterProducts() {
        const selectedCategory = document.getElementById('categoryFilter').value;
        filteredProducts = selectedCategory === 'all' 
            ? [...products] 
            : products.filter(p => p.category_id == selectedCategory); //  Use == for comparison
        renderProducts();
    }

    function addToCart(productId) {
        const product = products.find(p => p.product_id === productId);
        if (!product) {
            console.error("Product not found:", productId);
            return;
        }

        const existingCartItem = cart.find(item => item.product_id === productId);
        if (existingCartItem) {
            existingCartItem.qty++;
        } else {
            cart.push({ 
                product_id: product.product_id, //  Store product_id consistently
                product_name: product.product_name,
                price_id: parseFloat(product.price_id), 
                qty: 1 
            });
        }
        renderCart();
    }

    function updateQty(productId, change) {
        const cartItem = cart.find(item => item.product_id === productId);
        if (!cartItem) return;

        cartItem.qty += change;

        if (cartItem.qty <= 0) {
            removeFromCart(productId);
        } else {
            renderCart();
        }
    }

    function removeFromCart(productId) {
        const index = cart.findIndex(item => item.product_id === productId);
        if (index !== -1) {
            cart.splice(index, 1);
            renderCart();
        }
    }

    function renderCart() {
        const cartList = document.getElementById('cart-items');
        const totalDisplay = document.getElementById('total');
        if (!cartList || !totalDisplay) {
            console.error("Cart list or total display element not found!");
            return;
        }

        cartList.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            const subtotal = item.price_id * item.qty;
            total += subtotal;

            const cartItemElement = document.createElement('li');
            cartItemElement.classList.add('flex', 'justify-between', 'items-center', 'py-2', 'border-b', 'border-gray-200');

            cartItemElement.innerHTML = `
                <div>
                    <p class="font-semibold">${escapeHTML(item.product_name)}</p>
                    <p class="text-sm text-gray-600">₱${item.price_id.toLocaleString('en-PH', { minimumFractionDigits: 2 })} x ${item.qty}</p>
                    <div class="space-x-2 mt-2">
                        <button onclick="updateQty('${item.product_id}', 1)" class="px-2 py-1 bg-green-400 text-white rounded">+</button>
                        <button onclick="updateQty('${item.product_id}', -1)" class="px-2 py-1 bg-yellow-400 text-white rounded">-</button>
                        <button onclick="removeFromCart('${item.product_id}')" class="px-2 py-1 bg-red-500 text-white rounded">x</button>
                    </div>
                </div>
                <span class="font-bold">₱${subtotal.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
            `;

            cartList.appendChild(cartItemElement);
        });

        totalDisplay.textContent = total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        modalTotalDisplay.textContent = total.toLocaleString('en-PH', { minimumFractionDigits: 2 }); //  Update modal total
    }

    function openPaymentModal() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        paymentModal.classList.remove('hidden');
        cashGivenInput.value = '';
        changeDueDisplay.textContent = '0.00';
        cashGivenInput.focus();
    }

    function closePaymentModal() {
        paymentModal.classList.add('hidden');
    }

    function processPayment() {
        const totalAmount = parseFloat(totalDisplay.textContent.replace(/,/g, ''));  // Remove commas
        const cashGiven = parseFloat(cashGivenInput.value);

        if (isNaN(cashGiven) || cashGiven < totalAmount) {
            alert('Insufficient cash!');
            return;
        }

        const change = cashGiven - totalAmount;
        changeDueDisplay.textContent = change.toLocaleString('en-PH', { minimumFractionDigits: 2 });

        generateReceipt();
        closePaymentModal();
        receiptModal.classList.remove('hidden');

        const orderDetails = cart.map(item => ({
            product_id: item.product_id,
            quantity: item.qty,
            price: item.price_id
        }));

        fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                admin_id: <?= json_encode($admin_id ?? null); ?>, //  Send admin ID
                total_amount: totalAmount,
                cash_given: cashGiven,
                change: change,
                items: orderDetails
            })
        })
        .then(response => response.text())
        .then(data => {
            console.log('Order processing response:', data);
            if (data === 'success') {
                alert('Order processed and stock updated successfully!');
                cart.length = 0;
                renderCart();
            } else {
                alert('Error processing order: ' + data);
            }
        })
        .catch(error => {
            console.error('Error sending order data:', error);
            alert('An error occurred while processing the order.');
        });
    }

    function generateReceipt() {
        receiptContent.innerHTML = `
            <h4 class="text-lg font-semibold mb-2 text-center">Seven Dwarfs Boutique</h4>
            <p class="text-sm text-center mb-2">Trece Martires, Cavite</p>
            <p>Admin: ${adminName}</p>
            <p>Date: ${new Date().toLocaleString()}</p>
            <hr class="my-2">
        `;

        let total = 0;
        cart.forEach(item => {
            const subtotal = item.price_id * item.qty;
            const line = `${escapeHTML(item.product_name)} x ${item.qty} @ ₱${item.price_id.toLocaleString('en-PH', { minimumFractionDigits: 2 })} = ₱${subtotal.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
            receiptContent.innerHTML += `<p class="text-sm">${line}</p>`;
            total += subtotal;
        });

        receiptContent.innerHTML += `
            <hr class="my-2">
            <p class="font-bold text-right">Total: ₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>
            <p class="font-semibold text-right">Cash: ₱${parseFloat(cashGivenInput.value).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>
            <p class="font-semibold text-right">Change: ₱${changeDueDisplay.textContent}</p>
            <hr class="my-2">
            <p class="text-sm text-center">Thank you for your purchase!</p>
        `;

        window.print(); //  Trigger print dialog
    }

    renderProducts();
    renderCart();

</script>
</body>
</html>