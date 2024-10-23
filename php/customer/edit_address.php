<?php
include 'header.php'; // Include header
include 'db.php';     // Database connection

// Simulate logged-in customer ID (this would usually come from session)
$customerID = isset($_SESSION['customerId']) ? $_SESSION['customerId'] : 'cusID-0001'; 

// Check if an address ID is provided for editing
if (isset($_GET['addressId'])) {
    $addressId = $_GET['addressId'];

    // Fetch the existing address from the database
    $fetch_address_query = "SELECT * FROM addresses WHERE addressId = ? AND customerId = ?";
    $stmt = $conn->prepare($fetch_address_query);
    $stmt->bind_param("ss", $addressId, $customerID);
    $stmt->execute();
    $result = $stmt->get_result();
    $address = $result->fetch_assoc();

    // If the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $updatedAddress = $_POST['address'];

        // Validate the updated address
        if (!empty($updatedAddress)) {
            // Update the address in the database
            $update_address_query = "UPDATE addresses SET addresses = ? WHERE addressId = ? AND customerId = ?";
            $stmt_update = $conn->prepare($update_address_query);
            $stmt_update->bind_param("sss", $updatedAddress, $addressId, $customerID);

            if ($stmt_update->execute()) {
                // Redirect back to the Manage Addresses section
                header("Location: account.php?section=addresses");
                exit();
            } else {
                echo "Error updating address.";
            }

            $stmt_update->close();
        } else {
            echo "Address field cannot be empty.";
        }
    }

    $stmt->close();
} else {
    // Redirect to manage addresses if no addressId is provided
    header("Location: account.php?section=addresses");
    exit();
}

?>

<style>
/* Basic Styles for Edit Address Page */
.edit-address-page {
    padding: 40px 0;
    text-align: center;
}

.edit-address-page h2 {
    margin-bottom: 20px;
    color: #333;
}

.edit-address-page form input, .edit-address-page form button {
    display: block;
    width: 100%;
    max-width: 800px;
    margin: 10px auto;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.edit-address-page form button {
    background-color: teal;
    color: white;
    border: none;
    cursor: pointer;
}

.edit-address-page form button:hover {
    background-color: #006d6d;
}
</style>

<section class="edit-address-page">
    <div class="container">
        <h2>Update Address</h2>
        <form action="edit_address.php?addressId=<?php echo $addressId; ?>" method="POST">
            <input type="text" name="address" value="<?php echo $address['addresses']; ?>" required placeholder="Enter your new address">
            <button type="submit">Update Address</button>
        </form>
    </div>
</section>

<?php
include 'footer.php'; // Include footer
$conn->close();       // Close the database connection
?>
