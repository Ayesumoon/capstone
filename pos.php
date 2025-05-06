<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
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
          <li class="px-4 py-2 bg-pink-100 text-pink-600"><i class="fas fa-cash-register mr-2"></i><a href="POS">Point of Sale</a></li>
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
            <option value="clothes">Clothes</option>
            <option value="accessory">Accessory</option>
          </select>
        </div>

        <!-- Product and Cart Layout -->
        <div class="grid md:grid-cols-2 gap-8">
          <!-- Product List -->
          <div class="bg-white p-6 shadow-xl rounded-xl border" id="product-list"></div>

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
        </div>

        <!-- Receipt -->
        <div id="receipt" class="mt-10 bg-white p-6 shadow-xl rounded-xl border hidden max-w-xl mx-auto">
          <h2 class="text-2xl font-bold mb-4 text-green-700 text-center">📄 Receipt</h2>
          <div id="receipt-content" class="font-mono text-sm text-gray-700 leading-relaxed"></div>
          <button onclick="window.print()" class="mt-6 bg-gray-800 text-white px-6 py-2 rounded hover:bg-black w-full">
            🖨️ Print Receipt
          </button>
        </div>
      </div>
    </main>
  </div>

  <script>
    const products = [
      { id: 1, name: 'Blouse', category: 'clothes', price: 299 },
      { id: 2, name: 'Dress', category: 'clothes', price: 499 },
      { id: 3, name: 'Shoes', category: 'accessory', price: 899 },
      { id: 4, name: 'Perfume', category: 'accessory', price: 399 },
      { id: 5, name: 'Skirt', category: 'clothes', price: 350 },
    ];

    let filteredProducts = [...products];
    const cart = [];

    function renderProducts() {
      const productList = document.getElementById('product-list');
      productList.innerHTML = '';
      filteredProducts.forEach(product => {
        productList.innerHTML += `
          <div class="border-b pb-2 mb-3">
            <p class="font-medium">${product.name}</p>
            <p class="text-sm text-gray-500">${product.category}</p>
            <p>₱${product.price}</p>
            <button onclick="addToCart(${product.id})" class="mt-1 bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Add to Cart</button>
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
      let receiptHTML = <p>Date: ${new Date().toLocaleString()}</p><hr class="my-2">;

      cart.forEach(item => {
        const line = ${item.name} x${item.qty} = ₱${(item.price * item.qty).toFixed(2)};
        receiptHTML += <p>${line}</p>;
        total += item.price * item.qty;
      });

      receiptHTML += <hr class="my-2"><p class="font-bold">Total: ₱${total.toFixed(2)}</p>;
      receiptContent.innerHTML = receiptHTML;

      document.getElementById('receipt').classList.remove('hidden');
      cart.length = 0;
      renderCart();
    }

    renderProducts();
  </script>
</body>
</html>
cdn.tailwindcss.com