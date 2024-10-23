<?php
include 'sessioncheck.php'; // Include your DB connection file

// Function to generate a new menuGroup ID, starting from MG-0001
function generateMenuGroupID($conn) {
    $sql = "SELECT menuGroupId FROM menugroup ORDER BY menuGroupId DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['menuGroupId'];
        $num = (int) substr($last_id, 3) + 1; // Increment last ID number by 1
        return "MG-" . str_pad($num, 4, "0", STR_PAD_LEFT);
    } else {
        return "MG-0001"; // Start with MG-0001 if no records exist
    }
}

// Fetch all menu IDs and names for the dropdown
$sql_menu = "SELECT menuid, menuName FROM menu";
$result_menu = $conn->query($sql_menu);
$menuList = [];
if ($result_menu->num_rows > 0) {
    while ($row = $result_menu->fetch_assoc()) {
        $menuList[] = $row;
    }
}

// Handle form submission for adding new menu groups
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addMenuGroup'])) {
    $menuId = $_POST['menuId'];
    $groupName = $_POST['groupName'];
    $menuGroupId = generateMenuGroupID($conn); // Generate a new menu group ID

    // Insert the new menu group into the database
    $sql = "INSERT INTO menugroup (menuGroupId, menuId, groupName) VALUES ('$menuGroupId', '$menuId', '$groupName')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New menu group added successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle form submission for deleting menu groups
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteMenuGroup'])) {
    $menuGroupId = $_POST['menuGroupId']; // Get the menu group ID to delete

    // Delete the menu group from the database
    $sql = "DELETE FROM menugroup WHERE menuGroupId='$menuGroupId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Menu group deleted successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle form submission for editing menu groups
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editMenuGroup'])) {
    $menuGroupId = $_POST['menuGroupId'];
    $menuId = $_POST['menuId'];
    $groupName = $_POST['groupName'];

    // Update the menu group in the database
    $sql = "UPDATE menugroup SET menuId='$menuId', groupName='$groupName' WHERE menuGroupId='$menuGroupId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Menu group updated successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle search query
$searchQuery = "";
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT mg.menuGroupId, m.menuName, mg.groupName 
            FROM menugroup mg
            JOIN menu m ON mg.menuId = m.menuid 
            WHERE mg.groupName LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT mg.menuGroupId, m.menuName, mg.groupName 
            FROM menugroup mg
            JOIN menu m ON mg.menuId = m.menuid";
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
    <title>Menu Group Management</title>
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
                        <h3>Menu Group Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to Add a New Menu Group -->
                        <h4>Add New Menu Group</h4>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="menuId">Select Menu:</label>
                                <select class="form-control" id="menuId" name="menuId" required>
                                    <option value="">-- Select Menu --</option>
                                    <?php
                                    foreach ($menuList as $menu) {
                                        echo "<option value='{$menu['menuid']}'>{$menu['menuName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="groupName">Group Name:</label>
                                <input type="text" class="form-control" id="groupName" name="groupName" required>
                            </div>
                            <button type="submit" name="addMenuGroup" class="btn btn-primary">Add Menu Group</button>
                        </form>
                        <hr>

                        <!-- Display the Menu Groups -->
                        <h4>Current Menu Groups</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu Group ID</th>
                                    <th>Menu Name</th>
                                    <th>Group Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Display all or filtered menu groups
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['menuGroupId']}</td>
                                                <td>{$row['menuName']}</td>
                                                <td>{$row['groupName']}</td>
                                                <td>
                                                    <button class='btn btn-warning btn-sm editBtn' data-id='{$row['menuGroupId']}' data-menuid='{$row['menuName']}' data-groupname='{$row['groupName']}'>Edit</button>
                                                    <form method='POST' action='' style='display:inline-block;'>
                                                        <input type='hidden' name='menuGroupId' value='{$row['menuGroupId']}'>
                                                        <button type='submit' name='deleteMenuGroup' class='btn btn-danger btn-sm'>Delete</button>
                                                    </form>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No menu groups found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Edit Menu Group Modal -->
                        <div class="modal fade" id="editMenuGroupModal" tabindex="-1" role="dialog" aria-labelledby="editMenuGroupModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editMenuGroupModalLabel">Edit Menu Group</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="menuGroupId" id="editMenuGroupId">
                                            <div class="form-group">
                                                <label for="editMenuId">Select Menu:</label>
                                                <select class="form-control" name="menuId" id="editMenuId" required>
                                                    <option value="">-- Select Menu --</option> <!-- Default option -->
                                                    <?php
                                                    foreach ($menuList as $menu) {
                                                        echo "<option value='{$menu['menuid']}'>{$menu['menuName']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="editGroupName">Group Name:</label>
                                                <input type="text" class="form-control" name="groupName" id="editGroupName" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="editMenuGroup" class="btn btn-primary">Save changes</button>
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
                var menuGroupId = $(this).data('id');
                var menuId = $(this).data('menuid');
                var groupName = $(this).data('groupname');

                // Set the values in the modal
                $('#editMenuGroupId').val(menuGroupId);
                $('#editGroupName').val(groupName);

                // Reset the dropdown and add the default placeholder
                $('#editMenuId').val('');
                $('#editMenuId option').each(function() {
                    if ($(this).val() == menuId) {
                        $(this).prop('selected', true);
                    }
                });

                $('#editMenuGroupModal').modal('show');
            });
        });
    </script>

</body>
</html>
