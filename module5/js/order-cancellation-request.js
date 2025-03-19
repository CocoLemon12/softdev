document.addEventListener('DOMContentLoaded', function () {
    addEventListenersToButtons();
});

function addEventListenersToButtons() {
    const viewDetailsButtons = document.querySelectorAll('.view-details-button');
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id');
            window.location.href = `order-details.php?order_id=${orderId}`;
        });
    });

    const actionButtons = document.querySelectorAll('.order-action-button:not(.view-details-button)');
    actionButtons.forEach(button => {
        button.addEventListener('click', function () {
            const status = this.getAttribute('data-status');
            const orderId = this.closest('form').querySelector('input[name="order_id"]').value;
            handleButtonClick(status, orderId);
        });
    });
}

function handleButtonClick(status, orderId) {
    switch (status) {
        case 'Processing':
            redirectToCancellationReq(orderId);
            break;
        case 'Shipped':
            redirectToOrderComplete(orderId);
            break;
        case 'Complete':
            redirectToReturnReq(orderId);
            break;
        default:
            location.reload();
    }
}

function redirectToCancellationReq(orderId) {
    console.log('Redirecting to cancellation request with order ID:', orderId);
    window.location.href = `order-cancellation-request.php?order_id=${orderId}`;
}

function redirectToOrderComplete(orderId) {
    console.log('Redirecting to order complete with order ID:', orderId);
    window.location.href = `order-complete.php?action=complete&order_id=${orderId}`;
}

function redirectToReturnReq(orderId) {
    console.log('Redirecting to return request with order ID:', orderId);
    window.location.href = `order-return-request.php?order_id=${orderId}`;
}

function openPopup() {
    document.getElementById("popup-form").style.display = "block";
}

function closePopup() {
    document.getElementById("popup-form").style.display = "none";
}

function submitReason() {
    const selectedReason = document.querySelector('input[name="cancel-reason"]:checked');
    if (selectedReason) {
        document.getElementById("selected-reason").textContent = selectedReason.nextElementSibling.textContent;
        closePopup();
    } else {
        console.error("Please select a reason.");
    }
}

document.getElementById("cancellation-form").addEventListener("submit", function(event) {
    event.preventDefault();
    const orderId = document.getElementById("order-id").value;
    const reason = document.getElementById("selected-reason").textContent;
    const description = document.getElementById("detailed-reason").value;

    if (reason === "Please Select") {
        console.error("Please select a cancellation reason.");
        return;
    }

    if (!description.trim()) {
        console.error("Please provide a detailed explanation.");
        return;
    }

    const formData = new FormData();
    formData.append("order_id", orderId);
    formData.append("reason", reason);
    formData.append("description", description);

    fetch("php/create-cancellation-request.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
            window.location.href = "orders.php?status=Cancelled";
        } else {
            console.error(data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
});