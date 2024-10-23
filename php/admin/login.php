<?php
session_start(); // Start session if needed
include 'db.php'; // Include your DB connection file

// Initialize error variables
$usernameError = $passwordError = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username)) {
        $usernameError = "Username is required.";
    }

    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    if (empty($usernameError) && empty($passwordError)) {
        // Check if the username exists in the database
        $sql = "SELECT * FROM employee WHERE Username='$username'";
        $result = mysqli_query($conn, $sql);
        $rows = mysqli_num_rows($result);

        if ($rows == 0) {
            $usernameError = "Username does not exist!";
        } else {
            $record = mysqli_fetch_assoc($result);
            $hash_pw = $record['Password'];

            // Verify password
            if (!password_verify($password, $hash_pw)) {
                $passwordError = "Incorrect password.";
            } else {
                // Successful login - Store details in session
                $_SESSION['username'] = $record['Username'];
                $_SESSION['role'] = $record['Role']; 
                $_SESSION['employeeName'] = $record['employeeName']; 
                $_SESSION['profileImg'] = $record['profileImg']; 

                // Successful login
                echo '<script>alert("Login successful!");</script>';
                header('Location: dashboard.php'); // Redirect to dashboard
                exit();
            }
        }
    }
}
?>

<!-- HTML Form for Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS from RegisterEmployeeCss -->
    <title>Employee Login</title>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <img src="../../image/logo/mimielogo.png" alt="Logo" class="img-fluid mb-4" style="max-width: 75%;">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-teal text-white">
                        <h3>Employee Login</h3>
                    </div>
                    <div class="card-body">
                        <form action="login.php" method="POST" id="loginForm">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" placeholder="Enter your username" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                <?php if (!empty($usernameError)): ?>
                                    <small id="usernameError" class="error-text" style="color: red;"><?php echo $usernameError; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password"  placeholder="Enter your password"  class="form-control" id="password" name="password" required>
                                <?php if (!empty($passwordError)): ?>
                                    <small id="passwordError" class="error-text" style="color: red;"><?php echo $passwordError; ?></small>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>

                            <!-- Create Account Link -->
                            <div class="text-center mt-3">
                                <a href="register.php" class="text-teal">Don't have an account? Create one here</a>
                            </div>
                        </form>
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
