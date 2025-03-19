document.addEventListener('DOMContentLoaded', function () {
    addEventListenersToButtons();
});

function addEventListenersToButtons() {
    // View Details Button
    addButtonEventListener(".view-details-button", "order-details.php", "order_id");

    // Buy Again Button
    addButtonEventListener(".buy-again-button", "../module3/productdescription.php", "product_id");

    // Return Order Button
    addButtonEventListener(".return-order-button", "order-return-request.php", "order_id", "Are you sure you want to return this order?");

    // Cancel Order Button
    addButtonEventListener(".cancel-button", "cancel-order.php", "order_id", "Are you sure you want to cancel this order?");

    // Receive Order Button
    addButtonEventListener(".receive-order-button", "order-complete.php", "order_id", "Mark this order as received?");
}

function addButtonEventListener(selector, action, inputName, confirmMessage = null) {
    document.querySelectorAll(selector).forEach(button => {
        button.addEventListener("click", function () {
            const inputValue = this.getAttribute(`data-${inputName}`);
            if (!confirmMessage || confirm(confirmMessage)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputName;
                input.value = inputValue;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
}
