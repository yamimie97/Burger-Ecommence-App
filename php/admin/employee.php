<?php
include 'sessioncheck.php'; // Check session staff

// Handle search query
$searchQuery = "";
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT * FROM employee WHERE employeeName LIKE '%$searchQuery%' OR Role LIKE '%$searchQuery%' OR Username LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT * FROM employee";
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
    <title>Employee Management</title>
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
                        <h3>Employee Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display the Employee Table -->
                        <h4>Employee Details</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Role</th>
                                    <th>Profile Image</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any employees and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['employeeId']}</td>
                                                <td>{$row['employeeName']}</td>
                                                <td>{$row['Role']}</td>
                                                <td><img src='../../image/profile/{$row['profileImg']}' alt='Profile' class='img-fluid' style='max-width: 50px;'></td>
                                                <td>{$row['Username']}</td>
                                                <td>{$row['Password']}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No employees found</td></tr>";
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
