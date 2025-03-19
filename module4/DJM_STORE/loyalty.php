<?php
require_once('productsDB.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Purchase History</title>
    <link rel="stylesheet" href="styles/navbar.css" />
    <link rel="stylesheet" href="styles/globalStyle.css" />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles/myAccount.css" />
    <link rel="stylesheet" href="styles/sidebar.css" />
    <link rel="stylesheet" href="./styles/loyalty.css" />
  </head>

  <body>

  <?php
  $sql = "SELECT COUNT(*) AS total FROM products";
  $result = $conn->query($sql);

  $row = $result->fetch_assoc();
  $total_products = $row['total'] ;

  $need_value;
  $max_value;
  if($total_products < 10){
    $max_value = 10;
    $need_value = $max_value - $total_products;

  }
  else if($total_products < 20){
    $max_value = 20;
    $need_value = $max_value - $total_products;
  }
  else if($total_products < 30){
    $max_value = 30;
     $need_value = $max_value - $total_products;
  }
  else if($total_products < 40){
    $max_value = 40;
     $need_value = $max_value - $total_products;
  }
  else if($total_products < 50){
    $max_value = 50;
    $need_value = $max_value - $total_products;
  }
  ?>
    <nav class="nav-main-container">
      <!-- left -->
      <div class="nav-left">
        <div class="nav-logo">
          <h1>LOGO</h1>
        </div>
      </div>

      <!-- middle -->
      <div class="nav-middle">
        <div class="nav-li-main-container">
          <ul class="li-main-container">
            <a class="nav-link">Home</a>
            <a class="nav-link">Product</a>
            <a class="nav-link">About</a>
            <a class="nav-link">Contact</a>
          </ul>
        </div>
        <div class="nav-search">
          <a href="#" ><i class="bx bx-search bx-md"></i> </a>
        </div>
      </div>

      <!-- right -->
      <div class="nav-right">
        <div class="wishlist-btn">
          <a href="#"><i class="bx bx-heart bx-md"></i></a>
        </div>

        <div class="cart-btn">
          <a href=""><i class="bx bxs-cart bx-md"></i></a>
        </div>
        <div class="login-buttons">
          <a href="#"><i class="bx bxs-user bx-md"></i></a>
        </div>
      </div>
    </nav>

    <!-- main -->

    <main class="main-content">
      <!-- left -->
      <div class="sidebar">
        <div class="sidebar-navigation">
          <p>Home / <span>My Account</span></p>
        </div>
        <div class="sidebar-account-name">
          <p>Hello, <span class="user-text">Adrian!</span></p>
        </div>
        <div class="nav-left-profile">
          <img src="./assets/images/profile.png" />
        </div>
        <div class="left-nav-links">
          <a href="./userAccount.php" class="left-nav-link">
            <i class="bx bx-user-circle bx-sm"></i>
            <p class="my-account-css">My Account</p>
          </a>
          <a href="./purchaseHistory.php" class="left-nav-link">
            <i class="bx bx-cart-download bx-sm"></i>
            <p class="my-account-css">Purchase History</p>
          </a>
          <a href="./userAccount.php" class="left-nav-link">
            <i class="bx bx-heart bx-sm"></i>
            <p class="my-account-css">Wishlist</p>
          </a>
          <a href="./loyalty.html" class="left-nav-link">
            <i class="bx bx-credit-card-front bx-sm"></i>
            <p class="my-account-css">Loyalty Awards</p>
          </a>
        </div>
      </div>

      <!-- right -->
      <div class="right-content">
        <div class="right-content-sub">
          <div class="loyalty-container">
            <h1 style="color: #ba43ce">Classic</h1>
            <a class="loyalty-header">
              <p>See all tiers</p>
              <i class="bx bx-chevron-right bx-md"></i>
            </a>
          </div>
          <div class="card-container">
            <div>
              <p class="card-container-text">To Maintain Current Tier:</p>
              <div>
                <div class="card-img">
                  <img src="./assets/images/card1.png" />
                </div>
              </div>
            </div>
          </div>
          <div>
            <div class="loyalty-bar-text">
              <p class="loyalty-bar-num"><?php echo $total_products?>/<?php echo $max_value?></p>
              <p class>Complete <?php echo $need_value?> more orders.</p>
            </div>
            <div class="loyalty-bar">
              <progress class="progress-bar" value="<?php echo $total_products?>" max="<?php echo $max_value?>"></progress>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
  </body>
</html>
