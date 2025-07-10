<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Categorie List</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 font-poppins text-sm transition-all duration-300">

<div class="flex h-screen">

  <!-- Sidebar -->
  <div class="w-64 bg-white shadow-md min-h-screen" x-data="{ userMenu: false, productMenu: true }">
    <div class="p-4">
      <div class="flex items-center space-x-4">
        <img src="logo2.png" alt="Logo" class="rounded-full w-12 h-12" />
        <h2 class="text-lg font-semibold">SevenDwarfs</h2>
      </div>

      <div class="mt-4 flex items-center space-x-4">
        <img src="newID.jpg" alt="Admin" class="rounded-full w-10 h-10" />
        <div>
          <h3 class="text-sm font-semibold"><?php echo htmlspecialchars($admin_name); ?></h3>
          <p class="text-xs text-gray-500"><?php echo htmlspecialchars($admin_role); ?></p>
        </div>
      </div>
    </div>

    <nav class="mt-6">
      <ul>
        <li class="px-4 py-2 hover:bg-gray-200">
          <a href="dashboard.php" class="flex items-center"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
        </li>

        <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="userMenu = !userMenu">
          <div class="flex items-center justify-between">
            <span class="flex items-center"><i class="fas fa-users-cog mr-2"></i>User Management</span>
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': userMenu }"></i>
          </div>
        </li>
        <ul x-show="userMenu" x-transition class="pl-8 text-sm text-gray-700 space-y-1">
          <li class="py-1 hover:text-pink-600"><a href="users.php" class="flex items-center"><i class="fas fa-user mr-2"></i>User</a></li>
          <li class="py-1 hover:text-pink-600"><a href="user_types.php" class="flex items-center"><i class="fas fa-id-badge mr-2"></i>Type</a></li>
          <li class="py-1 hover:text-pink-600"><a href="user_status.php" class="flex items-center"><i class="fas fa-toggle-on mr-2"></i>Status</a></li>
          <li class="py-1"><a href="customers.php" class="flex items-center space-x-2 hover:text-pink-600"><i class="fas fa-users"></i><span>Customer</span></a></li>
        </ul>

        <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="productMenu = !productMenu">
          <div class="flex items-center justify-between">
            <span class="flex items-center"><i class="fas fa-box-open mr-2"></i>Product Management</span>
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': productMenu }"></i>
          </div>
        </li>
        <ul x-show="productMenu" x-transition class="pl-8 text-sm text-gray-700 space-y-1">
          <li class="py-1 bg-pink-100 text-pink-600 rounded"><a href="categories.php" class="flex items-center"><i class="fas fa-tags mr-2"></i>Category</a></li>
          <li class="py-1 hover:text-pink-600"><a href="products.php" class="flex items-center"><i class="fas fa-box mr-2"></i>Product</a></li>
          <li class="py-1 hover:text-pink-600"><a href="inventory.php" class="flex items-center"><i class="fas fa-warehouse mr-2"></i>Inventory</a></li>
        </ul>

        <li class="px-4 py-2 hover:bg-gray-200"><a href="orders.php" class="flex items-center"><i class="fas fa-shopping-cart mr-2"></i>Orders</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="suppliers.php" class="flex items-center"><i class="fas fa-industry mr-2"></i>Suppliers</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="payandtransac.php" class="flex items-center"><i class="fas fa-money-check-alt mr-2"></i>Payment & Transactions</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="storesettings.php" class="flex items-center"><i class="fas fa-cog mr-2"></i>Store Settings</a></li>
        <li class="px-4 py-2 hover:bg-gray-200"><a href="logout.php" class="flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Log out</a></li>
      </ul>
    </nav>
  </div>
<!-- Main Content -->
<div class="flex-1 p-6 space-y-6 transition-all duration-300 font-poppins">
  <!-- Header -->
  <div class="bg-pink-300 text-white p-4 rounded-t-2xl shadow-sm">
    <h1 class="text-2xl font-semibold">Category</h1>
  </div>

</head>
<body class="bg-[#f8fafc] font-sans text-gray-900">
  <div class="w-full min-h-screen bg-white p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-extrabold text-gray-800">Categorie List</h2>
      <button
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md px-4 py-2"
        type="button"
      >
        <i class="fas fa-plus"></i> Add Categories
      </button>
    </div>

    <div class="bg-white rounded-md shadow-sm p-4 overflow-x-auto">
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-4 sm:gap-0">
        <div class="flex items-center gap-2 text-sm text-gray-700">
          <span>Show</span>
          <select
            class="border border-gray-300 rounded-md text-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1"
            name="entries"
            aria-label="Show entries"
          >
            <option>10</option>
            <option>25</option>
            <option>50</option>
            <option>100</option>
          </select>
          <span>entries</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-700">
          <label for="search" class="whitespace-nowrap">Search:</label>
          <input
            id="search"
            type="text"
            class="border border-gray-300 rounded-md text-gray-700 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1"
          />
        </div>
      </div>

      <table class="min-w-full text-left text-sm text-gray-600 border-separate border-spacing-y-2">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="font-bold px-4 py-3 cursor-pointer select-none" scope="col">
              ID
              <i class="fas fa-sort-up text-gray-300 ml-1"></i>
            </th>
            <th class="font-bold px-4 py-3 cursor-pointer select-none" scope="col">
              CATEGORIE
              <i class="fas fa-sort-up text-gray-300 ml-1"></i>
            </th>
            <th class="font-bold px-4 py-3 cursor-pointer select-none" scope="col">
              DATE
              <i class="fas fa-sort-up text-gray-300 ml-1"></i>
            </th>
            <th class="font-bold px-4 py-3 cursor-pointer select-none" scope="col">
              STATUS
              <i class="fas fa-sort-up text-gray-300 ml-1"></i>
            </th>
            <th class="font-bold px-4 py-3 cursor-pointer select-none" scope="col">
              ACTION
              <i class="fas fa-sort-up text-gray-300 ml-1"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0001</td>
            <td class="px-4 py-3">Dress</td>
            <td class="px-4 py-3">March 13, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-green-700 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Published
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0001"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0001"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0002</td>
            <td class="px-4 py-3">Pants</td>
            <td class="px-4 py-3">January 14, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-yellow-400 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Scheduled
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0002"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0002"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0003</td>
            <td class="px-4 py-3">Trousers</td>
            <td class="px-4 py-3">February 08, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-green-700 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Published
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0003"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0003"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0004</td>
            <td class="px-4 py-3">Tops</td>
            <td class="px-4 py-3">April 02, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-yellow-400 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Scheduled
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0004"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0004"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0005</td>
            <td class="px-4 py-3">Perfume</td>
            <td class="px-4 py-3">June 19, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-green-700 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Published
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0005"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0005"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0006</td>
            <td class="px-4 py-3">Cloths</td>
            <td class="px-4 py-3">April 10, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-yellow-400 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Scheduled
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0006"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0006"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0007</td>
            <td class="px-4 py-3">Shoes</td>
            <td class="px-4 py-3">May 11, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-green-700 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Published
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0007"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0007"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
          <tr class="border-b border-gray-100">
            <td class="px-4 py-3 font-bold text-gray-800">#0008</td>
            <td class="px-4 py-3">Bags</td>
            <td class="px-4 py-3">June 13, 2021</td>
            <td class="px-4 py-3">
              <span class="inline-block bg-red-600 text-white text-[10px] font-semibold rounded-full px-2 py-0.5 select-none">
                Hidden
              </span>
            </td>
            <td class="px-4 py-3 flex gap-2">
              <button
                class="border border-gray-200 rounded-md p-1 text-green-600 hover:bg-green-50"
                aria-label="Edit #0008"
                type="button"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                class="border border-gray-200 rounded-md p-1 text-red-600 hover:bg-red-50"
                aria-label="Delete #0008"
                type="button"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>