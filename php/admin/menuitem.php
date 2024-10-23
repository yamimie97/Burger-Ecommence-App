<?php
include 'sessioncheck.php'; // Include your DB connection file

// Function to generate a new menu item ID
function generateMenuItemID($conn) {
    $sql = "SELECT menuitemId FROM menuitem ORDER BY menuitemId DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['menuitemId'];
        $num = (int) substr($last_id, 3) + 1;
        return "MI-" . str_pad($num, 4, "0", STR_PAD_LEFT);
    } else {
        return "MI-0001";
    }
}

// Fetch all menu groups for the dropdown
$sql_menugroup = "SELECT menuGroupId, groupName FROM menugroup";
$result_menugroup = $conn->query($sql_menugroup);
$menuGroupList = [];
if ($result_menugroup->num_rows > 0) {
    while ($row = $result_menugroup->fetch_assoc()) {
        $menuGroupList[] = $row;
    }
}

// Handle form submission for adding new menu items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addMenuItem'])) {
    $menuGroupId = $_POST['menuGroupId'];
    $itemName = $_POST['itemName'];
    $itemPrice = $_POST['itemPrice'];
    $itemDescription = $_POST['description'];
    
    // Handle image upload
    $itemImage = $_FILES['itemImage']['name'];
    $itemImageTmp = $_FILES['itemImage']['tmp_name'];
    $imagePath = "../../image/menu/" . $itemImage;
    copy($itemImageTmp, $imagePath);

    $menuItemId = generateMenuItemID($conn); // Generate a new menu item ID

    // Insert the new menu item into the database
    $sql = "INSERT INTO menuitem (menuitemId, menuGroupId, itemName, itemPrice, itemImage, description) VALUES ('$menuItemId', '$menuGroupId', '$itemName', '$itemPrice', '$imagePath', '$itemDescription')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New menu item added successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle form submission for deleting a menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteMenuItem'])) {
    $menuitemId = $_POST['menuitemId'];

    // Delete the menu item from the database
    $sql = "DELETE FROM menuitem WHERE menuitemId = '$menuitemId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Menu item deleted successfully!');</script>";
    } else {
        echo "Error deleting menu item: " . $conn->error;
    }
}

// Handle form submission for editing a menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editMenuItem'])) {
    $menuitemId = $_POST['menuitemId'];
    $menuGroupId = $_POST['menuGroupId'];
    $itemName = $_POST['itemName'];
    $itemPrice = $_POST['itemPrice'];
    $itemDescription = $_POST['description'];
    $currentImage = $_POST['currentImage'];

    // Check if a new image has been uploaded
    if (!empty($_FILES['itemImage']['name'])) {
        $itemImage = $_FILES['itemImage']['name'];
        $itemImageTmp = $_FILES['itemImage']['tmp_name'];
        $imagePath = "../../image/menu/" . $itemImage;
        copy($itemImageTmp, $imagePath); // Save the new image
    } else {
        // If no new image, use the current one
        $imagePath = $currentImage;
    }

    // Update the menu item in the database
    $sql = "UPDATE menuitem 
            SET menuGroupId = '$menuGroupId', itemName = '$itemName', itemPrice = '$itemPrice', itemImage = '$imagePath', description = '$itemDescription' 
            WHERE menuitemId = '$menuitemId'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Menu item updated successfully!');</script>";
    } else {
        echo "Error updating menu item: " . $conn->error;
    }
}

// Handle search query
$searchQuery = "";
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT mi.menuitemId, mg.groupName, mi.itemName, mi.itemPrice, mi.itemImage, mi.description
            FROM menuitem mi
            JOIN menugroup mg ON mi.menuGroupId = mg.menuGroupId
            WHERE mi.itemName LIKE '%$searchQuery%' OR mg.groupName LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT mi.menuitemId, mg.groupName, mi.itemName, mi.itemPrice, mi.itemImage, mi.description
            FROM menuitem mi
            JOIN menugroup mg ON mi.menuGroupId = mg.menuGroupId";
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
    <title>Menu Item Management</title>
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
                        <h3>Menu Item Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to Add a New Menu Item -->
                        <h4>Add New Menu Item</h4>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="menuGroupId">Select Menu Group:</label>
                                <select class="form-control" id="menuGroupId" name="menuGroupId" required>
                                    <option value="">-- Select Menu Group --</option>
                                    <?php
                                    foreach ($menuGroupList as $menuGroup) {
                                        echo "<option value='{$menuGroup['menuGroupId']}'>{$menuGroup['groupName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="itemName">Item Name:</label>
                                <input type="text" class="form-control" id="itemName" name="itemName" required>
                            </div>
                            <div class="form-group">
                                <label for="itemPrice">Item Price:</label>
                                <input type="text" class="form-control" id="itemPrice" name="itemPrice" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Item Description:</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="itemImage">Upload Image:</label>
                                <input type="file" class="form-control-file" id="itemImage" name="itemImage" required>
                            </div>
                            <button type="submit" name="addMenuItem" class="btn btn-primary">Add Menu Item</button>
                        </form>
                        <hr>

                        <!-- Display the Menu Items -->
                        <h4>Current Menu Items</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu Item ID</th>
                                    <th>Group Name</th>
                                    <th>Item Name</th>
                                    <th>Item Price</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any menu items and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['menuitemId']}</td>
                                                <td>{$row['groupName']}</td>
                                                <td>{$row['itemName']}</td>
                                                <td>{$row['itemPrice']}</td>
                                                <td>{$row['description']}</td>
                                                <td><img src='{$row['itemImage']}' alt='Item Image' style='max-width: 100px;'></td>
                                                <td>
                                                    <button class='btn btn-warning btn-sm editBtn' data-id='{$row['menuitemId']}' data-group='{$row['groupName']}' data-name='{$row['itemName']}' data-price='{$row['itemPrice']}' data-description='{$row['description']}' data-image='{$row['itemImage']}'>Edit</button>
                                                    <form method='POST' action='' style='display:inline-block;'>
                                                        <input type='hidden' name='menuitemId' value='{$row['menuitemId']}'>
                                                        <button type='submit' name='deleteMenuItem' class='btn btn-danger btn-sm'>Delete</button>
                                                    </form>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No menu items found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Edit Menu Item Modal -->
                        <div class="modal fade" id="editMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMenuItemModalLabel">Edit Menu Item</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="menuitemId" id="editMenuItemId">
                                            <input type="hidden" name="currentImage" id="currentImage">
                                            <div class="form-group">
                                                <label for="editMenuGroupId">Select Menu Group:</label>
                                                <select class="form-control" id="editMenuGroupId" name="menuGroupId" required>
                                                    <option value="">-- Select Menu Group --</option>
                                                    <?php
                                                    foreach ($menuGroupList as $menuGroup) {
                                                        echo "<option value='{$menuGroup['menuGroupId']}'>{$menuGroup['groupName']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="editItemName">Item Name:</label>
                                                <input type="text" class="form-control" name="itemName" id="editItemName" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="editItemPrice">Item Price:</label>
                                                <input type="text" class="form-control" name="itemPrice" id="editItemPrice" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="editDescription">Item Description:</label>
                                                <textarea class="form-control" name="description" id="editDescription" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="editItemImage">Upload New Image (optional):</label>
                                                <input type="file" class="form-control-file" name="itemImage" id="editItemImage">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="editMenuItem" class="btn btn-primary">Save changes</button>
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
                var menuitemId = $(this).data('id');
                var menuGroupId = $(this).data('group');
                var itemName = $(this).data('name');
                var itemPrice = $(this).data('price');
                var description = $(this).data('description');
                var itemImage = $(this).data('image');

                // Populate the edit modal fields with the current item data
                $('#editMenuItemId').val(menuitemId);
                $('#editItemName').val(itemName);
                $('#editItemPrice').val(itemPrice);
                $('#editDescription').val(description);
                $('#currentImage').val(itemImage);

                // Pre-select the correct menu group based on menuGroupId
                $('#editMenuGroupId option').each(function() {
                    if ($(this).val() == menuGroupId) {
                        $(this).prop('selected', true);
                    }
                });

                $('#editMenuItemModal').modal('show');
            });
        });
    </script>

</body>
</html>
