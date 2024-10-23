<?php
include 'sessioncheck.php'; // Check session staff

// Fetch all feedback from the database
$sql = "SELECT * FROM feedback";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS from RegisterEmployeeCss -->
    <title>Feedback Management</title>
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
                        <h3>Feedback Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Display the Feedback Table -->
                        <h4>Feedback</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Feedback ID</th>
                                    <th>Customer ID</th>
                                    <th>Comments</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there is any feedback and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['feedbackId']}</td>
                                                <td>{$row['customerId']}</td>
                                                <td>{$row['comments']}</td>
                                                <td>{$row['rating']}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No feedback found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- Help and Documentation Section -->
                        <div class="col-md-9 mt-4">
                            <div class="card shadow-sm">
                                <div class="card-header text-center bg-teal text-white"> <!-- Changed header color to teal and center-aligned -->
                                    <h3>Help and Documentation</h3>
                                </div>
                                <div class="card-body">
                                    <p>Here you can find guidance on how to manage feedback and use the admin dashboard:</p>
                                    <ul>
                                        <li><strong>Viewing Feedback:</strong> The table above displays all customer feedback. You can use this data to improve your service.</li>
                                        <li><strong>Handling Feedback:</strong> After reviewing feedback, you may reach out to customers if needed, and use the ratings to assess customer satisfaction.</li>
                                    </ul>
                                </div>
                            </div>
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
