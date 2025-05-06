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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Point Of Sale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
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

                <div id="product-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"></div>

                <div class="bg-pink-50 border-2 border-pink-500 p-6 shadow-xl rounded-xl mt-12">
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

    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl w-96">
            <h3 class="text-xl font-semibold mb-4">💰 Payment</h3>
            <p class="mb-2">Total Amount: ₱<span id="modal-total">0.00</span></p>
            <div class="mb-4">
                <label for="cashGiven" class="block text-gray-700 text-sm font-bold mb-2">Cash Given:</label>
                <input type="number" id="cashGiven" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter cash amount">
            </div>
            <p class="mb-2 font-semibold">Change: ₱<span id="changeDue">0.00</span></p>
            <div class="flex justify-end gap-2">
                <button onclick="closePaymentModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button onclick="processPayment()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Process Payment & Print</button>
            </div>
        </div>
    </div>

    <div id="receipt" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-xl w-96">
            <h3 class="text-xl font-semibold mb-4">🧾 Receipt</h3>
            <div id="receipt-content" class="space-y-2 text-sm text-gray-700"></div>
            <button onclick="document.getElementById('receipt').classList.add('hidden')" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Close
            </button>
        </div>
    </div>


    <script>
        const adminName = <?= json_encode($admin_name); ?>;
        const products = <?= json_encode($products); ?>;
        console.log("Products array:", products);
        let filteredProducts = [...products];
        const cart = [];
        const totalDisplay = document.getElementById('total');
        const modalTotalDisplay = document.getElementById('modal-total');
        const cashGivenInput = document.getElementById('cashGiven');
        const changeDueDisplay = document.getElementById('changeDue');
        const paymentModal = document.getElementById('paymentModal');
        const receiptModal = document.getElementById('receipt');
        const receiptContent = document.getElementById('receipt-content');

        function escapeHTML(str) {
            return String(str).replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        function renderProducts() {
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';

            filteredProducts.forEach(product => {
                const imageUrl = product.image_url || 'default-image.jpg';
                const productName = escapeHTML(product.product_name || 'No Name');
                const description = escapeHTML(product.description || 'No Description');
                const price = parseFloat(product.price_id || 0);

                productList.innerHTML += `
                    <div class="bg-white p-4 shadow-xl rounded-lg border h-full">
                        <img src="${imageUrl}" onerror="this.src='default-image.jpg'" alt="${productName}" class="w-full h-48 object-contain mb-3 rounded-lg" />
                        <p class="font-medium text-lg truncate">${productName}</p>
                        <p class="text-sm text-gray-500 truncate">${description}</p>
                        <p class="font-bold text-blue-500">₱${price.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</p>
                        <button onclick="addToCart('${product.product_id}')" class="mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 w-full">
                            Add to Cart
                        </button>
                    </div>
                `;
            });
        }

        function filterProducts() {
            const selectedCategory = document.getElementById('categoryFilter').value;
            filteredProducts = selectedCategory === 'all'
                ? [...products]
                : products.filter(p => p.category_id == selectedCategory);
            renderProducts();
        }

        function addToCart(productId) {
            console.log("addToCart called with productId:", productId);
            const product = products.find(p => p.product_id === productId);

            if (!product) {
                alert("Product not found");
                return;
            }

            const item = cart.find(c => c.product_id === productId);

            if (item) {
                item.qty++;
            } else {
                cart.push({ product_id: product.product_id, product_name: product.product_name, price_id: parseFloat(product.price_id), qty: 1 });
            }

            renderCart();
        }

        function renderCart() {
            const cartList = document.getElementById('cart-items');
            const totalDisplay = document.getElementById('total'); // Get totalDisplay here
            cartList.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.price_id * item.qty;
                total += subtotal;

                cartList.innerHTML += `
                    <li class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold">${escapeHTML(item.product_name)}</p>
                            <p class="text-sm text-gray-500">₱${item.price_id.toLocaleString('en-PH', { minimumFractionDigits: 2 })} x ${item.qty}</p>
                            <div class="space-x-1 mt-1">
                                <button onclick="updateQty('${item.product_id}', 1)" class="px-2 bg-green-400 text-white rounded">+</button>
                                <button onclick="updateQty('${item.product_id}', -1)" class="px-2 bg-yellow-400 text-white rounded">-</button>
                                <button onclick="removeFromCart('${item.product_id}')" class="px-2 bg-red-500 text-white rounded">x</button>
                            </div>
                        </div>
                        <span class="font-bold">₱${subtotal.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</span>
                    </li>
                `;
            });

            totalDisplay.textContent = total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
            modalTotalDisplay.textContent = total.toLocaleString('en-PH', { minimumFractionDigits: 2 }); // Update modal total
        }

        function updateQty(productId, change) {
            const item = cart.find(c => c.product_id === productId);
            if (!item) return;
            item.qty += change;
            if (item.qty <= 0) {
                removeFromCart(productId);
            } else {
                renderCart();
            }
        }

        function removeFromCart(productId) {
            const index = cart.findIndex(c => c.product_id === productId);
            if (index !== -1) {
                cart.splice(index, 1);
                renderCart();
            }
        }

        function openPaymentModal() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }
            paymentModal.classList.remove('hidden');
            cashGivenInput.value = ''; // Reset cash given input
            changeDueDisplay.textContent = '0.00'; // Reset change due
            cashGivenInput.focus();
        }

        function closePaymentModal() {
            paymentModal.classList.add('hidden');
        }

        function processPayment() {
    const totalAmount = parseFloat(totalDisplay.textContent);
    const cashGiven = parseFloat(cashGivenInput.value);

    if (isNaN(cashGiven) || cashGiven < totalAmount) {
        alert('Insufficient cash!');
        return;
    }

    const change = cashGiven - totalAmount;
    changeDueDisplay.textContent = change.toLocaleString('en-PH', { minimumFractionDigits: 2 });

    // Generate and show receipt
    generateReceipt();
    closePaymentModal();
    receiptModal.classList.remove('hidden');

    // Prepare order details to send to PHP
    const orderDetails = cart.map(item => ({
        product_id: item.product_id,
        quantity: item.qty,
        price: item.price_id // You might want to store the actual selling price here
    }));

    // Send order details to PHP script using fetch API
    fetch('process_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            admin_id: <?= json_encode($admin_id); ?>, // Send admin ID
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
            cart.length = 0; // Clear the cart after successful order
            renderCart(); // Re-render the empty cart
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
    const receiptContent = document.getElementById('receipt-content');

    if (!receiptContent) {
        console.error("Error: 'receipt-content' element not found in the DOM.");
        return;
    }

    let adminName = document.getElementById('admin-name-display')?.textContent || "Admin";

    receiptContent.innerHTML = `
        <div style="font-family: sans-serif; font-size: 12px; width: 250px; margin: 0 auto; text-align: left;">
            <h4 style="font-size: 16px; font-weight: bold; margin-bottom: 2px; text-align: center;">Seven Dwarfs Boutique</h4>
            <p style="font-size: 10px; text-align: center; margin-bottom: 10px;">Bayambang, Pangasinan</p>
            <p style="font-size: 10px; margin-bottom: 2px;">Admin: ${adminName}</p>
            <p style="font-size: 10px; margin-bottom: 10px;">Date: ${new Date().toLocaleString()}</p>
            <hr style="border-top: 1px dashed #000; margin-bottom: 10px;">
    `;

    let total = 0;
    const cart = getCartItems();

    cart.forEach(item => {
        const subtotal = item.price_id * item.qty;
        const formattedPrice = parseFloat(item.price_id).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        const formattedSubtotal = subtotal.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        const line = `<div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 10px;">
                        <span style="flex-grow: 1;">${escapeHTML(item.product_name)} x ${item.qty}</span>
                        <span style="text-align: right;">₱${formattedSubtotal}</span>
                    </div>`;
        receiptContent.innerHTML += line;
        total += subtotal;
    });

    const cashGivenInput = document.getElementById('cash-given');
    const changeDueDisplay = document.getElementById('change-due');

    const formattedTotal = total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const cashGivenValue = parseFloat(cashGivenInput?.value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    const changeDueValue = changeDueDisplay?.textContent || '0.00';

    receiptContent.innerHTML += `
            <hr style="border-top: 1px dashed #000; margin-top: 10px; margin-bottom: 5px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 10px; font-weight: bold;">
                <span>Total:</span>
                <span>₱${formattedTotal}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 10px; font-weight: bold;">
                <span>Cash:</span>
                <span>₱${cashGivenValue}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 10px; font-weight: bold;">
                <span>Change:</span>
                <span>₱${changeDueValue}</span>
            </div>
            <hr style="border-top: 1px dashed #000; margin-bottom: 10px;">
            <p style="font-size: 10px; text-align: center;">Thank you for your purchase!</p>
        </div>
    `;

    window.print();
}

function escapeHTML(str) {
    return str.replace(/[&<>"']/g, function(match) {
        switch (match) {
            case '&': return '&amp;';
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '"': return '&quot;';
            case "'": return '&#39;';
            default: return match;
        }
    });
}

function getCartItems() {
    const cartData = localStorage.getItem('pos_cart');
    return cartData ? JSON.parse(cartData) : [];
}
        renderProducts();
    </script>
</body>
</html>