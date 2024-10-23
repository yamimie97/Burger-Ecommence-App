<?php
include 'sessioncheck.php'; // Check session staff
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS from RegisterEmployeeCss -->
    <title>Main Dashboard</title>
</head>
<body>

    <!-- Include the header -->
    <?php 
        include 'header.php'; 
    ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Include the sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Right Content (75%) -->
            <div class="col-md-9 d-flex flex-column">
                <!-- Sub-Category Data Entry (75%) -->
                <div class="card shadow-sm flex-grow-1">
                    <div class="card-header text-center bg-teal text-white">
                        <h3>Main Dashboard</h3> <!-- Title for the dashboard -->
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center text-center" style="height: 100%;">
                        <div>
                            <p>Welcome to the Admin Dashboard!</p>
                            <p>Here you can manage your system data, orders, employees, promotions, and more.</p>
                            <p>Select a category from the left sidebar to begin.</p>
                        </div>
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
