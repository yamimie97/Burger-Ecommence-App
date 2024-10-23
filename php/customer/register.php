<?php
    include 'header.php'; // Include header
    include 'db.php'; // Include your DB connection file

    // Initialize error variables
    $usernameError = $passwordError = $confirmPasswordError = $nameError = $emailError = "";

    // Function to generate customer ID
    function generateCustomerID($conn) {
        $sql = "SELECT customerId FROM customer ORDER BY customerId DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row['customerId'];
            $num = (int) substr($last_id, 4) + 1; // Increment the last ID number
            return "Cus-" . str_pad($num, 5, "0", STR_PAD_LEFT);
        } else {
            return "Cus-00001"; // If no records, start with Cus-00001
        }
    }
    $successMessage = "";  // Initialize success message

    // Function to generate address ID
    function generateAddressID($conn) {
        $sql = "SELECT addressId FROM addresses ORDER BY addressId DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row['addressId'];
            $num = (int) substr($last_id, 4) + 1; // Increment the last ID number
            return "Adr-" . str_pad($num, 5, "0", STR_PAD_LEFT);
        } else {
            return "Adr-00001"; // If no records, start with Adr-00001
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phoneNumber = $_POST['phoneNumber'];
        $address = $_POST['address'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        $isFormValid = true;

        // Check for valid name format (no numbers or special characters)
        if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
            $nameError = "Name must only contain letters and spaces.";
            $isFormValid = false;
        }

        // Check for valid email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format.";
            $isFormValid = false;
        }

        // Check if username already exists in the database
        $sql_check_username = "SELECT * FROM customer WHERE username = ?";
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
            $customerID = generateCustomerID($conn);

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Handle profile image upload
            $profile_name = $_FILES['profile']['name'];
            $profile_tmp_name = $_FILES['profile']['tmp_name'];
            $path = "../../image/profile/" . $profile_name;
            copy($profile_tmp_name, $path);

            // Insert into database
            $sql = "INSERT INTO customer (customerId, name, email, phoneNumber, address, profileImage, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $customerID, $name, $email, $phoneNumber, $address, $path, $username, $hashed_password);

            if ($stmt->execute()) {
                // Insert the address into the addresses table
                $addressID = generateAddressID($conn);
                $sql_address = "INSERT INTO addresses (addressId, customerId, addresses) VALUES (?, ?, ?)";
                $stmt_address = $conn->prepare($sql_address);
                $stmt_address->bind_param("sss", $addressID, $customerID, $address);
    
                if ($stmt_address->execute()) {
                    $successMessage = "Customer registered successfully!";
                    $_POST = array(); // Clear the form inputs
                } else {
                    echo "Error adding address: " . $conn->error;
                }
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
?>

<!-- HTML Form for Customer Registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/custom.css"> <!-- Custom CSS -->
    <title>Customer Registration</title>
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
                    <h2 class="signin-title text-center">Create New Account</h2>

                    <div class="card-body">
                        <form action="register.php" method="POST" enctype="multipart/form-data" id="registrationForm">
                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" class="form-control" placeholder="Enter your name" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                <small id="nameError" class="error-text"><?php echo $nameError; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" placeholder="Enter your email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                <small id="emailError" class="error-text"><?php echo $emailError; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="phoneNumber">Phone Number:</label>
                                <input type="text" class="form-control" placeholder="Enter your phone number" id="phoneNumber" name="phoneNumber" value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" placeholder="Enter your address" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="profile">Profile Image:</label>
                                <input type="file" class="form-control-file" id="profile" name="profile" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Username:</label>
                                <input type="text" class="form-control" placeholder="Enter your username" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                                <small id="usernameError" class="error-text"><?php echo $usernameError; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" placeholder="Enter your password" id="password" name="password" required>
                                <small id="passwordError" class="error-text"><?php echo $passwordError; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password:</label>
                                <input type="password" class="form-control" placeholder="Confirm your password " id="confirmPassword" name="confirmPassword" required>
                                <small id="confirmPasswordError" class="error-text"><?php echo $confirmPasswordError; ?></small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                            <!-- Login Link -->
                            <div class="text-center mt-3">
                                <a href="signin.php" class="text-teal">Already have an account? Login here</a>
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

    <!-- JavaScript for Real-Time Validation and on Submit -->
    <script>
        // Real-time validation for name
        document.getElementById('name').addEventListener('input', function () {
            const nameField = document.getElementById('name');
            const nameError = document.getElementById('nameError');
            const namePattern = /^[a-zA-Z\s]+$/;

            if (!namePattern.test(nameField.value)) {
                nameError.style.display = 'block';
            } else {
                nameError.style.display = 'none';
            }
        });

        // Real-time validation for username
        document.getElementById('username').addEventListener('input', function () {
            const usernameField = document.getElementById('username');
            const usernameError = document.getElementById('usernameError');
            const usernamePattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d]{6,15}$/;

            if (!usernamePattern.test(usernameField.value)) {
                usernameError.style.display = 'block';
            } else {
                usernameError.style.display = 'none';
            }
        });

        // Password format validation on submit only
        document.getElementById("registrationForm").addEventListener("submit", function (event) {
            const passwordField = document.getElementById("password");
            const passwordError = document.getElementById("passwordError");

            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,17}$/;

            // Check password format when the Register button is clicked
            if (!passwordPattern.test(passwordField.value)) {
                event.preventDefault();  // Prevent form submission
                passwordError.style.display = 'block';
                passwordError.textContent = "Password must contain at least one uppercase letter, one lowercase letter, one number, and be between 8-17 characters.";
            } else {
                passwordError.style.display = 'none';  // Hide error if format is correct
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
        //Reset Form and Handle Success Message
        document.addEventListener("DOMContentLoaded", function() {
            const successMessage = "<?php echo $successMessage; ?>";
            if (successMessage) {
                document.getElementById("registrationForm").reset(); // Clear form inputs
            }
        });
    </script>
    </script>
</body>
</html>
<?php
include 'footer.php'; // Include footer
$conn->close();
?>