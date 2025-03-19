
<?php
require '../php/db.config.php'; 
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DJM TECH - Shopping Cart</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/cart.css">

</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="#" class="logo">DJM TECH</a>
            <nav class="nav-container">
                <a href="#" class="active">Home</a>
                <a href="#">Product</a>
                <a href="#">About</a>
                <a href="#">Contact</a>
            </nav>
            <div class="header-right">
                <i class='bx bx-search search-icon'></i>
                <div class="cart">
                    <i class='bx bx-cart cart-icon'></i>
                    <span class="cart-count">0</span>
                </div>
                <i class='bx bx-user user-icon'></i>
                <button id="theme-toggle" class="theme-toggle">
                    <i class='bx bx-moon'></i>
                </button>
            </div>
        </div>
    </header>

    <main class="main-container">
        <div class="cart-container">
            <div class="cart-section">
                <h2>Shopping Cart</h2>
                <div class="cart-items">
                    <!-- Cart items will be dynamically added here -->
                </div>
            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>₱ 0.00</span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span class="shipping-calc">Calculated after address entry</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>₱ 0.00</span>
                </div>
                <button class="checkout-btn">Checkout</button>
            </div>
        </div>

        <section class="frequently-bought">
            <h2>Frequently Bought Together</h2>
            <div class="product-slider">
                <button class="slider-btn prev"><i class='bx bx-chevron-left'></i></button>
                <div class="product-cards">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1618384887929-16ec33fab9ef" alt="Microphone">
                            <button class="wishlist-btn"><i class='bx bx-heart'></i></button>
                        </div>
                        <div class="product-details">
                            <div class="rating">
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                            </div>
                            <h3>Microphone</h3>
                            <div class="product-price-details">
                                <span>₱ 2,300.00</span>
                                <a href="#" class="view-details">View Details</a>
                            </div>
                            <button class="add-cart-btn">Add to Cart</button>
                        </div>
                    </div>
                    <!-- Repeat similar product cards for Mouse, Headset, and Monitor -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7" alt="Mouse">
                            <button class="wishlist-btn"><i class='bx bx-heart'></i></button>
                        </div>
                        <div class="product-details">
                            <div class="rating">
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                            </div>
                            <h3>Mouse</h3>
                            <div class="product-price-details">
                                <span>₱ 1,300.00</span>
                                <a href="#" class="view-details">View Details</a>
                            </div>
                            <button class="add-cart-btn">Add to Cart</button>
                        </div>
                    </div>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb" alt="Headset">
                            <button class="wishlist-btn"><i class='bx bx-heart'></i></button>
                        </div>
                        <div class="product-details">
                            <div class="rating">
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                            </div>
                            <h3>Headset</h3>
                            <div class="product-price-details">
                                <span>₱ 2,100.00</span>
                                <a href="#" class="view-details">View Details</a>
                            </div>
                            <button class="add-cart-btn">Add to Cart</button>
                        </div>
                    </div>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1527443224154-c4a3942d3acf" alt="Monitor">
                            <button class="wishlist-btn"><i class='bx bx-heart'></i></button>
                        </div>
                        <div class="product-details">
                            <div class="rating">
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                                <i class='bx bxs-star'></i>
                            </div>
                            <h3>Monitor</h3>
                            <div class="product-price-details">
                                <span>₱ 2,300.00</span>
                                <a href="#" class="view-details">View Details</a>
                            </div>
                            <button class="add-cart-btn">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <button class="slider-btn next"><i class='bx bx-chevron-right'></i></button>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>DJM TECH PC STORE</h3>
                <p>Lorem ipsum dolor sit amet consectetur. Enim magna nulla adipiscing purus.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="#">Home</a>
                <a href="#">About Us</a>
                <a href="#">Contact Us</a>
            </div>
            <div class="footer-section">
                <h4>Menu</h4>
                <a href="#">Home</a>
                <a href="#">Product</a>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="social-links">
                <a href="#"><i class='bx bxl-facebook'></i></a>
                <a href="#"><i class='bx bxl-instagram'></i></a>
                <a href="#"><i class='bx bxl-twitter'></i></a>
            </div>
            <p class="copyright">© 2025 By Lintech Inc. All Rights Reserved.</p>
            <div class="legal-links">
                <a href="#">Terms of Use</a>
                <a href="#">Privacy Policy</a>
            </div>
        </div>
    </footer>
    <script src="../js/cart.js"></script>
</body>
</html>