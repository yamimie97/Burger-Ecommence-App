<?php
include 'header.php';
include 'db.php'; // Database connection

// Function to fetch promotion details for an item
function getPromotionForItem($menuitemId, $conn) {
    $promo_query = "
        SELECT p.promotionName, p.discount
        FROM promotionItem pi
        JOIN promotion p ON pi.promotionId = p.promotionId
        WHERE pi.menuitemId = ?";
    
    $stmt = $conn->prepare($promo_query);
    $stmt->bind_param("s", $menuitemId);
    $stmt->execute();
    $promo_result = $stmt->get_result();
    
    if ($promo_result->num_rows > 0) {
        return $promo_result->fetch_assoc(); // Return the promotion details
    }
    return null; // No promotion for this item
}

// Process adding an item to the cart
if (isset($_GET['itemId']) && !isset($_GET['update']) && !isset($_GET['remove'])) {
    $itemId = $_GET['itemId'];

    // Query to get the details of the selected item
    $item_query = "SELECT menuitemId, itemName, itemPrice, itemImage FROM menuitem WHERE menuitemId = ?";
    $stmt = $conn->prepare($item_query);
    $stmt->bind_param("s", $itemId); 
    $stmt->execute();
    $item_result = $stmt->get_result();

    if ($item_result->num_rows > 0) {
        $item = $item_result->fetch_assoc();

        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if item already exists in the cart
        $itemExists = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['menuitemId'] == $item['menuitemId']) {
                $cartItem['quantity'] += 1; // Increment quantity if item exists
                $itemExists = true;
                break;
            }
        }

        // Add the item if it does not already exist
        if (!$itemExists) {
            $_SESSION['cart'][] = [
                'menuitemId' => $item['menuitemId'],
                'itemName' => $item['itemName'],
                'itemPrice' => $item['itemPrice'],
                'itemImage' => $item['itemImage'],
                'quantity' => 1
            ];
        }

        // Redirect to cart after adding item
        header('Location: menu.php'); // Use header() before any output
        exit();
    } else {
        echo "Item not found! <a href='menu.php'>Go back to menu</a>";
    }
    $stmt->close();
}

// Update the quantity (increase or decrease)
if (isset($_GET['update']) && isset($_GET['itemId'])) {
    $itemId = $_GET['itemId'];
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['menuitemId'] == $itemId) {
            if ($_GET['update'] == 'increase') {
                $cartItem['quantity'] += 1;
            } elseif ($_GET['update'] == 'decrease') {
                $cartItem['quantity'] -= 1;
                if ($cartItem['quantity'] <= 0) {
                    // Remove item from the cart if quantity is zero
                    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($itemId) {
                        return $item['menuitemId'] !== $itemId;
                    });
                }
            }
            break;
        }
    }
    header('Location: cart.php'); 
    exit();
}

// Remove an item from the cart
if (isset($_GET['remove']) && isset($_GET['itemId'])) {
    $itemId = $_GET['itemId'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($itemId) {
        return $item['menuitemId'] !== $itemId;
    });
    header('Location: cart.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
</head>
<body>

<?php
// Display shopping cart with columns and total price
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "
    <nav>
        <ul class='steps'>
            <li class='current-step'><h2>1. Shopping Cart</h2></li>
            <li><h2>2. Address & Payment</h2></li>
            <li><h2>3. Review Order</h2></li>
        </ul>
    </nav>";

    // Start of cart container
    echo "<div class='cart-container'>";

    // Cart table on the left
    echo "<div class='cart-table'>
            <table border='1'>
            <tr>
                <th>Item Image</th>
                <th>Item Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Discount</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>";

    // Loop through the cart items and display
    $total = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
        $subtotal = $cartItem['itemPrice'] * $cartItem['quantity']; // Calculate subtotal

        // Check if the item has a promotion
        $promotion = getPromotionForItem($cartItem['menuitemId'], $conn);
        $discount = 0;

        if ($promotion) {
            $discount = $cartItem['itemPrice'] * ($promotion['discount'] / 100); // Calculate discount
            $subtotal -= $discount * $cartItem['quantity']; // Apply discount to subtotal
        }

        $total += $subtotal; // Add to total price

        echo "<tr>
                <td><img src='" . htmlspecialchars($cartItem['itemImage']) . "' width='100' /></td>
                <td>" . htmlspecialchars($cartItem['itemName']) . "</td>
                <td>$" . htmlspecialchars($cartItem['itemPrice']) . "</td>
                <td>
                    <a href='cart.php?itemId=" . $cartItem['menuitemId'] . "&update=decrease'>-</a>
                    " . htmlspecialchars($cartItem['quantity']) . "
                    <a href='cart.php?itemId=" . $cartItem['menuitemId'] . "&update=increase'>+</a>
                </td>
                <td>" . ($promotion ? htmlspecialchars($promotion['discount']) . "% off" : 'No discount') . "</td>
                <td>$" . htmlspecialchars(number_format($subtotal, 2)) . "</td>
                <td><a href='cart.php?itemId=" . $cartItem['menuitemId'] . "&remove=true' onclick='return confirm(\"Are you sure you want to remove this item from the cart?\")'>Remove</a></td>
              </tr>";
    }

    echo "</table>
          </div>"; // Close the table container

    // Cart summary box on the right
    $tax = $total * 0.05;
    $grandTotal = $total + $tax;

    echo "<div class='cart-summary'>
            <h2>Summary</h2>
            <p>Subtotal: $" . number_format($total, 2) . "</p>
            <p>Tax (5%): $" . number_format($tax, 2) . "</p>
            <p>Total Price: $" . number_format($grandTotal, 2) . "</p>
            <a href='checkout.php'>Proceed to Checkout</a>
            <a href='menu.php'>Continue Shopping</a>
          </div>";
    echo "</div>"; 

} else {
    echo "<div class='center-content'>
         <style>/* Center the content if no cart item */
            .center-content {
            text-align: center;
            padding: 20px;
            }

            /* Style the button */
            .center-content .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #37B7C3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            }

            /* Change button background color to teal on hover */
            .center-content .button:hover {
            background-color: teal;
            }
            </style>
        <h2>Your cart is empty!</h2>
        <a href='menu.php' class='button'>Go back to menu</a>
      </div>";
}

include 'footer.php'; // Include footer
$conn->close(); // Close the database connection
?>

</body>
</html>
