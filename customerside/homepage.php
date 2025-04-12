<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seven Dwarfs Boutique</title>
  <style>
    body {
      margin: 0;
      font-family: sans-serif;
      background-color: #ffcbe0;
    }

    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 30px;
      background: linear-gradient(to right, #ffdee9, #ffcbe0);
      position: relative;
    }

    .logo {
      display: flex;
      align-items: center;
    }

    .logo img {
      height: 60px;
    }

    .menu-icon {
      font-size: 28px;
      cursor: pointer;
      z-index: 1001;
      color: white;
    }

    .search-bar {
      flex-grow: 1;
      margin: 0 30px;
    }

    .search-bar input[type="text"] {
      width: 50%;
      padding: 8px;
      border-radius: 20px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    .icons {
      font-size: 24px;
      display: flex;
      gap: 15px;
    }

    nav {
      display: flex;
      justify-content: center;
      gap: 20px;
      background-color: #f973b6;
      padding: 10px 0;
    }

    nav a {
      text-decoration: none;
      font-weight: bold;
      color: white;
    }

    nav .active {
      border: 2px solid white;
      padding: 4px 10px;
      border-radius: 5px;
    }

    .gallery {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 40px;
      flex-wrap: wrap;
    }

    .item {
      text-align: center;
    }

    .item img {
      width: 200px;
      height: auto;
      border-radius: 5px;
    }

    .item h3 {
      margin-top: 10px;
      font-size: 20px;
      letter-spacing: 1px;
    }

    .item p {
      margin: 0;
      font-size: 16px;
      font-style: italic;
    }

    /* Sidebar styles */
    .sidebar {
      height: 100%;
      width: 0;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #f973b6;
      overflow-x: hidden;
      transition: 0.3s;
      padding-top: 60px;
      z-index: 1000;
    }

    .sidebar a {
      padding: 15px 30px;
      text-decoration: none;
      font-size: 18px;
      color: white;
      display: block;
      transition: 0.3s;
    }

    .sidebar a:hover {
      background-color: #ffb3d9;
    }

    .sidebar .closebtn {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 30px;
    }

    @media (max-width: 768px) {
      nav {
        display: none;
      }
    }
  </style>
</head>
<body>

  <header>
  <div id="menuIcon" class="menu-icon" onclick="openSidebar()">&#9776;</div>


    <div class="logo">
      <img src="https://i.ibb.co/Z6VKJ9B/seven-dwarfs.png" alt="Seven Dwarfs Boutique Logo" />
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Search" />
    </div>

    <div class="icons">
      <span>&#128100;</span>
      <span>&#128722;</span>
    </div>
  </header>

  <div id="mySidebar" class="sidebar">
    <a href="javascript:void(0)" class="closebtn" onclick="closeSidebar()">&times;</a>
    <a href="homepage.php" class="active">HOME</a>
    <a href="backinstock.php.php">BACK IN STOCK</a>
    <a href="dresses.php">DRESSES</a>
    <a href="tops.php">TOPS</a>
    <a href="tshirts.php">T-SHIRTS</a>
    <a href="coords.php">COORDINATESS</a>
    <a href="pants.php">PANTS</a>
    <a href="bag.php">BAG</a>
    <a href="perfumes.php">PERFUMES </a>
  </div>

  <nav>
  <a href="homepage.php" class="active">HOME</a>
    <a href="trending.php">TRENDING</a>
    <a href="bestsellers.php">BEST SELLERS</a>
    <a href="sale.php">SALE</a>
    <a href="sets.php">SETS</a>
    <a href="newarriv.php">NEW ARRIVALS</a>
  </nav>

  <div class="gallery">
    <div class="item">
      <img src="https://i.ibb.co/FJX7pPY/lookgood.png" alt="Look Good Feel Good" />
      <h3>LOOK GOOD</h3>
      <p>FEEL GOOD</p>
    </div>
    <div class="item">
      <img src="https://i.ibb.co/n1tZD1B/upgrade.png" alt="Upgrade Your Look" />
      <h3>UPGRADE</h3>
      <p><em>your</em> LOOK</p>
    </div>
    <div class="item">
      <img src="https://i.ibb.co/w0WnCwN/slay.png" alt="Slay" />
      <h3>SLAY</h3>
    </div>
  </div>

  <script>
  function openSidebar() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("menuIcon").style.display = "none";
  }

  function closeSidebar() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("menuIcon").style.display = "block";
  }
</script>

</body>
</html>
