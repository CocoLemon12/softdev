<?php
include 'database.php';

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_POST['product_id'])) {
  die("Error: Product ID is missing!");
}

$product_id = intval($_POST['product_id']);
//query to get product details
$sql = "SELECT * FROM products WHERE product_id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
} else {
  die("Error: Product not found.");
}
//Query for related products
$currentCategory = $row['product_category'];
$currentBrand = $row['product_brand'];
$relatedQuery = "SELECT * FROM products WHERE (product_category = '$currentCategory' OR product_brand = '$currentBrand') 
                 AND product_id != $product_id ORDER BY (product_category = '$currentCategory') DESC, rating DESC LIMIT 4";

$relatedResult = $conn->query($relatedQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="icon" type="image/x-icon" href="assets/logo.png">
  <link rel="stylesheet" href="css/product.css" />
  <title><?php echo $row['product_name']; ?></title>
</head>

<body>
  <header class="head">
    <div class="left-side-button">

      <div class="logo"><img src="assets/logo.png" alt=""></div>

      <nav class="navbar">
        <a href="#">Home</a>
        <a href="#">Products</a>
        <a href="#">About</a>
        <a href="#">Contact</a>

      </nav>
      <div class="search-bar">
        <input type="text" placeholder="search" />
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


  <div class="filter-back">
    <a href="index.php">Products</a>
    <p>></p>
    <p><?php echo $row['product_category']; ?></p>
    <p>></p>
    <p><?php echo $row['product_name']; ?></p>
  </div>
  <?php
  $image_urls = json_decode($row['product_images'], true) ?? [];

  $first_image = isset($image_urls[0]) ? $image_urls[0] : '';
  $second_image = isset($image_urls[1]) ? $image_urls[1] : '';
  $third_image = isset($image_urls[2]) ? $image_urls[2] : '';
  $fourth_image = isset($image_urls[3]) ? $image_urls[3] : '';
  $fifth_image = isset($image_urls[4]) ? $image_urls[4] : '';
  ?>


  <div id="product-modal" class="modal">
    <div class="modal-content">
      <div class="modal-body">
        <div class="modal-image">
          <div class="modal-primary">
            <img id="main-image" src="<?php echo $first_image; ?>" alt="<?php echo $row['product_name']; ?>">
          </div>


          <div class="image-thumbnails">
            <!-- lalagay dito yung mga other perspective nung acer -->
            <?php if (!empty($first_image)): ?>
              <img src="<?php echo $first_image; ?>" alt="Thumbnail 1" class="thumbnail">
            <?php endif; ?>

            <?php if (!empty($second_image)): ?>
              <img src="<?php echo $second_image; ?>" alt="Thumbnail 2" class="thumbnail">
            <?php endif; ?>

            <?php if (!empty($third_image)): ?>
              <img src="<?php echo $third_image; ?>" alt="Thumbnail 3" class="thumbnail">
            <?php endif; ?>

            <?php if (!empty($fourth_image)): ?>
              <img src="<?php echo $fourth_image; ?>" alt="Thumbnail 4" class="thumbnail">
            <?php endif; ?>

            <?php if (!empty($fifth_image)): ?>
              <img src="<?php echo $fifth_image; ?>" alt="Thumbnail 5" class="thumbnail">
            <?php endif; ?>
          </div>
          <div class="modal-related">
            <h1>Related Items</h1>
            <div class="related-products">
              <?php
              while ($relatedRow = $relatedResult->fetch_assoc()) {
                $related_images = json_decode($relatedRow['product_images'], true);
                if (!is_array($related_images)) {
                  $related_images = [];
                }
                $related_first_image = !empty($related_images[0]) ? $related_images[0] : 'default.jpg';

                $stars = "";
                for ($i = 1; $i <= 5; $i++) {
                  $stars .= ($i <= $relatedRow['rating']) ? "<i class='bx bxs-star'></i>" : "<i class='bx bx-star'></i>";
                }

                echo "<div class='product-card'>
                        <div class='product-img'><img src='$related_first_image' alt=''></div>
                        <form action='productdescrption.php' method='POST'>
                            <input type='hidden' name='product_id' value='" . $relatedRow['product_id'] . "'>
                            <button type='submit' class='product-name'>" . htmlspecialchars($relatedRow['product_name']) . "</button>
                        </form>           
                        <div class='product-rating'>$stars " . number_format($relatedRow['rating'], 2) . "</div>
                        <div class='product-price'>₱ " . number_format($relatedRow['product_price'], 2) . "</div>
                      </div>";
              }
              ?>
            </div>
          </div>
        </div>

        <div class="modal-description">
          <div class="modal-item-description">
            <p><?php echo $row['product_name']; ?></p>
            <div class="modal-rating">
              <span>
                <?php
                echo $row['rating'];
                $rating = $row['rating'];

                echo "<div class='product-rating'>";
                for ($i = 1; $i <= 5; $i++) {
                  if ($i <= $rating) {
                    echo "<i class='bx bxs-star filled'></i>";
                  } else {
                    echo "<i class='bx bx-star empty'></i>";
                  }
                }
                echo "</div>";
                echo $row['rating_count'] . " ratings";
                ?>
              </span>
            </div>
            <div class="modal-availability">
              <?php if ($row['product_stock'] > 0): ?>
                <p><span>Available</span> | <?php echo $row['product_stock']; ?> in stock</p>
              <?php else: ?>
                <p><span>Not Available</span> | Out of stock</p>
              <?php endif; ?>
            </div>
            <div class="modal-price">
              <p>₱<?php echo number_format($row['product_price'], 2); ?></p>
            </div>
            <div class="modal-button">
              <div class="counter">
                <button class="btn-minus">-</button>
                <p><span>1</span></p>
                <button class="btn-plus">+</button>
              </div>

              <?php if ($row['product_stock'] > 0): ?>

                <button type="submit" name="action" value="buy" class="buy-now">Buy Now</button>

              <?php else: ?>

                <button type="submit" name="action" value="buy" class="buy-now" disabled>
                  <span class="strike-through">Buy Now</span>
                </button>

              <?php endif; ?>


              <button type="submit" name="action" name="cart" class="cart"><i class="bx bx-cart"></i></button>



              <button type="submit" name="action" name="wishlist" class="wishlist"><i class="bx bx-heart"></i></button>


              <script>
                const buyNowButtons = document.querySelectorAll('.buy-now');
                buyNowButtons.forEach(button => {
                  button.addEventListener('click', function () {
                    alert('Lalabas yung Module 4 hehehehe');
                  });
                });
              </script>

            </div>
          </div>
          <div class="modal-item-specification">
            <div class="modal-item-specific">
              <h3>Specifications</h3>
              <?php
              $specs = array_filter(explode("#", $row['product_specification']));
              echo "<ul>";
              foreach ($specs as $spec) {
                echo "<li>" . trim($spec) . "</li>";
              }
              echo "</ul>";
              ?>

            </div>
            <div class="modal-item-desc">
              <h3>Description</h3>
              <p><?php echo $row['product_description']; ?></p>
            </div>

            <div class="warranty">
              <h3 class="warranty-text">Warranty</h3>
              <div class="warrant-cont">
                <div class="warranty-text"><i class='bx bxs-check-square'></i>7 Days Replacement</div>
                <div class="warranty-text"><i class='bx bxs-check-shield'></i>1 Year Warranty</div>
                <div class="warranty-text"><i class='bx bxs-phone'></i></i>(02) 85552300</div>
                <div class="warranty-text"><i class='bx bxs-envelope'></i></i>DJCUBES.service@acer.com</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</body>
<script src="script/productdesctiption.js"></script>


</html>