<?php
  include 'homepage.php'; // Include header
  include 'db.php';     // Database connection

  // Get menuId, groupId from query parameters if available
  $menuId = isset($_GET['menuId']) ? $_GET['menuId'] : '';

  // Get all available menus for the category filter
  $menu_query = "SELECT * FROM menu";
  $menu_result = $conn->query($menu_query);

  // Modify the query to show a maximum of 9 menu items under the selected menu or all items
  $items_query = "SELECT * FROM menuitem LIMIT 9";
  if (!empty($menuId)) {
      // Show a maximum of 9 items under the selected menu
      $items_query = "SELECT menuitem.* 
                      FROM menuitem 
                      JOIN menugroup ON menuitem.menuGroupId = menugroup.menuGroupId
                      WHERE menugroup.menuId = '$menuId'
                      LIMIT 9";
  }
  $items_result = $conn->query($items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Content</title>
  <link href="css/offer_style.css" rel="stylesheet" />
</head>
<body>

<?php
  $isLoggedIn = isset($_SESSION['customerId']); // Check if customer is logged in
?>

  <!-- Main Content Start -->
  <div class="main_content">
    <section class="offer_section layout_padding-bottom">
      <h2 class="text-center">Special Offers</h2> <!-- Add your title here -->
      <div class="offer_container">
        <div class="container">
          <div class="row">
            <?php
            include 'db.php'; // Database connection

            // Fetch a limited number of menu items (for example, 2 items for the offer section)
            $offer_query = "
                SELECT p.promotionName, p.promotionImage, p.discount, 
                      mi.itemName, mi.itemImage, mi.itemPrice, pi.menuitemId
                FROM promotion p
                JOIN promotionitem pi ON p.promotionId = pi.promotionId
                JOIN menuitem mi ON pi.menuitemId = mi.menuitemId
                LIMIT 2";
            $offer_result = $conn->query($offer_query);

            // Check if there are any offers
            if ($offer_result->num_rows > 0) {
                // Loop through and display the limited offers
                while($row = $offer_result->fetch_assoc()) {
                    // Calculate discounted price
                    $discountedPrice = $row['itemPrice'] - ($row['itemPrice'] * $row['discount'] / 100);
                    ?>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="img-box">
                                <img src="../../image/menu/<?php echo $row['itemImage']; ?>" alt="<?php echo $row['itemName']; ?>">
                            </div>
                            <div class="detail-box">
                                <h5><?php echo $row['promotionName']; ?></h5>
                                <h6><span><?php echo $row['discount']; ?>%</span> Off</h6>
                                <p><strong><?php echo $row['itemName']; ?></strong></p>
                                <p><del>Original Price: $<?php echo number_format($row['itemPrice'], 2); ?></del></p>
                                <p><b>Discounted Price: $<?php echo number_format($discountedPrice, 2); ?></b></p>
                                <!-- "Order Now" Button -->
                                <a href="<?php echo $isLoggedIn ? 'cart.php?itemId=' . $row['menuitemId'] : 'signin.php'; ?>">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No special offers available right now.</p>";
            }
            $conn->close();
            ?>
          </div>
        </div>
      </div>
  </section>
  <section class="menu-section">
    <div class="container">
        <h2 class="text-center">Our Menu</h2>
        <!-- Display the Menu Items -->
        <div class="menu-items text-center">
            <?php
            if ($items_result->num_rows > 0) {
                while($item = $items_result->fetch_assoc()) {
                    echo '<div class="menu-item" data-id="'.$item['menuitemId'].'">';
                    echo '<img src="'.$item['itemImage'].'" alt="'.$item['itemName'].'">';
                    echo '<h4>'.$item['itemName'].'</h4>';
                    echo '<span>Price: $'.$item['itemPrice'].'</span>';
                    echo '</div>';
                }
            } else {
                echo '<p>No items found.</p>';
            }
            ?>
        </div>

        <!-- View More Button -->
        <div class="text-center">
            <a href="menu.php" class="btn btn-primary">View More</a>
        </div>
    </div>
</section>
  </div>
  <!-- Main Content End -->
</body>
</html>

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
<?php
include 'footer.php'; // Include footer
?>
