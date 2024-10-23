<?php
include 'sessioncheck.php'; // Check session staff

// Function to generate a new promotion ID
function generatePromotionID($conn) {
    $sql = "SELECT promotionId FROM promotion ORDER BY promotionId DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['promotionId'];
        $num = (int) substr($last_id, 2) + 1;
        return "P-" . str_pad($num, 4, "0", STR_PAD_LEFT);
    } else {
        return "P-0001";
    }
}

// Handle form submission for adding new promotions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPromotion'])) {
    $promotionName = $_POST['promotionName'];
    $discount = $_POST['discount'];
    $promotionImage = $_FILES['promotionImage']['name'];
    $promotionId = generatePromotionID($conn); // Generate a new promotion ID

    // Handle file upload
    $target_dir = "../../image/promo/";
    $target_file = $target_dir . basename($promotionImage);
    move_uploaded_file($_FILES['promotionImage']['tmp_name'], $target_file);

    // Insert the new promotion into the database
    $sql = "INSERT INTO promotion (promotionId, promotionName, discount, promotionImage) VALUES ('$promotionId', '$promotionName', '$discount', '$promotionImage')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New promotion added successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle edit promotion action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editPromotion'])) {
    $promotionId = $_POST['promotionId'];
    $promotionName = $_POST['promotionName'];
    $discount = $_POST['discount'];
    $promotionImage = $_FILES['promotionImage']['name'];

    // Handle file upload if a new image is uploaded
    if (!empty($promotionImage)) {
        $target_dir = "../../image/promo/";
        $target_file = $target_dir . basename($promotionImage);
        move_uploaded_file($_FILES['promotionImage']['tmp_name'], $target_file);
        $sql = "UPDATE promotion SET promotionName='$promotionName', discount='$discount', promotionImage='$promotionImage' WHERE promotionId='$promotionId'";
    } else {
        $sql = "UPDATE promotion SET promotionName='$promotionName', discount='$discount' WHERE promotionId='$promotionId'";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Promotion updated successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle delete promotion action
if (isset($_GET['deletePromotion'])) {
    $promotionId = $_GET['deletePromotion'];
    $sql = "DELETE FROM promotion WHERE promotionId='$promotionId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Promotion deleted successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle search query
$searchQuery = "";
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT * FROM promotion WHERE promotionName LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT * FROM promotion";
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
    <title>Promotion Management</title>
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
                        <h3>Promotion Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to Add a New Promotion -->
                        <h4>Add New Promotion</h4>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="promotionName">Promotion Name:</label>
                                <input type="text" class="form-control" id="promotionName" name="promotionName" required>
                            </div>
                            <div class="form-group">
                                <label for="discount">Discount (%):</label>
                                <input type="number" class="form-control" id="discount" name="discount" required>
                            </div>
                            <div class="form-group">
                                <label for="promotionImage">Promotion Image:</label>
                                <input type="file" class="form-control-file" id="promotionImage" name="promotionImage" required>
                            </div>
                            <button type="submit" name="addPromotion" class="btn btn-primary">Add Promotion</button>
                        </form>
                        <hr>

                        <!-- Display the Promotions Table -->
                        <h4>Current Promotions</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Promotion ID</th>
                                    <th>Promotion Name</th>
                                    <th>Discount</th>
                                    <th>Promotion Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any promotions and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['promotionId']}</td>
                                                <td>{$row['promotionName']}</td>
                                                <td>{$row['discount']}%</td>
                                                <td><img src='../../image/promo/{$row['promotionImage']}' alt='Promotion' class='img-fluid' style='max-width: 100px;'></td>
                                                <td>
                                                    <button class='btn btn-info btn-sm' data-toggle='modal' data-target='#editModal{$row['promotionId']}'>Edit</button>
                                                    <a href='?deletePromotion={$row['promotionId']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this promotion?\");'>Delete</a>
                                                </td>
                                              </tr>";

                                        // Edit modal for each promotion
                                        echo "
                                        <div class='modal fade' id='editModal{$row['promotionId']}' tabindex='-1' role='dialog' aria-labelledby='editModalLabel{$row['promotionId']}' aria-hidden='true'>
                                            <div class='modal-dialog' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title' id='editModalLabel{$row['promotionId']}'>Edit Promotion</h5>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <form method='POST' action='' enctype='multipart/form-data'>
                                                            <input type='hidden' name='promotionId' value='{$row['promotionId']}'>
                                                            <div class='form-group'>
                                                                <label for='promotionName'>Promotion Name:</label>
                                                                <input type='text' class='form-control' name='promotionName' value='{$row['promotionName']}' required>
                                                            </div>
                                                            <div class='form-group'>
                                                                <label for='discount'>Discount (%):</label>
                                                                <input type='number' class='form-control' name='discount' value='{$row['discount']}' required>
                                                            </div>
                                                            <div class='form-group'>
                                                                <label for='promotionImage'>Promotion Image:</label>
                                                                <input type='file' class='form-control-file' name='promotionImage'>
                                                            </div>
                                                            <button type='submit' name='editPromotion' class='btn btn-primary'>Update Promotion</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No promotions found</td></tr>";
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
