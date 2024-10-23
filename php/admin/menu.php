<?php
    include 'sessioncheck.php'; // Check session staff

    // Function to generate a new menu ID
    function generateMenuID($conn) {
        $sql = "SELECT menuid FROM menu ORDER BY menuid DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row['menuid'];
            $num = (int) substr($last_id, 2) + 1;
            return "M-" . str_pad($num, 4, "0", STR_PAD_LEFT);
        } else {
            return "M-0001";
        }
    }

    // Handle form submission for adding new menu items
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addMenu'])) {
        $menuName = $_POST['menuName'];
        $menuId = generateMenuID($conn); // Generate a new menu ID

        // Insert the new menu item into the database
        $sql = "INSERT INTO menu (menuid, menuName) VALUES ('$menuId', '$menuName')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('New menu item added successfully!');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Handle form submission for editing menu items
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editMenu'])) {
        $menuId = $_POST['menuId'];
        $menuName = $_POST['menuName'];

        // Update the menu item in the database
        $sql = "UPDATE menu SET menuName='$menuName' WHERE menuid='$menuId'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Menu item updated successfully!');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
}

    // Handle form submission for deleting menu items
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteMenu'])) {
        $menuId = $_POST['menuId'];

        // Delete the menu item from the database
        $sql = "DELETE FROM menu WHERE menuid='$menuId'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Menu item deleted successfully!');</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Handle search query
    $searchQuery = "";
    if (isset($_GET['searchQuery'])) {
        $searchQuery = $_GET['searchQuery'];
        $sql = "SELECT * FROM menu WHERE menuName LIKE '%$searchQuery%'";
    } else {
        $sql = "SELECT * FROM menu";
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
    <title>Menu Management</title>
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
                        <h3>Menu Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to Add a New Menu Item -->
                        <h4>Add New Menu</h4>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="menuName">Menu Name:</label>
                                <input type="text" class="form-control" id="menuName" name="menuName" required>
                            </div>
                            <button type="submit" name="addMenu" class="btn btn-primary">Add Menu</button>
                        </form>
                        <hr>

                        <!-- Display the Menu Items -->
                        <h4>Current Menu</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu ID</th>
                                    <th>Menu Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Display filtered or all menu items
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['menuid']}</td>
                                                <td>{$row['menuName']}</td>
                                                <td>
                                                    <button class='btn btn-warning btn-sm editBtn' data-id='{$row['menuid']}' data-name='{$row['menuName']}'>Edit</button>
                                                    <form method='POST' action='' style='display:inline-block;'>
                                                        <input type='hidden' name='menuId' value='{$row['menuid']}'>
                                                        <button type='submit' name='deleteMenu' class='btn btn-danger btn-sm'>Delete</button>
                                                    </form>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No menu items found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Edit Menu Item Modal -->
                        <div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="editMenuModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="menuId" id="editMenuId">
                                            <div class="form-group">
                                                <label for="editMenuName">Menu Name:</label>
                                                <input type="text" class="form-control" name="menuName" id="editMenuName" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="editMenu" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
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

    <!-- JavaScript to handle Edit Button clicks and populate the modal -->
    <script>
        $(document).ready(function() {
            $('.editBtn').on('click', function() {
                var menuId = $(this).data('id');
                var menuName = $(this).data('name');

                $('#editMenuId').val(menuId);
                $('#editMenuName').val(menuName);

                $('#editMenuModal').modal('show');
            });
        });
    </script>
</body>
</html>
