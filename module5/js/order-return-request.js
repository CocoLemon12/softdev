document.addEventListener('DOMContentLoaded', function () {
    document.getElementById("popup-form").style.display = "none";
    addEventListenersToElements();
});

function addEventListenersToElements() {
    const fileUploadElement = document.getElementById("fileUpload");
    if (fileUploadElement) {
        fileUploadElement.addEventListener('change', handleFileUpload);
    }

    const form = document.getElementById('return-request-form');
    if (form) {
        form.addEventListener('submit', handleSubmit);
    }
}

function handleFileUpload() {
    const file = this.files[0];
    const fileName = file.name;
    const uploadStatus = document.getElementById("upload-status");
    const uploadContent = document.querySelector(".upload-content");

    uploadStatus.textContent = `Uploaded: ${fileName}`;
    uploadContent.innerHTML = '';

    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = fileName;
        img.style.maxWidth = '100%';
        uploadContent.appendChild(img);
    } else if (file.type.startsWith('video/')) {
        const video = document.createElement('video');
        video.src = URL.createObjectURL(file);
        video.controls = true;
        video.style.maxWidth = '100%';
        uploadContent.appendChild(video);
    }

    const replaceButton = document.createElement('button');
    replaceButton.textContent = 'Replace Media';
    replaceButton.classList.add('replace-button');
    replaceButton.addEventListener('click', () => fileUploadElement.click());
    uploadContent.appendChild(replaceButton);
}

function handleSubmit(event) {
    event.preventDefault();
    const orderId = document.getElementById("order-id").value; // Ensure order ID is fetched from the hidden input
    const reason = document.getElementById("selected-reason").textContent;
    const description = document.getElementById("detailed-reason").value;
    const fileUpload = document.getElementById("fileUpload");

    if (!fileUpload || !fileUpload.files || fileUpload.files.length === 0) {
        alert("Please upload a photo or video as proof.");
        return;
    }

    const file = fileUpload.files[0];

    if (reason === "Please Select") {
        alert("Please select a return reason.");
        return;
    }

    if (!description.trim()) {
        alert("Please provide a detailed explanation.");
        return;
    }

    const formData = new FormData();
    formData.append("order_id", orderId); // Append order ID to form data
    formData.append("fileUpload", file);

    fetch("../module5/php/upload-media.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const mediaContent = data.media_content;
            const returnId = data.return_id;
            submitReturnRequest(orderId, reason, description, mediaContent, returnId);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert("Error: " + error);
    });
}

function submitReturnRequest(orderId, reason, description, mediaContent, returnId) {
    const formData = new FormData();
    formData.append("order_id", orderId);
    formData.append("reason", reason);
    formData.append("description", description);
    formData.append("media_content", mediaContent);
    formData.append("return_id", returnId);

    fetch("../module5/php/create-return-request.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = "orders.php?status=Return";
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert("Error: " + error);
    });
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
        alert("Please select a reason.");
    }
}
