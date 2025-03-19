<?php
require_once('config.php');

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles/navbar.css" />
    <link rel="stylesheet" href="styles/globalStyle.css" />
    <link rel="stylesheet" href="styles/profile.css" />
    <link rel="stylesheet" href="styles/updateProfile.css" />

    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles/myAccount.css" />

    <link rel="stylesheet" href="styles/sidebar.css" />
  </head>
  <body>
  <div>
    <?php

    $UserID = 1; 

    $sql = "SELECT firstName, lastName, Email, Address FROM users_test WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$UserID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $Fname = htmlspecialchars($user['firstName']);
        $Lname = htmlspecialchars($user['lastName']);
        $Email = htmlspecialchars($user['Email']);
        $Address = htmlspecialchars($user['Address']);
    } else {
        $Fname = $Lname = $Email = $Address = "Not found";
    };


    if(isset($_POST["save"])){
      $Fname = $_POST["Fname"];
      $Lname = $_POST["Lname"];
      $Email = $_POST["Email"];
      $Address = $_POST["Address"];

    //   $sql = "INSERT INTO users_test (firstName,lastName, Email, Address) VALUES(?,?,?,?)";
    //   $stmtinsert = $db ->prepare($sql);
    //   $result = $stmtinsert->execute([$Fname, $Lname, $Email, $Address]);
    //   if($result){
    //     echo "Success";
    // } else { 
    //   echo "failed";
    // }


    // Check if any field is empty
if (empty($Fname) || empty($Lname) || empty($Email) || empty($Address)) {
    } else {
        // Database connection (Ensure $db is initialized)
        // Example: $db = new PDO("mysql:host=localhost;dbname=testdb", "username", "password");

        $sql = "UPDATE users_test SET firstName = ?, lastName = ?, Email = ?, Address = ? WHERE id = 1";
        $stmtupdate = $db->prepare($sql);

        // Execute update query
        $result = $stmtupdate->execute([$Fname, $Lname, $Email, $Address]);

        if ($result) {
            echo "Update successful";
        } else {
            echo "Update failed";
        }
    }
      }  ?>


  </div>








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
          <div class="my-profile-css">
            <h2>My Profile</h2>
          </div>
          <div class="profile-image-css">
            <img src="./assets/images/profile.png" />
          </div>

          <form action="updateProfile.php" method="post">
            <div class="profile-fields">
              <div class="fields-column-one">
                <p>First Name</p>
                <input
                  name="Fname"
                  type="text"
                  class="field-css"
                  placeholder="Enter First Name"
                  value="<?php echo $Fname; ?>"
                />
                <p>Email</p>
                <input
                  name="Email"
                  type="email"
                  class="field-css"
                  placeholder="Enter Email"
                  value="<?php echo $Email; ?>"
                />
              </div>
              <div class="fields-column-two">
                <p>Last Name</p>
                <input
                name="Lname"
                  type="text"
                  class="field-css"
                  placeholder="Enter Last Name"
                  value="<?php echo $Lname; ?>"
                />
                <p>Address</p>
                <input
                  name="Address"
                  type="text"
                  class="field-css"
                  placeholder="Enter Address"
                  value="<?php echo $Address; ?>"
                />
              </div>
            </div>

            <!-- Change Password Section -->
            <div class="password-section">
              <p>Change Password</p>
              <input
                type="password"
                class="field-css"
                id="current-password"
                placeholder="Current Password"
              />
              <input
                type="password"
                class="field-css"
                id="new-password"
                placeholder="New Password"
              />
              <input
                type="password"
                class="field-css"
                id="confirm-password"
                placeholder="Confirm New Password"
              />
            </div>
            <!-- Buttons -->
            <div class="profile-buttons">
              <button
                class="cancel-button"
                onclick="window.location.href='userAccount.html'"
              >
                Cancel
              </button>
              <input
                type="submit"
                name="save"
                value="Save"
                class="save-button"
                onclick="window.location.href='userAccount.html'"
              />
            </div>
          </form>
        </div>
      </div>
    </main>
  </body>
</html>
