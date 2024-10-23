<?php
include 'sessioncheck.php'; // Check session staff

// Fetch all order details from the database with search functionality
$searchQuery = "";

// Check if a search query is provided from the header search
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT * FROM orderdetails 
            WHERE orderdetailsId LIKE '%$searchQuery%' 
            OR menuitemId LIKE '%$searchQuery%' 
            OR orderId LIKE '%$searchQuery%' 
            OR status LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT * FROM orderdetails";
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
    <title>Order Details Management</title>
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
                        <h3>Order Details Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display the Order Details Table -->
                        <h4>Order Details</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order Details ID</th>
                                    <th>Menu Item ID</th>
                                    <th>Order ID</th>
                                    <th>Quantity</th>
                                    <th>Total Item Cost</th>
                                    <th>Discount Applied</th>
                                    <th>Tax Amount</th>
                                    <th>Final Item Cost</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any order details and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['orderdetailsId']}</td>
                                                <td>{$row['menuitemId']}</td>
                                                <td>{$row['orderId']}</td>
                                                <td>{$row['quantity']}</td>
                                                <td>{$row['totalItemCost']}</td>
                                                <td>{$row['discountApplied']}</td>
                                                <td>{$row['taxAmount']}</td>
                                                <td>{$row['finalItemCost']}</td>
                                                <td>{$row['status']}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No order details found</td></tr>";
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
