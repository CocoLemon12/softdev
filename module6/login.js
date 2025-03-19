document.querySelector("form").addEventListener("submit", async function (e) {
    e.preventDefault();

    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    try {
        let response = await fetch("login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password })
        });

        let data = await response.json(); // Try parsing JSON

        if (data.success) {
            window.location.href = "dashboard.html"; // Redirect on success
        } else {
            alert(data.message); // Show error message
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Something went wrong. Check your connection.");
    }
});
