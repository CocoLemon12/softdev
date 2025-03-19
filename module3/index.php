<?php
include 'database.php';

$brandQuery = "SELECT DISTINCT product_brand FROM products ORDER BY product_brand ASC";
$brandResult = $conn->query($brandQuery);

$categoryQuery = "SELECT DISTINCT product_category FROM products ORDER BY product_category ASC";
$categoryResult = $conn->query($categoryQuery);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="icon" type="image/x-icon" href="assets/logo.png">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script src="script/script.js"></script>
  <title>Product Catalog</title>
</head>

<body>
  <header class="head">
    <div class="left-side-button">

      <div class="logo"><img src="assets/logo.png" alt=""></div>

      <nav class="navbar">
        <a href="#">Home</a>
        <a href="#" class="active">Products</a>
        <a href="#">About</a>
        <a href="#">Contact</a>

      </nav>
      <div class="search-bar">
        <input type="text" id="search-input" placeholder="search" onkeyup="liveSearch()" />
        <a href="#"> <i class="bx bx-search"></i> </a>
      </div>


    </div>


    <div class="right-side-button">

      <div class="wishlist-btn">
        <a href="#"><i class="bx bx-heart"></i></a>
        <div class="notification">0</div>
      </div>

      <div class="cart-btn">
        <a href=""><i class="bx bxs-cart"></i></a>
        <div class="notification">0</div>
      </div>
      <div class="login-buttons">
        <a href="#"><i class="bx bxs-user"></i></a>
      </div>
    </div>
  </header>


  <header id="header-catalog">
    <h1>Products</h1>
  </header>
  <div class="container">

    <div id="filters">
      <div class="filter-header">Filter By</div>

      <div class="filter" id="price-filter">
        <div class="filter-header">Price</div>
        <div id="range">
          <input class="price-range" type="number" id="min-price" name="min-price" placeholder="MIN" />
          -
          <input class="price-range" type="number" id="max-price" name="max-price" placeholder="MAX" />
        </div>
      </div>

      <div class="filter">
        <div class="filter-header">Brand</div>
        <div id="list">
          <ul>
            <?php
            if ($brandResult->num_rows > 0) {
              while ($brandRow = $brandResult->fetch_assoc()) {
                echo "<li> <input type='checkbox' value='" . htmlspecialchars($brandRow['product_brand']) . "' name='brand'>" . htmlspecialchars($brandRow['product_brand']) . "</li>";
              }
            }
            ?>
          </ul>
        </div>
      </div>

      <div class="filter">

        <div class="filter-header">Category</div>
        <div id="list">
          <ul>
            <?php
            if ($categoryResult->num_rows > 0) {
              while ($categoryRow = $categoryResult->fetch_assoc()) {
                echo "<li> <input type='checkbox' value='" . htmlspecialchars($categoryRow['product_category']) . "' name='category'>" . htmlspecialchars($categoryRow['product_category']) . "</li>";
              }
            }
            ?>
          </ul>
        </div>
      </div>



      <div class="filter">
        <div class="filter-header">Rating</div>
        <div id="list">
          <ul id="star-rating">
            <li> <input type="checkbox" value="5" name="5star">
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
            </li>
            <li> <input type="checkbox" value="4" name="4star">
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bx-star'></i>
            </li>
            <li> <input type="checkbox" value="3" name="3star">
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bx-star'></i>
              <i class='bx bx-star'></i>
            </li>
            <li> <input type="checkbox" value="2" name="2star">
              <i class='bx bxs-star'></i>
              <i class='bx bxs-star'></i>
              <i class='bx bx-star'></i>
              <i class='bx bx-star'></i>
              <i class='bx bx-star'></i>
            </li>
            <li> <input type="checkbox" value="1" name="1star">
              <i class='bx bxs-star'></i>
              <i class='bx bx-star'></i>
              <i class='bx bx-star'></i>
              <i class='bx bx-star'></i>
              <i class='bx bx-star'></i>
            </li>
          </ul>
        </div>
      </div>


    </div>

    <div id="products">
      <div id="product-header">
        <div>
          <h4><?php echo $result->num_rows; ?> Products Found</h4>
        </div>

      </div>

      <div id="product-list">
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $stars = "";
            for ($i = 1; $i <= 5; $i++) {
              if ($i <= $row['rating']) {
                $stars .= "<i class='bx bxs-star'></i>";
              } else {
                $stars .= "<i class='bx bx-star'></i>";
              }
            }

            // Decode JSON to array
            $image_urls = json_decode($row['product_images'], true);
            $first_image = !empty($image_urls) ? $image_urls[0] : 'default.jpg';
            echo "<div class='product-card'>
                  <div class='product-img'><img src='" . $first_image . "' alt=''></div>
                  <div class='product-name'>" . $row['product_name'] . "</div>
                  <div class='product-rating'>$stars " . number_format($row['rating'], 2) . "</div>
                  <div class='product-price'>₱ " . number_format($row['product_price'], 2) . "</div>
                  <div class='product-buttons'>
                      <form action='productdescrption.php' method='POST'>
                          <input type='hidden' name='product_id' value='" . $row['product_id'] . "'>
                          <button type='submit' class='buy-now'>Buy Now</button>
                      </form>
                      <button class='cart' name='cart'><i class='bx bx-cart'></i></button>
                      <button class='wishlist' name = 'wishlist'><i class='bx bx-heart'></i></button>
                  </div>
              </div>";
          }
        } else {
          echo "<p>No products found.</p>";
        }
        ?>


      </div>

    </div>
  </div>


  <footer>
    <div class="upperfooter">
      <div class="column-footlogo">
        <div class="logo">DJM <br />TECHSTORE</div>
        <p>Wala kabang Keyboard or bulok bulok na ang mga keycaps mo eto ang solution para sayo</p>
      </div>
      <div class="column">
        <h1>Quick</h1>
        <a href="">Home</a>
        <a href="">About Us</a>
        <a href="">Contact Us</a>
      </div>
      <div class="column">
        <h1>Menu</h1>
        <a href="">Home</a>
        <a href="">Product</a>
      </div>
      <div class="column">
        <h1>Company</h1>
        <a href="">About Us</a>
        <a href="">Contact Us</a>
      </div>

    </div>

    <div class="line"></div>

    <div class="lower-footer">
      <div class="footer-socmed">
        <a href="#"><i class='bx bxl-facebook'></i></a>
        <a href="#"><i class='bx bxl-instagram'></i></a>
        <a href="#"><i class='bx bxl-twitter'></i></a>
      </div>

      <div class="footer-credit">
        <p>&copy; 2025 By DJM Techstore. All Rights Reserved</p>
      </div>

    </div>





  </footer>
</body>

</html>