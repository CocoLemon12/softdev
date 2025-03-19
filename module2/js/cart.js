document.addEventListener('DOMContentLoaded', async function () {
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartCount = document.querySelector('.cart-count');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const checkoutBtn = document.querySelector('.checkout-btn');

    // Fetch Cart Items from Database
    async function fetchCart() {
        try {
            const response = await fetch('../php/fetch_cart.php'); 
            const cart = await response.json();
            updateCartDisplay(cart);
        } catch (error) {
            console.error("Error fetching cart:", error);
        }
    }

    // Update Cart UI
    function updateCartDisplay(cart) {
        cartItemsContainer.innerHTML = '';
    
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `<p class="empty-cart">Your cart is empty. Start shopping now!</p>`;
            subtotalElement.textContent = '₱ 0.00';
            totalElement.textContent = '₱ 0.00';
        } else {
            let total = 0;
    
            cart.forEach((item) => {
                const cartItem = document.createElement('div');
                cartItem.classList.add('cart-item');
                cartItem.dataset.cartId = item.cart_id; // ✅ Ensure cart_id is assigned
                cartItem.dataset.productId = item.product_id;
                total += item.price * item.quantity;
    
                cartItem.innerHTML = `
                    <button class="remove-btn"><i class='bx bx-x'></i></button>
                    <img src="${item.image}" alt="${item.product_name}" loading="lazy">
                    <div class="item-info">
                        <h3>${item.product_name}</h3>
                        <button class="show-details">Show details <i class='bx bx-chevron-down'></i></button>
                    </div>
                    <div class="quantity-controls">
                        <button class="qty-btn minus">-</button>
                        <span class="qty">${item.quantity}</span>
                        <button class="qty-btn plus">+</button>
                    </div>
                    <p class="price">₱ ${item.price.toLocaleString('en-PH')}</p>
                `;
    
                cartItemsContainer.appendChild(cartItem);
            });
    
            subtotalElement.textContent = `₱ ${total.toLocaleString('en-PH')}`;
            totalElement.textContent = `₱ ${total.toLocaleString('en-PH')}`;
            cartCount.textContent = cart.length;
        }
    }
    

    // Add to Cart
    async function addToCart(productId) {
        try {
            const response = await fetch('/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            });

            const result = await response.json();
            if (result.success) {
                fetchCart();
            } else {
                console.error("Failed to add item:", result.message);
            }
        } catch (error) {
            console.error("Error adding to cart:", error);
        }
    }

    // Update Cart Quantity
    async function updateCartQuantity(cartId, newQuantity) {
        try {
            const response = await fetch('../php/update_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId, quantity: newQuantity })
            });

            const result = await response.json();
            if (result.success) {
                fetchCart();
            } else {
                console.error("Failed to update quantity:", result.message);
            }
        } catch (error) {
            console.error("Error updating cart:", error);
        }
    }

    // Remove from Cart
    async function removeFromCart(cartId) {
        try {
            console.log("Attempting to remove item. Cart ID:", cartId); // Debugging log
    
            if (!cartId) {
                console.error("Cart ID is missing before sending request.");
                return;
            }
    
            const response = await fetch('../php/remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId }) // Ensure it's named correctly
            });
    
            const result = await response.json();
            console.log("Server Response:", result); // Debugging response
    
            if (result.success) {
                fetchCart(); // Refresh cart
            } else {
                console.error("Failed to remove item:", result.message);
            }
        } catch (error) {
            console.error("Error removing item:", error);
        }
    }
    
    
    // Handle Cart Interactions
    cartItemsContainer.addEventListener('click', async function (e) {
        const target = e.target;
        const cartItem = target.closest('.cart-item');
        if (!cartItem) return;
    
        const cartId = cartItem.dataset.cartId; // ✅ Now fetching correctly
    
        console.log("Clicked remove. Cart ID:", cartId); // Debugging log
    
        if (target.closest('.remove-btn')) {
            await removeFromCart(cartId);
        }
    
        if (target.classList.contains('qty-btn')) {
            const qtyElement = cartItem.querySelector('.qty');
            let newQuantity = parseInt(qtyElement.textContent);
    
            if (target.classList.contains('minus')) {
                newQuantity = Math.max(1, newQuantity - 1);
            } else if (target.classList.contains('plus')) {
                newQuantity++;
            }
    
            await updateCartQuantity(cartId, newQuantity);
        }
    });
    

    // Add Product to Cart from Product List
    document.querySelector('.product-slider').addEventListener('click', async (e) => {
        if (e.target.closest('.add-cart-btn')) {
            const productCard = e.target.closest('.product-card');
            const productId = productCard.dataset.productId;
            await addToCart(productId);
        }
    });

    // Checkout Button
    checkoutBtn.addEventListener('click', () => {
        window.location.href = 'checkout.html';
    });
    async function updateCartCount() {
        try {
            const response = await fetch('../php/fetch_cart_count.php');
            const data = await response.json();
    
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement && data.cart_count !== undefined) {
                cartCountElement.textContent = data.cart_count;
            } else {
                console.error('Cart count element not found or invalid response:', data);
            }
        } catch (error) {
            console.error('Error fetching cart count:', error);
        }
    }
    

    // Initial Fetch of Cart Items
    fetchCart();
    updateCartCount();
    updateCartQuantity();
    removeFromCart(cartId);

});
