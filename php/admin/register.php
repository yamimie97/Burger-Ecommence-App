<?php
    include 'db.php'; // Include your DB connection file

    // Initialize error variables
    $usernameError = $passwordError = $confirmPasswordError = $nameError = "";

    // Function to check employee ID and generate new ID
    function generateEmployeeID($conn) {
        $sql = "SELECT employeeID FROM employee ORDER BY employeeID DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row['employeeID'];
            $num = (int) substr($last_id, 2) + 1; // Increment the last ID number
            return "E-" . str_pad($num, 4, "0", STR_PAD_LEFT);
        } else {
            return "E-0001"; // If no records, start with E-0001
        }
    }
    $successMessage = "";  // Initialize success message

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        $isFormValid = true;

        // Check for valid name format (no numbers or special characters)
        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $nameError = "Name must only contain letters and spaces.";
            $isFormValid = false;
        }

        // Check if username already exists in the database
        $sql_check_username = "SELECT * FROM employee WHERE username = ?";
        $stmt = $conn->prepare($sql_check_username);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usernameError = "Username already exists. Please choose a different one.";
            $isFormValid = false;
        }

        // Check for valid username format (must include at least one uppercase, one lowercase, one number, and be 6-15 characters)
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d]{6,15}$/', $username)) {
            $usernameError = "Username must contain at least one uppercase letter, one lowercase letter, one number, and be between 6-15 characters.";
            $isFormValid = false;
        }

        // Check for valid password format
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,17}$/', $password)) {
            $passwordError = "Password must contain at least one uppercase letter, one lowercase letter, one number, and be between 8-17 characters.";
            $isFormValid = false;
        }

        // Check if password and confirm password match
        if ($password !== $confirmPassword) {
            $confirmPasswordError = "Passwords do not match.";
            $isFormValid = false;
        }

        if ($isFormValid) {
            $employeeID = generateEmployeeID($conn);
            $role = 'staff';

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Handle profile image upload
            $profile_name = $_FILES['profile']['name'];
            $profile_tmp_name = $_FILES['profile']['tmp_name'];
            $path = "../../image/profile/" . $profile_name;
            copy($profile_tmp_name, $path);

            // Insert into database
            $sql = "INSERT INTO employee (employeeId, employeeName, role, profileImg, username, password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $employeeID, $name, $role, $path, $username, $hashed_password);

            if ($stmt->execute()) {
                $successMessage = "Employee registered successfully!";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
?>

<!-- HTML Form for Admin Registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS -->
    <title>Admin Registration</title>
    <style>
        .error-text {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="../../image/logo/mimielogo.png" alt="Logo" class="img-fluid" style="max-width: 90%; height: auto;">
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-teal text-white">
                        <h3>Admin Register</h3>
                    </div>
                    <div class="card-body">
                        <form action="register.php" method="POST" enctype="multipart/form-data" id="registrationForm">
                            <div class="form-group">
                                <label for="name">Employee Name:</label>
                                <input type="text" placeholder="Enter your name" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                <small id="nameError" class="error-text" style="display: none;">Name must only contain letters and spaces.</small>
                            </div>
                            <div class="form-group">
                                <label for="profile">Profile Image:</label>
                                <input type="file" class="form-control-file" id="profile" name="profile" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" placeholder="Enter your username" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                <?php if (!empty($usernameError)): ?>
                                    <small id="usernameError" class="error-text"><?php echo $usernameError; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" placeholder="Enter your password" class="form-control" id="password" name="password" required>
                                <small id="passwordError" class="error-text" style="display: none;">Password must contain at least one uppercase letter, one lowercase letter, one number, and be between 8-17 characters.</small>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password:</label>
                                <input type="password" placeholder="Confirm your password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                <small id="confirmPasswordError" class="error-text" style="display: none;">Passwords do not match.</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                            <!-- Login Link -->
                            <div class="text-center mt-3">
                                <a href="login.php" class="text-teal">Already have an account? Login here</a>
                            </div>
                            <div id="successMessage" class="alert alert-success text-center" style="display: none;">
                                <?php echo $successMessage; ?>
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

    <script>
    // Real-time validation for confirm password
    document.getElementById('confirmPassword').addEventListener('input', function () {
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirmPassword');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordError.style.display = 'block';
            confirmPasswordError.textContent = "Passwords do not match.";
        } else {
            confirmPasswordError.style.display = 'none';
        }
    });

    // Password format validation on submit only
    document.getElementById("registrationForm").addEventListener("submit", function (event) {
        const passwordField = document.getElementById("password");
        const passwordError = document.getElementById("passwordError");
        const confirmPasswordField = document.getElementById('confirmPassword');
        const confirmPasswordError = document.getElementById('confirmPasswordError');

        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,17}$/;

        // Check password format when the Register button is clicked
        if (!passwordPattern.test(passwordField.value)) {
            event.preventDefault();  // Prevent form submission
            passwordError.style.display = 'block';
            passwordError.textContent = "Password must contain at least one uppercase letter, one lowercase letter, one number, and be between 8-17 characters.";
        } else {
            passwordError.style.display = 'none';  // Hide error if format is correct
        }

        // Check if confirm password matches the password
        if (passwordField.value !== confirmPasswordField.value) {
            event.preventDefault();  // Prevent form submission
            confirmPasswordError.style.display = 'block';
            confirmPasswordError.textContent = "Passwords do not match.";
        } else {
            confirmPasswordError.style.display = 'none';  // Hide error if they match
        }
    });

    // Hide password error when the user starts typing after an error
    document.getElementById('password').addEventListener('input', function () {
        const passwordError = document.getElementById('passwordError');
        const passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,17}$/;

        // Only hide the error if the password is valid after typing
        if (passwordPattern.test(this.value)) {
            passwordError.style.display = 'none';  // Hide error if the format is correct after typing
        }
    });

    // Show success message if it exists
    document.addEventListener("DOMContentLoaded", function () {
        const successMessage = "<?php echo $successMessage; ?>";
        if (successMessage) {
            document.getElementById("successMessage").style.display = 'block';
        }
    });

    // Hide success message when typing in any input field
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            document.getElementById("successMessage").style.display = 'none';
        });
    });

    // Hide username error when user starts typing
    document.getElementById('username').addEventListener('input', function () {
        const usernameError = document.getElementById('usernameError');
        if (usernameError) {
            usernameError.style.display = 'none';
        }
    });

</script>

</body>
</html>
