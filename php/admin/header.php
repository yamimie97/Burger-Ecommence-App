<?php
// Access the session variables
$profileImg = $_SESSION['profileImg'];
$username = $_SESSION['username'];
$employeeName = $_SESSION['employeeName'];
$role = $_SESSION['role']; // 'staff' or other roles
?>

<div class="container-fluid">
    <!-- Header with logo, search bar, profile info -->
    <div class="row align-items-center mb-3">
        <div class="col-md-3 text-center">
            <img src="../../image/logo/mimielogo.png" alt="Logo" class="img-fluid" style="max-width: 250px;">
        </div>

        <div class="col-md-9">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="searchQuery" placeholder="Search...">
                            <div class="input-group-append">
                                <button class="btn btn-teal" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="col-md-3 text-right">
                    <div class="d-inline-block mr-2">
                        <h5><?php echo $employeeName; ?></h5>
                    </div>
                    <div class="d-inline-block">
                        <img src="../../image/profile/<?php echo $profileImg; ?>" alt="Profile Image" class="img-fluid rounded-circle" style="max-width: 60px;">
                    </div>
                    <a href="logout.php" class="logout-link">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
