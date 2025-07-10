<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <title>Suppliers Information</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            poppins: ['Poppins', 'sans-serif'],
          },
          colors: {
            primary: '#ec4899',
          }
        }
      }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
</head>
<body class="bg-gray-100 font-poppins text-sm">
<div class="flex min-h-screen">
  <!-- Sidebar -->
  <div class="w-64 bg-white shadow-md" x-data="{ userMenu: false, productMenu: false }">
    <div class="p-4">
      <div class="flex items-center space-x-4">
        <img src="logo2.png" alt="Logo" class="rounded-full w-12 h-12" />
        <h2 class="text-lg font-semibold">SevenDwarfs</h2>
      </div>

      <div class="mt-4 flex items-center space-x-4">
        <img src="newID.jpg" alt="Admin" class="rounded-full w-10 h-10" />
        <div>
          <h3 class="text-sm font-semibold">Admin Name</h3>
          <p class="text-xs text-gray-500">Administrator</p>
        </div>
      </div>
    </div>

    <nav class="mt-6">
      <ul>
        <li class="px-4 py-2 hover:bg-gray-200">
          <a href="dashboard.php" class="flex items-center">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
          </a>
        </li>
        <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="userMenu = !userMenu">
          <div class="flex items-center justify-between">
            <span class="flex items-center">
              <i class="fas fa-users-cog mr-2"></i>User Management
            </span>
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': userMenu }"></i>
          </div>
        </li>
        <ul x-show="userMenu" x-transition class="pl-8 text-sm text-gray-700 space-y-1">
          <li><a href="users.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-user mr-2"></i>User</a></li>
          <li><a href="user_types.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-id-badge mr-2"></i>Type</a></li>
          <li><a href="user_status.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-toggle-on mr-2"></i>Status</a></li>
          <li><a href="customers.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-users mr-2"></i>Customer</a></li>
        </ul>
        <li class="px-4 py-2 hover:bg-gray-200 cursor-pointer" @click="productMenu = !productMenu">
          <div class="flex items-center justify-between">
            <span class="flex items-center">
              <i class="fas fa-box-open mr-2"></i>Product Management
            </span>
            <i class="fas fa-chevron-down transition-transform duration-200" :class="{ 'rotate-180': productMenu }"></i>
          </div>
        </li>
        <ul x-show="productMenu" x-transition class="pl-8 text-sm text-gray-700 space-y-1">
          <li><a href="categories.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-tags mr-2"></i>Category</a></li>
          <li><a href="products.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-box mr-2"></i>Product</a></li>
          <li><a href="inventory.php" class="hover:text-pink-600 flex items-center"><i class="fas fa-warehouse mr-2"></i>Inventory</a></li>
        </ul>
        <li class="px-4 py-2 hover:bg-gray-200">
          <a href="orders.php" class="flex items-center">
            <i class="fas fa-shopping-cart mr-2"></i>Orders
          </a>
        </li>
        <li class="px-4 py-2 hover:bg-gray-200 bg-pink-100 text-pink-600 rounded-r-lg">
          <a href="suppliers.php" class="flex items-center">
            <i class="fas fa-industry mr-2"></i>Suppliers
          </a>
        </li>
        <li class="px-4 py-2 hover:bg-gray-200">
          <a href="payandtransac.php" class="flex items-center">
            <i class="fas fa-money-check-alt mr-2"></i>Payment & Transactions
          </a>
        <li class="px-4 py-2 hover:bg-gray-200">
          <a href="storesettings.php" class="flex items-center">
            <i class="fas fa-cog mr-2"></i>Store Settings
          </a>
        </li>
        <li class="px-4 py-2 hover:bg-gray-200">
          <a href="logout.php" class="flex items-center">
            <i class="fas fa-sign-out-alt mr-2"></i>Log out
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="flex-1 p-6 overflow-auto">
    <!-- Header -->
    <div class="bg-pink-300 text-white p-4 rounded-t-2xl shadow-sm mb-4">
      <h1 class="text-2xl font-semibold">Suppliers Information</h1>
    </div>

    </head>
 <body class="bg-white text-gray-900 font-sans">
  <div class="max-w-full mx-4 my-6">
   <div class="flex justify-between items-center border-b border-gray-200 pb-3 mb-4">
    <h2 class="text-lg font-extrabold text-gray-900">
    
    </h2>
    <button class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" type="button">
     <i class="fas fa-plus">
     </i>
     Add Suppliers
    </button>
   </div>
   <div class="bg-white border border-gray-200 rounded-md shadow-sm p-4">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-4 md:gap-0">
     <div class="flex items-center gap-2 text-sm text-gray-700">
      <label class="whitespace-nowrap" for="entries">
       Show
      </label>
      <select class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" id="entries" name="entries">
       <option>
        10
       </option>
       <option>
        25
       </option>
       <option>
        50
       </option>
       <option>
        100
       </option>
      </select>
      <span>
       entries
      </span>
     </div>
     <div class="flex items-center gap-2 text-sm text-gray-700">
      <label class="whitespace-nowrap" for="search">
       Search:
      </label>
      <input class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" id="search" name="search" type="search"/>
     </div>
    </div>
    <div class="overflow-x-auto">
     <table class="min-w-full text-left text-sm text-gray-700 border-separate border-spacing-y-2">
      <thead>
       <tr class="bg-gray-50 border-b border-gray-200">
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         ID
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         ITEMS
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         SUPPLIERS
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         SUPPLIERS REGDATE
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         MAIL
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         PHONE
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         ADDRESS
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         TAX NO
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
        <th class="px-3 py-2 font-extrabold cursor-pointer select-none" scope="col">
         ACTIONS
         <i class="fas fa-sort-up ml-1 text-gray-400 text-xs">
         </i>
        </th>
       </tr>
      </thead>
      <tbody>
       <tr class="bg-white border border-gray-100 rounded-md">
        <td class="px-3 py-3 font-extrabold text-gray-900 whitespace-nowrap">
         #SP-00002
        </td>
        <td class="px-3 py-3">
         Tops
        </td>
        <td class="px-3 py-3 flex items-center gap-2 font-extrabold text-gray-900 whitespace-nowrap">
         Rustom Dalope
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         12/03/2021
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Rustomdalope@gmail.com
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         202-555-0983
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Bayambang,Pangasinan
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         5869
        </td>
        <td class="px-3 py-3 whitespace-nowrap flex gap-2">
         <button aria-label="Edit Rustom Dalope" class="text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1" type="button">
          <i class="fas fa-edit">
          </i>
         </button>
         <button aria-label="Delete Rustom Dalope" class="text-red-600 hover:text-red-700 border border-red-300 rounded px-2 py-1" type="button">
          <i class="fas fa-trash-alt">
          </i>
         </button>
        </td>
       </tr>
       <tr class="bg-white border border-gray-100 rounded-md">
        <td class="px-3 py-3 font-extrabold text-gray-900 whitespace-nowrap">
         #SP-00004
        </td>
        <td class="px-3 py-3">
         Tshirts
        </td>
        <td class="px-3 py-3 flex items-center gap-2 font-extrabold text-gray-900 whitespace-nowrap">
         Ricky Dela Cruz
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         16/03/2021
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Rickydelacruz@gmail.com
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         843-555-0175
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
        Basista,Pangasinan
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         4659
        </td>
        <td class="px-3 py-3 whitespace-nowrap flex gap-2">
         <button aria-label="Edit Ricky Dela Cruz" class="text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1" type="button">
          <i class="fas fa-edit">
          </i>
         </button>
         <button aria-label="Delete Ricky Dela Cruz" class="text-red-600 hover:text-red-700 border border-red-300 rounded px-2 py-1" type="button">
          <i class="fas fa-trash-alt">
          </i>
         </button>
        </td>
       </tr>
       <tr class="bg-white border border-gray-100 rounded-md">
        <td class="px-3 py-3 font-extrabold text-gray-900 whitespace-nowrap">
         #SP-00006
        </td>
        <td class="px-3 py-3">
         Shoes
        </td>
        <td class="px-3 py-3 flex items-center gap-2 font-extrabold text-gray-900 whitespace-nowrap">
         Trecy Rosales
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         12/03/2021
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
        Trecyrosales@gmail.com
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         303-555-0151
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Bayambang,Pangasinan
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         4568
        </td>
        <td class="px-3 py-3 whitespace-nowrap flex gap-2">
         <button aria-label="Edit Trecy Rosales" class="text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1" type="button">
          <i class="fas fa-edit">
          </i>
         </button>
         <button aria-label="Delete Trecy Rosales" class="text-red-600 hover:text-red-700 border border-red-300 rounded px-2 py-1" type="button">
          <i class="fas fa-trash-alt">
          </i>
         </button>
        </td>
       </tr>
       <tr class="bg-white border border-gray-100 rounded-md">
        <td class="px-3 py-3 font-extrabold text-gray-900 whitespace-nowrap">
         #SP-00011
        </td>
        <td class="px-3 py-3">
         Bags
        </td>
        <td class="px-3 py-3 flex items-center gap-2 font-extrabold text-gray-900 whitespace-nowrap">
        Johnvir Sabado
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         25/02/2021
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Johnvirsabado@gmail.com
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         404-555-0100
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Bayambang,Pangasinan
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         2567
        </td>
        <td class="px-3 py-3 whitespace-nowrap flex gap-2">
         <button aria-label="Edit Johnvir Sabado" class="text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1" type="button">
          <i class="fas fa-edit">
          </i>
         </button>
         <button aria-label="Delete Johnvir Sabado" class="text-red-600 hover:text-red-700 border border-red-300 rounded px-2 py-1" type="button">
          <i class="fas fa-trash-alt">
          </i>
         </button>
        </td>
       </tr>
       <tr class="bg-white border border-gray-100 rounded-md">
        <td class="px-3 py-3 font-extrabold text-gray-900 whitespace-nowrap">
         #SP-00014
        </td>
        <td class="px-3 py-3">
         Pants
        </td>
        <td class="px-3 py-3 flex items-center gap-2 font-extrabold text-gray-900 whitespace-nowrap">
         Eya Barcena
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         18/01/2021
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Eyabarcena@gmail.com
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         502-555-0133
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Bayambang,Pangasinan
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         6584
        </td>
        <td class="px-3 py-3 whitespace-nowrap flex gap-2">
         <button aria-label="Edit Eya Barcena" class="text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1" type="button">
          <i class="fas fa-edit">
          </i>
         </button>
         <button aria-label="Delete Eya Barcena" class="text-red-600 hover:text-red-700 border border-red-300 rounded px-2 py-1" type="button">
          <i class="fas fa-trash-alt">
          </i>
         </button>
        </td>
       </tr>
       <tr class="bg-white border border-gray-100 rounded-md">
        <td class="px-3 py-3 font-extrabold text-gray-900 whitespace-nowrap">
         #SP-00018
        </td>
        <td class="px-3 py-3">
        Perfume
        </td>
        <td class="px-3 py-3 flex items-center gap-2 font-extrabold text-gray-900 whitespace-nowrap">
         Abi Eslao
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         16/02/2021
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Abieslao@gmail.com
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         502-555-0118
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         Basista,Pangasinan
        </td>
        <td class="px-3 py-3 whitespace-nowrap">
         7586
        </td>
        <td class="px-3 py-3 whitespace-nowrap flex gap-2">
         <button aria-label="Edit Abi Eslao" class="text-green-600 hover:text-green-700 border border-green-300 rounded px-2 py-1" type="button">
          <i class="fas fa-edit">
          </i>
         </button>
         <button aria-label="Delete Abi Eslao" class="text-red-600 hover:text-red-700 border border-red-300 rounded px-2 py-1" type="button">
          <i class="fas fa-trash-alt">
          </i>
         </button>
        </td>
       </tr>
      </tbody>
     </table>
    </div>
    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 text-xs text-gray-700 font-normal">
     <div class="mb-2 sm:mb-0">
      Showing 1 to 6 of 6 entries
     </div>
     <nav aria-label="Pagination" class="inline-flex rounded-md shadow-sm" role="navigation">
      <button aria-label="Previous page" class="rounded-l-md bg-indigo-100 text-indigo-600 px-4 py-2 font-medium hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
       Previous
      </button>
      <button aria-current="page" aria-label="Page 1" class="bg-indigo-700 text-white px-4 py-2 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
       1
      </button>
      <button aria-label="Next page" class="rounded-r-md bg-indigo-100 text-indigo-600 px-4 py-2 font-medium hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
       Next
      </button>
     </nav>
    </div>
   </div>
  </div>
 </body>
</html>
