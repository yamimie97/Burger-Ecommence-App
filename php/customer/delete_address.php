<?php
include 'db.php'; // Database connection

if (isset($_GET['addressId'])) {
    $addressId = $_GET['addressId'];

    // Delete the address from the database
    $delete_address_query = "DELETE FROM addresses WHERE addressId = ?";
    $stmt = $conn->prepare($delete_address_query);
    $stmt->bind_param("s", $addressId);

    if ($stmt->execute()) {
        // Redirect back to the Manage Addresses section
        header("Location: account.php?section=addresses");
        exit();
    } else {
        echo "Error deleting address.";
    }

    $stmt->close();
}

$conn->close();
?>
