<?php
include 'header.php'; // Include header
include 'db.php';     // Database connection

// Simulate logged-in customer ID
$customerID = isset($_SESSION['customerId']) ? $_SESSION['customerId'] : 'cusID-0001';

// Get active section
$activeSection = isset($_GET['section']) ? $_GET['section'] : 'profile';

// Initialize error and success messages
$oldPasswordError = $newPasswordError = $confirmPasswordError = $nameUpdateMessage = $passwordUpdateMessage = "";

// Fetch user data from the database
$profile_query = "SELECT * FROM customer WHERE customerId = '$customerID'";
$profile_result = $conn->query($profile_query);
$customer = $profile_result->fetch_assoc();
$stored_hashed_password = $customer['password'];

// Handle form submission for name or password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the action is to update the name
    if (isset($_GET['action']) && $_GET['action'] === 'updateName') {
        $newName = $_POST['name'];
        
        // Validate the name (ensure it only contains letters and spaces)
        if (!preg_match('/^[a-zA-Z\s]+$/', $newName)) {
            $nameUpdateMessage = "Name must contain only letters and spaces.";
        } else {
            // Update the name in the database
            $update_name_sql = "UPDATE customer SET name = ? WHERE customerId = ?";
            $stmt_update_name = $conn->prepare($update_name_sql);
            $stmt_update_name->bind_param("ss", $newName, $customerID);

            if ($stmt_update_name->execute()) {
                $nameUpdateMessage = "Name updated successfully!";
            } else {
                echo "Error updating name: " . $conn->error;
            }
        }
    }

    // Check if the action is to update the password
    if (isset($_GET['action']) && $_GET['action'] === 'updatePassword') {
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        $isFormValid = true;

        // Verify the old password
        if (!password_verify($oldPassword, $stored_hashed_password)) {
            $oldPasswordError = "The old password is incorrect.";
            $isFormValid = false;
        }

        // Check for valid new password format
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,17}$/', $newPassword)) {
            $newPasswordError = "New password must contain at least one uppercase letter, one lowercase letter, one number, and be between 8-17 characters.";
            $isFormValid = false;
        }

        // Check if new password and confirm password match
        if ($newPassword !== $confirmPassword) {
            $confirmPasswordError = "New password and confirm password do not match.";
            $isFormValid = false;
        }

        // If all validations pass, update the password
        if ($isFormValid) {
            // Hash the new password
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_password_sql = "UPDATE customer SET password = ? WHERE customerId = ?";
            $stmt_update_password = $conn->prepare($update_password_sql);
            $stmt_update_password->bind_param("ss", $hashedNewPassword, $customerID);

            if ($stmt_update_password->execute()) {
                $passwordUpdateMessage = "Password updated successfully!";
            } else {
                echo "Error updating password: " . $conn->error;
            }
        }
    }
}
?>

<style>
.account-page {
    padding: 40px 0;
}

.account-page h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.account-page .nav {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
    list-style: none;
    padding: 0;
}

.account-page .nav li {
    margin: 0 10px;
}

.account-page .nav a {
    text-decoration: none;
    color: #333;
    padding: 10px 20px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.account-page .nav a.active {
    background-color: teal;
    color: white;
    border-color: teal;
}

.account-section {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

/* Form Styles */
.account-section form input, .account-section form button {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
}

.account-section form button {
    background-color: teal;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.account-section form button:hover {
    background-color: #006d6d;
}

.reorder-btn {
    background-color: teal;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

.reorder-btn:hover {
    background-color: #006d6d;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

button.edit-btn {
    background-color: teal;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

button.edit-btn:hover {
    background-color: #006d6d;
}

a.delete-btn {
    color: black;
    text-decoration: none;
}

a.delete-btn:hover {
    color: teal;
    text-decoration: underline;
}
.error-text {
    color: red;
    font-size: 14px;
    margin-top: -10px;
    margin-bottom: 15px;
    display: block;
    font-weight: bold;
}

/* Error Input Highlight */
input.error {
    border-color: red;
    background-color: #ffe6e6;
}

/* Success Message Styles */
.success-text {
    color: green;
    font-size: 14px;
    margin-bottom: 15px;
    display: block;
    font-weight: bold;
}

.form-group {
    margin-bottom: 20px;
    position: relative;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: teal;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .account-page {
        padding: 20px 10px;
    }

    .account-page .nav {
        flex-direction: column;
        align-items: center;
        
    }

    .account-page .nav li {
        margin: 10px 0;
        padding: 5px;
    }

    table, th, td {
        font-size: 14px;
    }
}
</style>

<section class="account-page">
    <div class="container">
        <h2>My Account</h2>

        <!-- Navigation Links for Account Sections -->
        <ul class="nav">
            <li><a href="account.php?section=profile" class="<?php echo $activeSection == 'profile' ? 'active' : ''; ?>">Profile</a></li>
            <li><a href="account.php?section=history" class="<?php echo $activeSection == 'history' ? 'active' : ''; ?>">Purchase History</a></li>
            <li><a href="account.php?section=addresses" class="<?php echo $activeSection == 'addresses' ? 'active' : ''; ?>">Manage Addresses</a></li>
        </ul>

        <!-- Account Section Content -->
        <div class="account-section">
            <?php
            // Show Profile Update Form with Password Verification
            if ($activeSection == 'profile') {
                // Fetch user data from database
                $profile_query = "SELECT * FROM customer WHERE customerId = '$customerID'";
                $profile_result = $conn->query($profile_query);
                $customer = $profile_result->fetch_assoc();
            ?>
                <h2>Update Profile</h2>

                <!-- Form for updating name -->
                <form action="account.php?section=profile&action=updateName" method="POST">
                    <input type="hidden" name="customerID" value="<?php echo $customer['customerId']; ?>">
                    
                    <label for="name">Name</label>
                    <input type="text" name="name" value="<?php echo $customer['name']; ?>" required>
                    
                    <button type="submit">Update Name</button>

                    <?php if (!empty($nameUpdateMessage)) { ?>
                        <p class="success-text"><?php echo $nameUpdateMessage; ?></p>
                    <?php } ?>
                </form>

                <hr> <!-- Separator between the two sections -->

                <h2>Change Password</h2>

                <!-- Form for updating password -->
                <form action="account.php?section=profile&action=updatePassword" method="POST">
                    <input type="hidden" name="customerID" value="<?php echo $customer['customerId']; ?>">

                    <label for="old_password">Old Password</label>
                    <input type="password" name="old_password" placeholder="Enter old password" required>
                    <small class="error-text"><?php echo $oldPasswordError; ?></small>

                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                    <small class="error-text"><?php echo $newPasswordError; ?></small>

                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                    <small class="error-text"><?php echo $confirmPasswordError; ?></small>

                    <button type="submit">Update Password</button>

                    <?php if (!empty($passwordUpdateMessage)) { ?>
                        <p class="success-text"><?php echo $passwordUpdateMessage; ?></p>
                    <?php } ?>
                </form>

            <?php
            } elseif ($activeSection == 'history') {
                // Show Purchase History and Order Items
                $history_query = "SELECT * FROM burgerorder WHERE customerId = '$customerID' ORDER BY orderDate DESC";
                $history_result = $conn->query($history_query);
            ?>
            <?php
            if ($activeSection == 'history') {
                // Show Purchase History and Order Items
                $history_query = "SELECT * FROM burgerorder WHERE customerId = '$customerID' ORDER BY orderDate DESC";
                $history_result = $conn->query($history_query);
            ?>
            <h2>Purchase History</h2>
            <?php if ($history_result->num_rows > 0) { ?>
                <?php while ($order = $history_result->fetch_assoc()) { ?>
                    <h4>Date: <?php echo $order['orderDate']; ?></h4>
                    <ul>
                    <?php
                        // Fetch order items for this order, including any applicable promotion
                        $order_items_query = "SELECT mi.itemName, od.quantity, mi.itemPrice, 
                                                    p.discount 
                                            FROM orderdetails od
                                            JOIN menuitem mi ON od.menuitemId = mi.menuitemId
                                            LEFT JOIN promotionitem pi ON mi.menuitemId = pi.menuitemId
                                            LEFT JOIN promotion p ON pi.promotionId = p.promotionId
                                            WHERE od.orderId = '" . $order['orderId'] . "'";
                        $order_items_result = $conn->query($order_items_query);
                        
                        $grandTotal = 0;  // Variable to calculate grand total for the order

                        while ($item = $order_items_result->fetch_assoc()) {
                            // Calculate item total before any discounts
                            $itemTotal = $item['itemPrice'] * $item['quantity'];

                            // Check if the item has an applicable promotion discount
                            if (!is_null($item['discount'])) {
                                // Apply the promotion discount
                                $discountAmount = $itemTotal * ($item['discount'] / 100);  // Convert discount percentage
                                $itemTotal -= $discountAmount;
                                echo '<li>' . $item['itemName'] . ' x ' . $item['quantity'] . ' - $' . number_format($itemTotal, 2) . ' (Discount ' . $item['discount'] . '% applied)</li>';
                            } else {
                                echo '<li>' . $item['itemName'] . ' x ' . $item['quantity'] . ' - $' . number_format($itemTotal, 2) . '</li>';
                            }

                            // Add item total to grand total
                            $grandTotal += $itemTotal;
                        }
                        
                        // Apply 5% tax to the grand total
                        $taxAmount = $grandTotal * 0.05;
                        $finalTotal = $grandTotal + $taxAmount;
                    ?>
                    </ul>
                    <!-- Display the grand total for the order, with tax applied -->
                    <p><strong>Subtotal: $<?php echo number_format($grandTotal, 2); ?></strong></p>
                    <p><strong>Tax (5%): $<?php echo number_format($taxAmount, 2); ?></strong></p>
                    <p><strong>Grand Total (after tax): $<?php echo number_format($finalTotal, 2); ?></strong></p>
                    <button class="reorder-btn" onclick="location.href='reorder.php?orderId=<?php echo $order['orderId']; ?>'">Reorder</button>
                    <hr>
                <?php } ?>
            <?php } else { ?>
                <p>You have no purchase history.</p>
            <?php } ?>
                <?php } else { ?>
                    <p>You have no purchase history.</p>
                <?php } ?>
            <?php
            } elseif ($activeSection == 'addresses') {
                // Show Addresses Linked to Customer
                $addresses_query = "SELECT * FROM addresses WHERE customerId = '$customerID'";
                $addresses_result = $conn->query($addresses_query);
            ?>
                <h2>Manage Addresses</h2>
                <?php if ($addresses_result->num_rows > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($address = $addresses_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $address['addresses']; ?></td>
                                    <td>
                                    <button class="edit-btn" onclick="location.href='edit_address.php?addressId=<?php echo $address['addressId']; ?>'">Edit</button>
                                        <a class="delete-btn" href="delete_address.php?addressId=<?php echo $address['addressId']; ?>" onclick="return confirm('Are you sure you want to delete this address?');">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>No addresses found. <a href="add_address.php">Add a new address</a></p>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>

<script>
    // Function to handle the focus event on input fields
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', function () {
            // Find the related error or success message element
            const errorText = this.nextElementSibling;
            const successText = document.querySelector('.success-text');

            // Check if the next sibling is an error message and has the 'error-text' class
            if (errorText && errorText.classList.contains('error-text')) {
                // Hide the error message
                errorText.style.display = 'none';
                // Remove the error class from the input field
                this.classList.remove('error');
            }

            // Hide the success message when any input is focused
            if (successText) {
                successText.style.display = 'none';
            }
        });
    });
</script>


<?php
include 'footer.php';
$conn->close();
?>
