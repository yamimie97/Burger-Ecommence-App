<?php
include 'header.php';
include 'db.php'; // Include database connection

// Assuming the user is logged in and their user ID is stored in session
if (!isset($_SESSION['customerId'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['customerId'];

// Fetch user details (for autofilled name and phone number)
$user_query = "SELECT name, phoneNumber FROM customer WHERE customerId = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $userId); 
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch existing addresses of the user
$address_query = "SELECT * FROM addresses WHERE customerId = ?";
$stmt = $conn->prepare($address_query);
$stmt->bind_param("s", $userId); 
$stmt->execute();
$address_result = $stmt->get_result();

// If user submits the form to add a new address
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_address'])) {
    $address = $_POST['address'];

    // Insert the new address into the database
    $insert_address_query = "INSERT INTO addresses (customerId, addresses) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_address_query);
    $stmt->bind_param("ss", $userId, $address);
    $stmt->execute();

    // Retrieve the ID of the newly inserted address
    $newAddressId = $conn->insert_id;

    // Pass this new address ID and address to the next step (review_order.php)
    $_POST['addressId'] = $newAddressId;
    $_POST['new_address'] = $address;
    
    // Redirect to the same page to refresh the address list
    header("Location: checkout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Address & Payment</title>
    <link rel="stylesheet" href="css/checkout_style.css">
</head>
<body>
    <nav>
        <ul class='steps'>
            <li><h2>1. Shopping Cart</h2></li>
            <li class='current-step'><h2>2. Address & Payment</h2></li>
            <li><h2>3. Review Order</h2></li>
        </ul>
    </nav>
    <form action="revieworder.php" method="POST" id="checkoutForm">
        <div class="checkout-container"> 
            <div class="left-section">
                <!-- User Name, Address, and Phone Number -->
                <h2>Delivery Information</h2>
                <div class="user-details">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly />
                    
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phoneNumber']); ?>" readonly />
                    
                    <!-- User Address Selection -->

                        <div class="address-section">
                            <h2>Delivery Address</h2>
                            <!-- Existing Addresses from Database -->
                            <?php if ($address_result->num_rows > 0): ?>
                                <?php while ($address = $address_result->fetch_assoc()): ?>
                                    <div>
                                        <label>
                                            <input type="radio" name="addressId" value="<?php echo $address['addressId']; ?>" required>
                                            <?php echo htmlspecialchars($address['addresses']); ?>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No existing addresses found. Please add a new address.</p>
                            <?php endif; ?>

                            <!-- Option to Add New Address -->
                            <div> 
                                <label for="otherAddress"><input type="radio" name="addressId" value="other" id="otherAddress">Other</label>
                            </div>

                            <!-- New Address Input Field -->
                            <div class="new-address" id="newAddressFields" style="display: none;">
                                <input type="text" name="new_address" id="new_address" placeholder="Enter your new address">
                            </div>
                        </div>
                    
                </div>
            </div>

            <!-- Payment Section on Right Side -->
            <div class="right-section">
                <h2>Payment Information</h2>
                <div class="payment-section">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" name="cardNumber" id="cardNumber" placeholder="XXXX-XXXX-XXXX-XXXX" required pattern="\d{12,19}" title="Card number must be between 10 to 19 digits" maxlength="19">
                    
                    <label for="expiryDate">Expiry Date</label>
                    <input type="text" name="expiryDate" id="expiryDate" placeholder="MM/YY" required>
                    
                    <label for="cvv">CVV</label>
                    <input type="text" name="cvv" id="cvv" placeholder="123" required pattern="\d{3}" title="CVV must be exactly 3 digits" maxlength="3">

                    <div class="error-message" id="paymentError">Please fill all payment fields correctly.</div>
                </div>
            </div>
        </div>

        <!-- Buttons for Proceed and Back to Cart -->
        <div class="button-container">
            <button type="submit" name="checkout" class="submit-btn">Proceed to Review Order</button>
            <a href="cart.php" class="back-btn">← Back to Cart</a>
        </div>
    </form>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const otherAddressRadio = document.getElementById('otherAddress');
    const newAddressFields = document.getElementById('newAddressFields');
    const checkoutForm = document.getElementById('checkoutForm');
    const paymentError = document.getElementById('paymentError');
    const cardNumber = document.getElementById('cardNumber');
    const cvv = document.getElementById('cvv');
    const newAddressInput = document.getElementById('newAddress');
    
    // Show the new address input when "Other" is selected
    otherAddressRadio.addEventListener('change', function() {
        if (this.checked) {
            newAddressFields.style.display = 'block';
            newAddressInput.required = true; // Make new address required if "Other" is selected
        }
    });

    // Show new address fields when "Other" is selected
    otherAddressRadio.addEventListener('change', function() {
        if (this.checked) {
            newAddressFields.style.display = 'block';
            document.getElementById('address').required = true;
        }
    });

    // Hide new address fields when any other address is selected
    document.querySelectorAll('input[name="addressId"]').forEach(function(radio) {
        if (radio.id !== 'otherAddress') {
            radio.addEventListener('change', function() {
                newAddressFields.style.display = 'none';
                document.getElementById('address').required = false;
            });
        }
    });

    // Allow only numbers in card number and CVV fields
    document.getElementById('cardNumber').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, ''); 
    });

    document.getElementById('cvv').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3); // Limit to 3 digits
    });

    // Expiry date formatting: Add '/' after 2 digits
    document.getElementById('expiryDate').addEventListener('input', function(e) {
        let input = this.value.replace(/[^0-9]/g, ''); 
        if (input.length >= 2) {
            input = input.slice(0, 2) + '/' + input.slice(2, 4); // Format as MM/YY
        }
        this.value = input;
    });

    // Ensure expiry date can't exceed 5 characters (MM/YY)
    document.getElementById('expiryDate').addEventListener('keypress', function(e) {
        if (this.value.length >= 5 && e.keyCode !== 8) {
            e.preventDefault(); // Prevent typing more than 5 characters
        }
    });
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>

