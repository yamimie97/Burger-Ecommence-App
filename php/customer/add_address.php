<?php
include 'header.php'; // Include header
include 'db.php';     // Database connection

// Simulate logged-in customer ID (this would usually come from session)
$customerID = isset($_SESSION['customerId']) ? $_SESSION['customerId'] : 'cusID-0001'; // Example

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newAddress = $_POST['address'];

    // Validate the new address
    if (!empty($newAddress)) {
        // Insert the new address into the database
        $add_address_query = "INSERT INTO addresses (customerId, addresses) VALUES (?, ?)";
        $stmt = $conn->prepare($add_address_query);
        $stmt->bind_param("ss", $customerID, $newAddress);

        if ($stmt->execute()) {
            // Redirect back to the Manage Addresses section
            header("Location: account.php?section=addresses");
            exit();
        } else {
            echo "Error adding address.";
        }

        $stmt->close();
    } else {
        echo "Address field cannot be empty.";
    }
}

?>

<style>
/* Basic Styles for Add Address Page */
.add-address-page {
    padding: 40px 0;
    text-align: center;
}

.add-address-page h2 {
    margin-bottom: 20px;
    color: #333;
}

.add-address-page form input, .add-address-page form button {
    display: block;
    width: 100%;
    max-width: 800px;
    margin: 10px auto;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.add-address-page form button {
    background-color: teal;
    color: white;
    border: none;
    cursor: pointer;
}

.add-address-page form button:hover {
    background-color: #006d6d;
}
</style>

<section class="add-address-page">
    <div class="container">
        <h2>Add a New Address</h2>
        <form action="add_address.php" method="POST">
            <input type="text" name="address" required placeholder="Enter your new address">
            <button type="submit">Add Address</button>
        </form>
    </div>
</section>

<?php
include 'footer.php'; // Include footer
$conn->close();       // Close the database connection
?>
