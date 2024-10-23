<?php
include 'sessioncheck.php'; // Check session staff

// Fetch all menus for the combo box
$menu_sql = "SELECT * FROM menu";
$menu_result = $conn->query($menu_sql);

// Fetch the menugroups if a menu is selected
$menuId = isset($_POST['menuId']) ? $_POST['menuId'] : null;
$menugroup_sql = $menuId ? "SELECT * FROM menugroup WHERE menuId = '$menuId'" : "";
$menugroup_result = $menuId ? $conn->query($menugroup_sql) : null;

// Fetch the menuitems if a menugroup is selected
$menuGroupId = isset($_POST['menuGroupId']) ? $_POST['menuGroupId'] : null;
$menuitem_sql = $menuGroupId ? "SELECT * FROM menuitem WHERE menuGroupId = '$menuGroupId'" : "";
$menuitem_result = $menuGroupId ? $conn->query($menuitem_sql) : null;

// Keep the selected promotionId
$promotionId = isset($_POST['promotionId']) ? $_POST['promotionId'] : null;

// Function to generate a new promotion item ID
function generatePromotionItemID($conn) {
    $sql = "SELECT promotionitemId FROM promotionitem ORDER BY promotionitemId DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_id = $row['promotionitemId'];
        $num = (int) substr($last_id, 3) + 1;
        return "PI-" . str_pad($num, 4, "0", STR_PAD_LEFT);
    } else {
        return "PI-0001";
    }
}

// Handle form submission for adding new promotion items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPromotionItem'])) {
    $promotionId = $_POST['promotionId'];
    $menuitemId = $_POST['menuitemId'];
    $promoDescription = $_POST['promoDescription'];
    $promotionItemId = generatePromotionItemID($conn); // Generate a new promotion item ID

    // Insert the new promotion item into the database
    $sql = "INSERT INTO promotionitem (promotionitemId, promotionId, menuitemId, promoDescription) VALUES ('$promotionItemId', '$promotionId', '$menuitemId', '$promoDescription')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New promotion item added successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle deletion of promotion item
if (isset($_GET['deletePromotionItem'])) {
    $promotionitemId = $_GET['deletePromotionItem'];
    $sql = "DELETE FROM promotionitem WHERE promotionitemId = '$promotionitemId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Promotion item deleted successfully!');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Search functionality
$searchQuery = "";
if (isset($_GET['searchQuery'])) {
    $searchQuery = $_GET['searchQuery'];
    $sql = "SELECT p.promotionName, m.itemName, pi.promotionitemId, pi.promoDescription 
            FROM promotionitem pi
            JOIN promotion p ON pi.promotionId = p.promotionId
            JOIN menuitem m ON pi.menuitemId = m.menuitemId
            WHERE p.promotionName LIKE '%$searchQuery%' 
            OR m.itemName LIKE '%$searchQuery%' 
            OR pi.promoDescription LIKE '%$searchQuery%'";
} else {
    $sql = "SELECT p.promotionName, m.itemName, pi.promotionitemId, pi.promoDescription 
            FROM promotionitem pi
            JOIN promotion p ON pi.promotionId = p.promotionId
            JOIN menuitem m ON pi.menuitemId = m.menuitemId";
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
    <title>Promotion Item Management</title>
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
                        <h3>Promotion Item Management</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to Add a New Promotion Item -->
                        <h4>Add New Promotion Item</h4>
                        <form method="POST" action="">
                            <!-- Promotion Combo Box -->
                            <div class="form-group">
                                <label for="promotionId">Promotion:</label>
                                <select class="form-control" id="promotionId" name="promotionId" required>
                                    <option value="">Select Promotion</option>
                                    <?php
                                    $promotion_sql = "SELECT * FROM promotion";
                                    $promotion_result = $conn->query($promotion_sql);
                                    while ($row = $promotion_result->fetch_assoc()) {
                                        $selected = ($row['promotionId'] == $promotionId) ? 'selected' : ''; // Check if promotion is selected
                                        echo "<option value='{$row['promotionId']}' $selected>{$row['promotionName']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Menu Combo Box -->
                            <div class="form-group">
                                <label for="menuId">Menu:</label>
                                <select class="form-control" id="menuId" name="menuId" onchange="this.form.submit()">
                                    <option value="">Select Menu</option>
                                    <?php while ($row = $menu_result->fetch_assoc()) : ?>
                                        <option value="<?php echo $row['menuid']; ?>" <?php if (isset($menuId) && $menuId == $row['menuid']) echo 'selected'; ?>>
                                            <?php echo $row['menuName']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Menu Group Combo Box -->
                            <div class="form-group">
                                <label for="menuGroupId">Menu Group:</label>
                                <select class="form-control" id="menuGroupId" name="menuGroupId" onchange="this.form.submit()">
                                    <option value="">Select Menu Group</option>
                                    <?php if ($menugroup_result): ?>
                                        <?php while ($row = $menugroup_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $row['menuGroupId']; ?>" <?php if (isset($menuGroupId) && $menuGroupId == $row['menuGroupId']) echo 'selected'; ?>>
                                                <?php echo $row['groupName']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Menu Item Combo Box -->
                            <div class="form-group">
                                <label for="menuitemId">Menu Item:</label>
                                <select class="form-control" id="menuitemId" name="menuitemId">
                                    <option value="">Select Menu Item</option>
                                    <?php if ($menuitem_result): ?>
                                        <?php while ($row = $menuitem_result->fetch_assoc()) : ?>
                                            <option value="<?php echo $row['menuitemId']; ?>">
                                                <?php echo $row['itemName']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Promotion Description -->
                            <div class="form-group">
                                <label for="promoDescription">Promotion Description:</label>
                                <textarea class="form-control" id="promoDescription" name="promoDescription" rows="3" required></textarea>
                            </div>

                            <button type="submit" name="addPromotionItem" class="btn btn-primary">Add Promotion Item</button>
                        </form>
                        <hr>

                        <!-- Display the Promotion Items Table -->
                        <h4>Current Promotion Items</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Promotion Item ID</th>
                                    <th>Promotion Name</th>
                                    <th>Menu Item</th>
                                    <th>Description</th>
                                    <th>Actions</th> <!-- New Actions column for delete -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Check if there are any promotion items and display them
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td>{$row['promotionitemId']}</td>
                                                <td>{$row['promotionName']}</td>
                                                <td>{$row['itemName']}</td>
                                                <td>{$row['promoDescription']}</td>
                                                <td>
                                                    <a href='?deletePromotionItem={$row['promotionitemId']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this promotion item?\");'>Delete</a>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No promotion items found</td></tr>";
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
