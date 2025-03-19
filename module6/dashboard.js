document.addEventListener("DOMContentLoaded", function () {
    fetch("getCategorySales.php")
        .then(response => response.json())
        .then(data => {
            console.log("Category Sales Trends Data:", data);
            renderChart(data); // Call chart function
        })
        .catch(error => console.error("Error fetching category sales trends:", error));
});

function renderChart(data) {
    const ctx = document.getElementById("categorySalesChart").getContext("2d");

    const months = data.months; // ["2025-01", "2025-02", ..., "2025-12"]
    const salesData = data.sales;

    // Convert category sales into dataset format for Chart.js
    const datasets = Object.keys(salesData).map(category => {
        return {
            label: category,
            data: months.map(month => salesData[category][month] || 0),
            borderColor: getRandomColor(),
            fill: false,
            tension: 0.1 // Smooth line
        };
    });

    // Create the Line Chart
    new Chart(ctx, {
        type: "line",
        data: {
            labels: months, // X-axis (months)
            datasets: datasets // Y-axis (sales per category)
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Category Sales Trends (Monthly)"
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: "Months"
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Total Sales"
                    }
                }
            }
        }
    });
}

// Generate Random Colors for Each Category
function getRandomColor() {
    return `hsl(${Math.floor(Math.random() * 360)}, 70%, 50%)`;
}