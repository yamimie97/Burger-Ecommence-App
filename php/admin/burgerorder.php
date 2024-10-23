<?php
include 'sessioncheck.php'; // Check session staff

// Initialize the search query
$searchQuery = "";

// Check if a search query is provided
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT * FROM burgerorder 
            WHERE orderId LIKE '%$searchQuery%' 
            OR customerId LIKE '%$searchQuery%' 
            OR employeeId LIKE '%$searchQuery%' 
            OR totalAmount LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT * FROM burgerorder";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS from RegisterEmployeeCss -->
    <title>Burger Order Management</title>
</head>
<body>

    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Include the sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Right Content (75%) -->
            <div class="col-md-9 d-flex flex-column">
                <div class="card shadow-sm flex-grow-1">
                    <div class="card-header text-center bg-teal text-white">
                        <h3>Burger Order Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display the Burger Orders Table -->
                        <h4>Burger Orders</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer ID</th>
                                    <th>Employee ID</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any orders and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['orderId']}</td>
                                                <td>{$row['customerId']}</td>
                                                <td>{$row['employeeId']}</td>
                                                <td>{$row['orderDate']}</td>
                                                <td>{$row['totalAmount']}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No burger orders found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
