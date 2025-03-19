// liveSearch
function liveSearch() {
  const query = document.getElementById("search-input").value.toLowerCase().trim();
  const productCards = document.querySelectorAll(".product-card");
  let visibleProducts = 0;

  productCards.forEach((card) => {
    const productName = card.querySelector(".product-name").textContent.toLowerCase();

    if (query === "") {
      
      card.style.display = ""; 
      visibleProducts++;
    } else if (productName.startsWith(query)) {
      card.style.display = ""; 
      visibleProducts++;
    } else {
      card.style.display = "none"; 
    }
  });

  // Update the product count display
  document.querySelector("#product-header h4").textContent = `${visibleProducts} Products Found`;
}


// price filter
document.addEventListener('DOMContentLoaded', function() {
  function livePriceFilter() {
      const minPrice = parseFloat(document.getElementById('min-price').value) || 0;
      const maxPrice = parseFloat(document.getElementById('max-price').value) || Infinity;

      const productCards = document.querySelectorAll('.product-card'); 
      let visibleProducts = 0; 

      productCards.forEach((card) => {
          const productPrice = parseFloat(card.querySelector('.product-price').textContent.replace(/[₱,]/g, '').trim());

         
          if (productPrice >= minPrice && productPrice <= maxPrice) {
              card.style.display = "";
              visibleProducts++;
          } else {
              card.style.display = "none";
          }
      });
      document.getElementById("product-header").querySelector("h4").textContent = `${visibleProducts} Products Found`;
      liveSearch();
  }
  const minPriceInput = document.getElementById('min-price');
  const maxPriceInput = document.getElementById('max-price');

  if (minPriceInput && maxPriceInput) {
      minPriceInput.addEventListener('input', livePriceFilter);
      maxPriceInput.addEventListener('input', livePriceFilter);
  }
  livePriceFilter();
});
document.getElementById('search-input').addEventListener('input', liveSearch);







/*
//livesearch
function liveSearch() {
  const query = document.getElementById("search-input").value.toLowerCase().trim();
  const productCards = document.querySelectorAll(".product-card");
  let visibleProducts = 0;

  productCards.forEach((card) => {
    const productName = card.querySelector(".product-name").textContent.toLowerCase(); 
    if (productName.startsWith(query)) {
      card.style.display = ""; 
      visibleProducts++;
    } else {
      card.style.display = "none"; 
    }
  });
  document.querySelector("#product-header h4").textContent = `${visibleProducts} Products Found`;
}
//search end 

//price filter
document.addEventListener('DOMContentLoaded', function() {
  // Function to filter products based on the selected price range
  function livePriceFilter() {
      const minPrice = parseFloat(document.getElementById('min-price').value) || 0;
        const maxPrice = parseFloat(document.getElementById('max-price').value) || Infinity;

        const productCards = document.querySelectorAll('.product-card'); 
        let visibleProducts = 0; 

        productCards.forEach((card) => {
            const productPrice = parseFloat(card.querySelector('.product-price').textContent.replace(/[₱,]/g, '').trim());

            console.log('Product Price:', productPrice);

            if (productPrice >= minPrice && productPrice <= maxPrice) {
                card.style.display = "";
                visibleProducts++;
            } else {
                card.style.display = "none";
            }
        });
        document.getElementById("product-header").querySelector("h4").textContent = `${visibleProducts} Products Found`;
    }
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');

    if (minPriceInput && maxPriceInput) {
        minPriceInput.addEventListener('input', livePriceFilter);
        maxPriceInput.addEventListener('input', livePriceFilter);
    }
    livePriceFilter();
  });
  //price filter end 

  */



