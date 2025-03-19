
<?php
require 'productsDB.php'; // Include the database connection

$UserID = 1; // Change this to the specific user ID you want to fetch

$sql = "SELECT first_name, last_name, email, city FROM tbl_users WHERE userID = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the result contains data
    if ($result && $user = $result->fetch_assoc()) {
        $Fname = htmlspecialchars($user['first_name']);
        $Lname = htmlspecialchars($user['last_name']);
        $Email = htmlspecialchars($user['email']);
        $Address = htmlspecialchars($user['city']);
    } else {
        $Fname = $Lname = $Email = $Address = "Not found";
    }

    $stmt->close();
} else {
    die("Query preparation failed: " . $mysqli->error);
}

$mysqli->close();

?>

<!-- HTML Form -->




<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles/navbar.css" />
    <link rel="stylesheet" href="styles/globalStyle.css" />
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles/myAccount.css" />
    <link rel="stylesheet" href="styles/sidebar.css" />
    <link rel="stylesheet" href="styles/profile.css" />
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
          <a href="./userAccount.php" class="left-nav-link"
            ><i class="bx bx-user-circle bx-sm"></i>
            <p class="my-account-css">My Account</p></a
          >
          <a href="./purchaseHistory.php" class="left-nav-link"
            ><i class="bx bx-cart-download bx-sm"></i>
            <p class="my-account-css">Purchase History</p></a
          >
          <a href="./userAccount.php" class="left-nav-link"
            ><i class="bx bx-heart bx-sm"></i>
            <p class="my-account-css">Wishlist</p></a
          >
          <a href="./loyalty.html" class="left-nav-link"
            ><i class="bx bx-credit-card-front bx-sm"></i>
            <p class="my-account-css">Loyalty Awards</p></a
          >
        </div>
      </div>

      <!-- right -->
      <div class="right-content">
        <div class="right-content-sub">
          <div class="my-profile-css">
            <h2>My Profile</h2>
          </div>
          <div class="profile-image-css">
            <img src="./assets/images/profile.png" />
          </div>
          <div class="profile-fields">
            <div class="fields-column-one">
              <p class="field-text-header">First Name</p>
              <div class="field-new-css">
                <p class="display-profile-detail"><?php echo $Fname; ?></p>
              </div>
              <p class="field-text-header">Email</p>
              <div class="field-new-css"><p class="display-profile-detail"><?php echo $Email; ?></p></div>
            </div>
            <div class="fields-column-two">
              <p class="field-text-header">Last Name</p>
              <div class="field-new-css"> <p class="display-profile-detail"><?php echo $Lname; ?></p></div>
              <p class="field-text-header">Address</p>
              <div class="field-new-css"> <p class="display-profile-detail"><?php echo $Address; ?></p></div>
            </div>
          </div>
          <div class="edit-profile-button">
            <button
              class="edit-profile-button-css"
              onclick="window.location.href='updateProfile.php'"
            >
              Edit Profile
            </button>
          </div>
           
        </div>
      </div>
    </main>
  </body>
</html>
