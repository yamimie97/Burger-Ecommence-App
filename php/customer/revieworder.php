<?php
include 'header.php';
include 'db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['customerId'];

// Fetch user details for the review
$user_query = "SELECT name, phoneNumber FROM customer WHERE customerId = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

function generateAddressId($conn) {
    $sql = "SELECT addressId FROM addresses ORDER BY addressId DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['addressId'];
        $num = (int) substr($last_id, 4) + 1;
        return "Adr-" . str_pad($num, 5, "0", STR_PAD_LEFT);
    } else {
        return "Adr-00001"; // Start with Adr-00001 if no records exist
    }
}
// Determine the selected address
$selected_address = null;
if (isset($_POST['addressId']) && $_POST['addressId'] === 'other' && isset($_POST['new_address'])) {
    // Insert new address into the database
    $newAddress = $_POST['new_address'];
    $newAddressId = generateAddressId($conn); // Generate a new address ID

    // Insert new address into the addresses table
    $insert_address_query = "INSERT INTO addresses (addressId, customerId, addresses) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_address_query);
    $stmt->bind_param("sss", $newAddressId, $userId, $newAddress);
    $stmt->execute();

    // Set the selected address to the newly inserted address
    $selected_address = ['addresses' => $newAddress];
} else if (isset($_POST['addressId'])) {
    // Existing address logic remains the same
    $addressId = $_POST['addressId'];
    $address_query = "SELECT addresses FROM addresses WHERE addressId = ?";
    $stmt = $conn->prepare($address_query);
    $stmt->bind_param("s", $addressId);
    $stmt->execute();
    $address_result = $stmt->get_result();
    $selected_address = $address_result->fetch_assoc();
}

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

// Fetch and sanitize cardNumber and expiryDate from POST
$cardNumber = $_POST['cardNumber'] ?? '';  // Fetch card number from POST
$expiryDate = $_POST['expiryDate'] ?? '';  // Fetch expiry date from POST

// Check if the card number is long enough (at least 4 characters)
if (strlen($cardNumber) >= 4) {
    $maskedCardNumber = str_repeat('X', strlen($cardNumber) - 4) . substr($cardNumber, -4);
} else {
    $maskedCardNumber = $cardNumber; 
}

// Function to detect card type (Visa, MasterCard, etc.)
function getCardType($cardNumber) {
    // Remove spaces and dashes from card number
    $cardNumber = preg_replace('/[\s-]/', '', $cardNumber);

    // Visa
    if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber)) {
        return 'Visa';
    }
    // MasterCard
    elseif (preg_match('/^5[1-5][0-9]{14}$/', $cardNumber) || preg_match('/^2[2-7][0-9]{14}$/', $cardNumber)) {
        return 'MasterCard';
    }
    // American Express
    elseif (preg_match('/^3[47][0-9]{13}$/', $cardNumber)) {
        return 'American Express';
    }
    // Discover
    elseif (preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber)) {
        return 'Discover';
    }
    // JCB
    elseif (preg_match('/^35(2[89]|[3-8][0-9])[0-9]{12}$/', $cardNumber)) {
        return 'JCB';
    }
    // Diners Club
    elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $cardNumber)) {
        return 'Diners Club';
    }
    // Unknown card type
    else {
        return 'Unknown';
    }
}

$cardType = getCardType($cardNumber);

// Calculate cart totals
$total = 0;
$totalDiscount = 0;
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $cartItem) {
        $subtotal = $cartItem['itemPrice'] * $cartItem['quantity'];
        
        // Check if there is a promotion for this item
        $promotion = getPromotionForItem($cartItem['menuitemId'], $conn);
        $discount = 0;
        if ($promotion) {
            $discount = $cartItem['itemPrice'] * ($promotion['discount'] / 100);
            $totalDiscount += $discount * $cartItem['quantity']; // Calculate total discount
        }

        $subtotal -= $discount * $cartItem['quantity']; // Subtract discount from subtotal
        $total += $subtotal; // Add to total price
    }
}

$tax = number_format($total * 0.05, 2, '.', '');
$grandTotal = number_format($total + $tax, 2, '.', '');

// If user clicks Complete Order
if (isset($_POST['complete_order'])) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // Function to generate order ID
        function generateOrderID($conn) {
            $sql = "SELECT orderId FROM burgerorder ORDER BY orderId DESC LIMIT 1";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $last_id = $row['orderId'];
                $num = (int) substr($last_id, 4) + 1;
                return "Odr-" . str_pad($num, 5, "0", STR_PAD_LEFT);
            } else {
                return "Odr-00001"; // If no records, start with O-00001
            }
        }

        // Function to generate orderdetails ID
        function generateOrderDetailsID($conn) {
            $sql = "SELECT orderdetailsId FROM orderdetails ORDER BY orderdetailsId DESC LIMIT 1";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $last_id = $row['orderdetailsId'];
                $num = (int) substr($last_id, 4) + 1;
                return "OrD-" . str_pad($num, 5, "0", STR_PAD_LEFT);
            } else {
                return "OrD-00001"; // If no records, start with O-00001
            }
        }

        $orderId = generateOrderID($conn);

        // Insert into burgerorder table
        $orderDate = date('Y-m-d H:i:s');
        $employeeId = "E-0001"; // Assuming employeeId is optional, you can set it if needed
        $order_query = "INSERT INTO burgerorder (orderId, customerId, employeeId, orderDate, totalAmount) VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("sssss", $orderId, $userId, $employeeId, $orderDate, $grandTotal);
        $stmt->execute();

        // Insert into orderdetails table
        $taxRate = 0.05;
        $cartItems = $_SESSION['cart'] ?? [];
        foreach ($cartItems as $cartItem) {
            $orderdetailsId = generateOrderDetailsID($conn);
            $promotion = getPromotionForItem($cartItem['menuitemId'], $conn);
            $discount = number_format($promotion ? $cartItem['itemPrice'] * ($promotion['discount'] / 100) : 0, 2, '.', '');
            $totalItemCost = number_format($cartItem['itemPrice'] * $cartItem['quantity'], 2, '.', '');
            $finalItemCost = number_format((($totalItemCost - ($discount * $cartItem['quantity'])) + ($totalItemCost * $taxRate)), 2, '.', '');
            $status = 'pending'; // Set initial status
            $orderDetailsQuery = "
                INSERT INTO orderdetails (orderdetailsId, menuitemId, orderId, quantity, totalItemCost, discountApplied, taxAmount, finalItemCost, status) 
                VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($orderDetailsQuery);
            $stmt->bind_param(
                "ssssiddds",
                $orderdetailsId,
                $cartItem['menuitemId'],
                $orderId,
                $cartItem['quantity'],
                $totalItemCost,
                $discount,
                $tax,
                $finalItemCost,
                $status
            );
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        // Clear the cart
        unset($_SESSION['cart']);

        // Display the success message and auto-redirect
        echo "<div class='success-message'>Your items have been successfully ordered! Redirecting to homepage...</div>";

        // JavaScript to redirect after 5 seconds
        echo "
        <script>
            setTimeout(function() {
                window.location.href = 'index.php'; // Redirect to index.php
            }, 5000); // 5 seconds
        </script>";
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
<style>
    .success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    padding: 15px;
    margin: 20px 0;
    text-align: center;
    border-radius: 5px;
    font-size: 18px;
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Order</title>
    <link rel="stylesheet" href="css/revieworder_style.css">
</head>
<body>

<nav>
    <ul class='steps'>
        <li><h2>1. Shopping Cart</h2></li>
        <li><h2>2. Address & Payment</h2></li>
        <li class='current-step'><h2>3. Review Order</h2></li>
    </ul>
</nav>

<div class="review-container">
    <!-- Cart Summary -->
    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <table>
            <tr>
                <th>Item Image</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Subtotal</th>
            </tr>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <h2>Your cart is empty</h2>
            <?php else: ?>
                <table>
                    <?php foreach ($_SESSION['cart'] as $cartItem): 
                        // Calculate discount
                        $promotion = getPromotionForItem($cartItem['menuitemId'], $conn);
                        $discount = 0;
                        if ($promotion) {
                            $discount = $cartItem['itemPrice'] * ($promotion['discount'] / 100);
                        }
                        $subtotal = ($cartItem['itemPrice'] - $discount) * $cartItem['quantity']; ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($cartItem['itemImage']); ?>" alt="Item Image" width="100"></td>
                            <td><?php echo htmlspecialchars($cartItem['itemName']); ?></td>
                            <td><?php echo htmlspecialchars($cartItem['quantity']); ?></td>
                            <td>$<?php echo number_format($cartItem['itemPrice'], 2); ?></td>
                            <td>$<?php echo $discount > 0 ? number_format($discount, 2) . ' off' : 'No discount'; ?></td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p><strong>Subtotal:</strong> $<?php echo number_format($total, 2); ?></p>
                <p><strong>Total Discount:</strong> $<?php echo number_format($totalDiscount, 2); ?></p>
                <p><strong>Tax (5%):</strong> $<?php echo number_format($tax, 2); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($grandTotal, 2); ?></p>
            <?php endif; ?>
        </table>
    </div>

    <div class="details-container">
        <!-- Delivery Information Summary -->
        <div class="delivery-info">
            <h2>Delivery Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phoneNumber']); ?></p>
            <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($selected_address['addresses'] ?? 'No address selected'); ?></p>
        </div>

        <!-- Payment Information Summary -->
        <div class="payment-info">
            <h2>Payment Information</h2>
            <p><strong>Card Number:</strong> <?php echo htmlspecialchars($maskedCardNumber); ?></p>
            <p><strong>Expiry Date:</strong> <?php echo htmlspecialchars($_POST['expiryDate'] ?? ''); ?></p>
        </div>
    

        <!-- Action Buttons -->
        <form action="revieworder.php" method="POST">
        <div class="button-container">
            <input type="hidden" name="complete_order" value="1">
            <button type="submit" name="checkout" class="complete-btn">Complete Order</button>
            <a href="checkout.php" class="back-btn">← Back to Checkout</a>
        </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
