<?php
include 'db.php'; // Database connection

session_start();
$customerID = isset($_SESSION['customerId']) ? $_SESSION['customerId'] : null;
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : null;

if (!$customerID || !$orderId) {
    header("Location: signin.php"); // Redirect to login if customer is not logged in
    exit();
}

// Fetch the items from the previous order
$order_items_query = "
    SELECT od.menuitemId, od.quantity, mi.itemName, mi.itemPrice, mi.itemImage
    FROM orderdetails od
    JOIN menuitem mi ON od.menuitemId = mi.menuitemId
    WHERE od.orderId = ?";
    
$stmt = $conn->prepare($order_items_query);
$stmt->bind_param("s", $orderId);
$stmt->execute();
$order_items_result = $stmt->get_result();

// Initialize the cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add each item from the previous order to the cart session
if ($order_items_result->num_rows > 0) {
    while ($order_item = $order_items_result->fetch_assoc()) {
        $menuItemId = $order_item['menuitemId'];
        $quantity = $order_item['quantity'];

        // Check if the item already exists in the cart
        $itemExists = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['menuitemId'] == $menuItemId) {
                $cartItem['quantity'] += $quantity; // Increment quantity if item already exists
                $itemExists = true;
                break;
            }
        }

        // If item doesn't exist in the cart, add it as a new item
        if (!$itemExists) {
            $_SESSION['cart'][] = [
                'menuitemId' => $order_item['menuitemId'],
                'itemName' => $order_item['itemName'],
                'itemPrice' => $order_item['itemPrice'],
                'itemImage' => $order_item['itemImage'],
                'quantity' => $quantity
            ];
        }
    }
}

// Redirect to the cart page after reordering
header("Location: cart.php");
exit();
?>
