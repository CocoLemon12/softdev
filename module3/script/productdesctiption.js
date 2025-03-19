//increment decrement on how many product
document.querySelector(".btn-minus").addEventListener("click", function() {
    const counter = document.querySelector(".counter p span");
    let currentValue = parseInt(counter.textContent);
  
    if (currentValue > 1) {
        counter.textContent = currentValue - 1;
    }
  });
  // change image of product
  document.querySelector(".btn-plus").addEventListener("click", function() {
    const counter = document.querySelector(".counter p span");
    let currentValue = parseInt(counter.textContent);
    counter.textContent = currentValue + 1;
  });

  document.querySelectorAll('.thumbnail').forEach(item => {
    item.addEventListener('click', event => {
      document.getElementById('main-image').src = event.target.src;
    });
  });

  /*
  <form action='productdescrption.php' method='POST'>
  <input type='hidden' name='product_id' value='" . $row['product_id'] . "'>
  <div class='product-name'>" . $row['product_name'] . "</div>
</form>
*/

/*$sql = "SELECT * FROM products";
                $result = $conn->query($sql); */