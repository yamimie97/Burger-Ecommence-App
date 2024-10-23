<?php
include 'sessioncheck.php'; // Check session staff

// Initialize the search query
$searchQuery = "";

// Check if a search query is provided
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT * FROM customer 
            WHERE customerId LIKE '%$searchQuery%' 
            OR name LIKE '%$searchQuery%' 
            OR email LIKE '%$searchQuery%' 
            OR phoneNumber LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT * FROM customer";
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
    <title>Customer Management</title>
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
                        <h3>Customer Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display the Customer Details Table -->
                        <h4>Customer Details</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Username</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any customers and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['customerId']}</td>
                                                <td>{$row['name']}</td>
                                                <td>{$row['email']}</td>
                                                <td>{$row['phoneNumber']}</td>
                                                <td>{$row['address']}</td>
                                                <td>{$row['username']}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No customers found</td></tr>";
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
