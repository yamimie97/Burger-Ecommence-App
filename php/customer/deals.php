<?php
include 'header.php'; // Include header
include 'db.php';     // Database connection

// Check if customer is logged in
$isLoggedIn = isset($_SESSION['customerId']);

// SQL query to fetch promotions with respective menu items and discount
$sql = "
    SELECT p.promotionId, p.promotionName, p.promotionImage, pi.promoDescription, p.discount,
           pi.menuitemId, mi.itemName, mi.itemImage, mi.itemPrice, mi.description
    FROM promotion p
    JOIN promotionitem pi ON p.promotionId = pi.promotionId
    JOIN menuitem mi ON pi.menuitemId = mi.menuitemId
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deals</title>
    <link rel="stylesheet" href="css/deals_style.css"> <!-- Assuming this exists -->
</head>
<body>

<div class="deals-container">
    <?php
        if ($result->num_rows > 0) {
            // Output data of each promotion and menu item
            $currentPromotion = null;
            while($row = $result->fetch_assoc()) {
                if ($row['promotionId'] !== $currentPromotion) {
                    // Close the previous promotion card
                    if ($currentPromotion !== null) {
                        echo '</div></div>';
                    }

                    // Start a new promotion card
                    echo '<div class="deal-card">';
                    echo '<img src="../../image/promo/' . $row['promotionImage'] . '" alt="Promotion Image">';
                    echo '<div class="promotion-title">' . $row['promotionName'] . '</div>';
                    echo '<div class="promotion-description">' . $row['promoDescription'] . '</div>';
                    echo '<div class="menu-items">';
                    $currentPromotion = $row['promotionId'];
                }

                // Calculate discounted price
                $discountedPrice = $row['itemPrice'] - ($row['itemPrice'] * $row['discount'] / 100);

                // Output menu items horizontally
                echo '<div class="menu-item" data-id="'.$row['menuitemId'].'">';
                    echo '<img src="' . $row['itemImage'] . '" alt="Menu Item Image">';
                    echo '<p><strong>' . $row['itemName'] . '</strong></p>';
                    echo '<p class="original-price">Price: $' . number_format($row['itemPrice'], 2) . '</p>';
                    echo '<p class="discounted-price">Discounted Price: $' . number_format($discountedPrice, 2) . '</p>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<button class="add-to-cart-btn">Add to Cart</button>'; // Add to Cart button
                echo '</div>';
            }

            // Close the last promotion card
            if ($currentPromotion !== null) {
                echo '</div></div>';
            }
        } else {
            echo "No promotions found.";
        }
    ?>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    const menuItems = document.querySelectorAll(".menu-item");

    menuItems.forEach(item => {
      const addToCartBtn = item.querySelector(".add-to-cart-btn");

      addToCartBtn.addEventListener("click", function(event) {
        event.stopPropagation(); 
        if (!isLoggedIn) {
          // If the customer is not logged in, redirect to sign in
          window.location.href = 'signin.php';
          return;
        }
        const itemId = item.getAttribute("data-id"); 
        window.location.href = `cart.php?itemId=${itemId}`;
      });

    });
  });
</script>

</body>
</html>

<?php
include 'footer.php'; // Include footer
$conn->close();
?>
