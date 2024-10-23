<?php
    include 'header.php'; // Include header
    include 'db.php';     // Database connection

    // Check if customer is logged in
    $isLoggedIn = isset($_SESSION['customerId']);
    // Get menuId, groupId, and search term from query parameters if available
    $menuId = isset($_GET['menuId']) ? $_GET['menuId'] : '';
    $menuGroupId = isset($_GET['menuGroupId']) ? $_GET['menuGroupId'] : '';
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

    // Get all available menus for the category filter
    $menu_query = "SELECT * FROM menu";
    $menu_result = $conn->query($menu_query);

    // Modify the query to handle search functionality
    $items_query = "SELECT * FROM menuitem";
    if (!empty($searchTerm)) {
        // If a search term is provided, find matching menu items
        $items_query = "SELECT * FROM menuitem WHERE itemName LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";
    } elseif (!empty($menuId)) {
        // Get menu groups for the selected menu
        $group_query = "SELECT * FROM menugroup WHERE menuId = '$menuId'";
        $group_result = $conn->query($group_query);

        // If a group is selected, filter the menu items by both menu and group
        if (!empty($menuGroupId)) {
            $items_query = "SELECT * FROM menuitem WHERE menuGroupId = '$menuGroupId'";
        } else {
            // If no group is selected, show all items under the selected menu
            $items_query = "SELECT menuitem.* 
                            FROM menuitem 
                            JOIN menugroup ON menuitem.menuGroupId = menugroup.menuGroupId
                            WHERE menugroup.menuId = '$menuId'";
        }
    }
    $items_result = $conn->query($items_query);
?>

<div class="container">
    <h2 class="text-center">Our Menu</h2>
    <!-- Search Form -->
    <form action="menu.php" method="GET" class="search-form text-center">
        <input type="text" name="search" placeholder="Search menu items..." class="search-bar" value="<?php echo htmlspecialchars($searchTerm); ?>" />
        <button type="submit" class="search-button">Search</button>
    </form>

    <!-- Display the Menu Categories in center -->
    <ul class="menu-categories text-center">
        <li><a href="menu.php" class="<?php echo empty($menuId) ? 'active' : ''; ?>">All</a></li> <!-- "All" option -->
        <?php
        if ($menu_result->num_rows > 0) {
            while($menu_row = $menu_result->fetch_assoc()) {
                $active = ($menuId == $menu_row['menuid']) ? 'class="active"' : '';
                echo '<li><a href="menu.php?menuId='.$menu_row['menuid'].'" '.$active.'>'.$menu_row['menuName'].'</a></li>';
            }
        }
        ?>
    </ul>

    <hr>

    <?php if (!empty($menuId)): ?>
        <!-- Display the Menu Groups if a menu is selected -->
        <div class="menu-groups text-center">
            <ul class="group-categories">
                <li><a href="menu.php?menuId=<?php echo $menuId; ?>" class="<?php echo empty($menuGroupId) ? 'active' : ''; ?>">All</a></li> <!-- "All" for menu -->
                <?php
                if ($group_result->num_rows > 0) {
                    while($group_row = $group_result->fetch_assoc()) {
                        $active = ($menuGroupId == $group_row['menuGroupId']) ? 'class="active"' : '';
                        echo '<li><a href="menu.php?menuId='.$menuId.'&menuGroupId='.$group_row['menuGroupId'].'" '.$active.'>'.$group_row['groupName'].'</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Display the Menu Items -->
    <div class="menu-items text-center">
        <?php
        if ($items_result->num_rows > 0) {
            while($item = $items_result->fetch_assoc()) {
                echo '<div class="menu-item" data-id="'.$item['menuitemId'].'" data-description="'.$item['description'].'">';
                echo '<img src="'.$item['itemImage'].'" alt="'.$item['itemName'].'">';
                echo '<h4>'.$item['itemName'].'</h4>';
                echo '<span>Price: $'.$item['itemPrice'].'</span>';
                echo '<button class="add-to-cart-btn">Add to Cart</button>';
                echo '</div>';
            }
        } else {
            echo '<p>No items found.</p>';
        }
        ?>
    </div>
    
    <!-- Pop-up for showing menu item details -->
    <div class="popup-overlay"></div>
    <div class="menu-popup">
        <div class="popup-content">
            <img src="" alt="Menu Item Image" class="popup-image">
            <div class="popup-details">
                <h4>Item Name</h4>
                <span>$0.00</span>
                <p>Description of the item will go here.</p>
                <button class="add-to-cart-btn">Add to Cart</button>
                <button class="close-btn">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    const menuItems = document.querySelectorAll(".menu-item");
    const popupOverlay = document.querySelector(".popup-overlay");
    const menuPopup = document.querySelector(".menu-popup");
    const closeBtn = document.querySelector(".close-btn");

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

      item.addEventListener("click", function() {
        const itemName = this.querySelector("h4").textContent;
        const itemPrice = this.querySelector("span").textContent;
        const itemImg = this.querySelector("img").src;
        const itemDesc = this.getAttribute("data-description");

        // Set the content of the popup
        menuPopup.querySelector("h4").textContent = itemName;
        menuPopup.querySelector("span").textContent = itemPrice;
        menuPopup.querySelector("img").src = itemImg;
        menuPopup.querySelector("p").textContent = itemDesc;

        // Show the popup and overlay
        popupOverlay.style.display = "block";
        menuPopup.style.display = "block";
      });
    });

    // Handle Add to Cart button in the pop-up
    const popupAddToCartBtn = menuPopup.querySelector(".add-to-cart-btn");
    popupAddToCartBtn.addEventListener("click", function() {
        if (!isLoggedIn) {
            window.location.href = 'signin.php';
            return;
        }
        const itemName = menuPopup.querySelector("h4").textContent;
        const itemId = [...menuItems].find(item => item.querySelector("h4").textContent === itemName).getAttribute("data-id");
        window.location.href = `cart.php?itemId=${itemId}`;
    });

    closeBtn.addEventListener("click", function() {
      popupOverlay.style.display = "none";
      menuPopup.style.display = "none";
    });

    // Hide pop-up when clicking outside of it
    popupOverlay.addEventListener("click", function() {
      popupOverlay.style.display = "none";
      menuPopup.style.display = "none";
    });
  });
</script>

<?php
include 'footer.php'; // Include footer
$conn->close();
?>
