<?php
  require_once("productsDB.php");

  $sql = "SELECT * FROM tbl_orders";
  $all_product = $mysqli ->query($sql);

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
    <link rel="stylesheet" href="./styles/purchaseHistory.css" />
  </head>

  <body>
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
          <a href="#"><i class="bx bx-search bx-md"></i> </a>
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
          <div class="purchase-history">
            <h1>My Purchase History</h1>
          </div>

          <?php while( $row = mysqli_fetch_assoc($all_product) ){
          ?>
          <div class="purchase-product-main">
            <div class="date-receive-container">
              <div class="date-receive-css">
                <i class="bx bxs-truck bx-md" color="purple"></i>
                <p>Date Received: <?php echo $row["order_Arrival"];?></p>
              </div>
              <p class="parcel-delivered">Parcel has been delivered</p>
            </div>
            <div class="product-container">
              <img class="product-image" src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" alt="Image">

              <div class="name-price-qty">
                <p class="product-name">
                  <?php echo $row["product_Name"];?>
                </p>

                <div class="price-qty">
                  <p>Price: ₱<?php echo $row["total_Price"];?></p>
                  <p>Qty: <?php echo $row["order_Qty"];?></p>
                </div>
              </div>
            </div>
            <div class="buy-again-container">
              <button class="buy-again-button">Buy Again</button>
            </div>
          </div>
          <?php
          }
          ?>
        </div>
      </div>
    </main>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
  </body>
</html>
