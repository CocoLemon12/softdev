<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
<body>
  <header class="head">
    <div class="left-side-button">
      <div class="logo">
        <a class="logoLink" href="index.html"><img src="assets/DJMLogo.png"></a>
      </div>
      <div class="hamburger" onclick="toggleMenu()">
        <i class='bx bx-menu'></i>
      </div>
      <nav class="navbar" id="nav-menu">
        <a href="../module1/DJMTECH/DJMTECH/index.html">Home</a>
        <a href="../module3/index.php">Products</a>
        <a href="../module1/DJMTECH/DJMTECH/about.html">About</a>
        <a href="../module1/DJMTECH/DJMTECH/contact.php">Contact</a>
      </nav>
      <div class="search-bar">
        <input type="text" placeholder="search" />
        <a href="#"> <i class="bx bx-search"></i> </a>
      </div>
    </div>
    <div class="right-side-button">
      <div class="wishlist-btn">
        <a href="../module1/wishlist.html"><i class="bx bx-heart"></i></a>
      </div>
      <div class="cart-btn">
        <a href=""><i class="bx bxs-cart"></i></a>
        <div class="notification">0</div>
      </div>
      <div class="login-buttons">
        <a href="../module1/DJMTECH/DJMTECH/authentication_page.html"><i class="bx bxs-user"></i></a>
      </div>
    </div>
  </header>
  <script src="js/hamburger-menu.js"></script>
</body>

</html>