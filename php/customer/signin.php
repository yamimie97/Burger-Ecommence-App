<?php
include 'header.php';
include 'db.php'; // Include your DB connection file

// Initialize error variables
$usernameError = $passwordError = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (empty($username)) {
        $usernameError = "Username is required.";
    }
    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    if (empty($usernameError) && empty($passwordError)) {
        // Check if the username exists in the database
        $sql = "SELECT * FROM customer WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $usernameError = "Username does not exist!";
        } else {
            $record = $result->fetch_assoc();
            $hash_pw = $record['password']; // Hashed password from DB

            // Debugging: Ensure you're retrieving the correct hashed password
            // echo "Hashed password from DB: " . $hash_pw;

            // Verify the entered password with the hashed password from the database
            if (!password_verify($password, $hash_pw)) {
                $passwordError = "Incorrect password.";
            } else {
                // Successful login - Store details in session
                $_SESSION['username'] = $record['username'];
                $_SESSION['customerId'] = $record['customerId']; 
                $_SESSION['name'] = $record['name']; 
                $_SESSION['profileImage'] = $record['profileImage']; 

                // Redirect to homepage after successful login
                header('Location: index.php');
                exit();
            }
        }
    }
}
?>

<!-- HTML Form for Customer Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS from RegisterEmployeeCss -->
    <title>Customer Login</title>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <h2 class="signin-title text-center">Login</h2>
                    <div class="card-body">
                        <form action="signin.php" method="POST" id="loginForm">
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" placeholder="Enter your username" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                <?php if (!empty($usernameError)): ?>
                                    <small id="usernameError" class="error-text" style="color: red;"><?php echo $usernameError; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" placeholder="Enter your password" class="form-control" id="password" name="password" required>
                                <?php if (!empty($passwordError)): ?>
                                    <small id="passwordError" class="error-text" style="color: red;"><?php echo $passwordError; ?></small>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>

                            <!-- Create Account Link -->
                            <div class="text-center mt-3">
                                <a href="register.php" class="text-teal">Don't have an account? Register here</a>
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

<?php
include 'footer.php'; // Include footer
$conn->close();
?>
