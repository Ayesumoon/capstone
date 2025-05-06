<?php
require 'conn.php';

$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = "Admin";
$admin_role = "Admin";

if ($admin_id) {
    $query = "
        SELECT 
            CONCAT(first_name, ' ', last_name) AS full_name, 
            r.role_name 
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

// Fetch categories
$categories = [];
$categoryMap = [];

$categoryQuery = "SELECT category_id, category_name FROM categories";
$categoryResult = $conn->query($categoryQuery);
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['category_name'];
    $categoryMap[$row['category_id']] = $row['category_name'];
}

// Fetch products
$productQuery = "
    SELECT product_id, product_name, price_id, category_id, image_url
    FROM products
";

$productResult = $conn->query($productQuery);
$products = [];

while ($row = $productResult->fetch_assoc()) {
  $products[] = [
      'id' => (int)$row['product_id'],
      'name' => $row['product_name'],
      'price' => (float)$row['price_id'],
      'category' => $categoryMap[$row['category_id']] ?? 'Unknown',
      'image' => $row['image_url']  // Add image URL
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Point of Sale</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <style>
  .receipt-hidden {
    display: none;
  }

  @media print {
    body * {
      visibility: hidden;
    }

    #receipt, #receipt * {
      visibility: visible;
    }

    #receipt {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
    }
  }
</style>

</head>
<body class="bg-gray-100">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
      <div class="p-4">
        <div class="flex items-center space-x-4">
          <img src="logo.png" alt="Logo" width="50" height="50" class="rounded-full" />
          <h2 class="text-lg font-semibold">SevenDwarfs</h2>
        </div>
        <div class="mt-4 flex items-center space-x-4">
          <img src="newID.jpg" alt="Admin" width="40" height="40" class="rounded-full" />
          <div>
            <h3 class="text-sm font-semibold"><?= htmlspecialchars($admin_name); ?></h3>
            <p class="text-xs text-gray-500"><?= htmlspecialchars($admin_role); ?></p>
          </div>
        </div>
      </div>
      <nav class="mt-6">
        <ul>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-tachometer-alt mr-2"></i><a href="dashboard.php">Dashboard</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-box mr-2"></i><a href="products.php">Products</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-shopping-cart mr-2"></i><a href="orders.php">Orders</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-users mr-2"></i><a href="customers.php">Customers</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-warehouse mr-2"></i><a href="inventory.php">Inventory</a></li>
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-cash-register mr-2"></i><a href="POS.php">Point of Sale</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-user mr-2"></i><a href="users.php">Users</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-money-check-alt mr-2"></i><a href="payandtransac.php">Payment & Transactions</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-cog mr-2"></i><a href="storesettings.php">Store Settings</a></li>
          <li class="px-4 py-2 hover:bg-gray-200"><i class="fas fa-sign-out-alt mr-2"></i><a href="logout.php">Log out</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 bg-gradient-to-br from-gray-50 to-gray-200 p-8 font-sans">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold mb-8 text-center text-blue-800">🛒 Seven Dwarfs Boutique</h1>

        <!-- Category Filter -->
        <div class="mb-6 flex items-center gap-3">
          <label class="font-semibold text-lg text-gray-700">Filter by Category:</label>
          <select id="categoryFilter" onchange="filterProducts()" class="p-2 border border-gray-300 rounded-lg shadow-sm">
            <option value="all">All</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= htmlspecialchars($category); ?>"><?= htmlspecialchars($category); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Product List -->
        <div class="bg-white p-6 shadow-xl rounded-xl border mb-10">
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="product-list"></div>
        </div>

        <!-- Cart -->
        <div class="bg-pink-50 border-2 border-pink-500 p-6 shadow-xl rounded-xl">
          <h2 class="text-2xl font-semibold mb-4 text-blue-700">🧺 Cart</h2>
          <ul id="cart-items" class="space-y-4"></ul>
          <div class="mt-6 text-lg font-bold text-right text-green-700">
            Total: ₱<span id="total">0.00</span>
          </div>
          <button onclick="checkout()" class="mt-6 bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 w-full font-semibold text-lg shadow">
            🧾 Checkout
          </button>
        </div>

        <!-- Receipt -->
        <div id="receipt" class="mt-10 bg-white p-6 shadow-xl rounded-xl border max-w-xl mx-auto receipt-hidden">
          <h2 class="text-2xl font-bold mb-4 text-green-700 text-center">📄 Receipt</h2>
          <div id="receipt-content" class="font-mono text-sm text-gray-700 leading-relaxed"></div>
          <button onclick="window.print()" class="mt-6 bg-gray-800 text-white px-6 py-2 rounded hover:bg-black w-full">
            🖨️ Print Receipt
          </button>
        </div>
      </div>
    </main>
  </div>

  <!-- JavaScript -->
  <script>
    const products = <?php echo json_encode($products); ?>;
    let filteredProducts = [...products];
    const cart = [];

    function renderProducts() {
      const productList = document.getElementById('product-list');
      productList.innerHTML = '';
      filteredProducts.forEach(product => {
        productList.innerHTML += `
          <div class="border p-3 rounded-md shadow-sm text-center bg-gray-50 hover:shadow-md transition">
            <img src="${product.image}" alt="${product.name}" class="w-24 h-24 mx-auto object-cover mb-2 rounded">
            <p class="text-sm font-medium truncate">${product.name}</p>
            <p class="text-xs text-gray-500">${product.category}</p>
            <p class="text-sm font-semibold mt-1">₱${product.price}</p>
            <button onclick="addToCart(${product.id})" class="mt-2 bg-green-500 text-white text-sm px-3 py-1 rounded hover:bg-green-600">Add</button>
          </div>
        `;
      });
    }

    function filterProducts() {
      const selected = document.getElementById('categoryFilter').value;
      filteredProducts = selected === 'all' ? [...products] : products.filter(p => p.category === selected);
      renderProducts();
    }

    function addToCart(productId) {
      const product = products.find(p => p.id === productId);
      const item = cart.find(c => c.id === productId);
      if (item) {
        item.qty++;
      } else {
        cart.push({ ...product, qty: 1 });
      }
      renderCart();
    }

    function updateQty(productId, change) {
      const item = cart.find(c => c.id === productId);
      if (!item) return;
      item.qty += change;
      if (item.qty <= 0) {
        const index = cart.findIndex(c => c.id === productId);
        cart.splice(index, 1);
      }
      renderCart();
    }

    function removeFromCart(productId) {
      const index = cart.findIndex(c => c.id === productId);
      if (index !== -1) cart.splice(index, 1);
      renderCart();
    }

    function renderCart() {
      const cartList = document.getElementById('cart-items');
      const totalDisplay = document.getElementById('total');
      cartList.innerHTML = '';
      let total = 0;

      cart.forEach(item => {
        total += item.price * item.qty;
        cartList.innerHTML += `
          <li class="flex justify-between items-center">
            <div>
              <p>${item.name}</p>
              <p class="text-sm text-gray-500">₱${item.price} x ${item.qty}</p>
              <div class="space-x-1 mt-1">
                <button onclick="updateQty(${item.id}, 1)" class="px-2 bg-green-400 text-white rounded">+</button>
                <button onclick="updateQty(${item.id}, -1)" class="px-2 bg-yellow-400 text-white rounded">-</button>
                <button onclick="removeFromCart(${item.id})" class="px-2 bg-red-500 text-white rounded">x</button>
              </div>
            </div>
            <span>₱${(item.price * item.qty).toFixed(2)}</span>
          </li>
        `;
      });

      totalDisplay.textContent = total.toFixed(2);
    }

    function checkout() {
  if (cart.length === 0) {
    alert('Cart is empty!');
    return;
  }

  const receiptContent = document.getElementById('receipt-content');
  let total = 0;
  let receiptHTML = `<p>Date: ${new Date().toLocaleString()}</p><hr class="my-2">`;

  cart.forEach(item => {
    const line = `${item.name} x${item.qty} = ₱${(item.price * item.qty).toFixed(2)}`;
    receiptHTML += `<p>${line}</p>`;
    total += item.price * item.qty;
  });

  receiptHTML += `<hr class="my-2"><p class="font-bold">Total: ₱${total.toFixed(2)}</p>`;
  receiptContent.innerHTML = receiptHTML;

  // Show receipt and prepare for printing
  const receiptDiv = document.getElementById('receipt');
  receiptDiv.classList.remove('receipt-hidden');
  document.body.classList.add('print-only-receipt');

  setTimeout(() => {
    window.print();
    document.body.classList.remove('print-only-receipt');
    receiptDiv.classList.add('receipt-hidden'); // Optional: hide again after printing
  }, 100);

  cart.length = 0; // Clear cart
  renderCart();    // Update UI
}

    // Initial render
    renderProducts();
  </script>
</body>
</html>