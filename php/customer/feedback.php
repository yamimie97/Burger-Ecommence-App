<?php
include 'header.php'; // Include header
include 'db.php';     // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    // If not logged in, redirect to login page
    header("Location: signin.php");
    exit();
}
    // Function to generate customer ID
    function generateFeedbackID($conn) {
        $sql = "SELECT feedbackId FROM feedback ORDER BY feedbackId DESC LIMIT 1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_id = $row['feedbackId'];
            $num = (int) substr($last_id, 4) + 1; // Increment the last ID number
            return "Cus-" . str_pad($num, 5, "0", STR_PAD_LEFT);
        } else {
            return "FDB-00001"; // If no records, start with Cus-00001
        }
    }

// Get logged-in customer ID from session
$customerID = $_SESSION['customerId'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comments = $_POST['comments'];
    $rating = $_POST['rating'];

    // Validate the form inputs
    if (!empty($comments) && !empty($rating)) {
        // Insert the feedback into the database
        $feedbackId = generateFeedbackID($conn);
        $feedback_query = "INSERT INTO feedback (feedbackId, customerId, comments, rating) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($feedback_query);
        $stmt->bind_param("sssi", $feedbackId, $customerID, $comments, $rating); // "s" for string, "i" for integer

        if ($stmt->execute()) {
            $successMessage = "Thank you for your feedback!";
        } else {
            $errorMessage = "Error submitting feedback. Please try again.";
        }

        $stmt->close();
    } else {
        $errorMessage = "All fields are required.";
    }
}
?>

<style>
    .feedback-form {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .feedback-form input, .feedback-form textarea, .feedback-form button {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .feedback-form button {
        background-color: teal;
        color: white;
        border: none;
        cursor: pointer;
    }

    .feedback-form button:hover {
        background-color: #006d6d;
    }

    .error-text {
        color: red;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .success-text {
        color: green;
        font-size: 14px;
        margin-bottom: 15px;
    }
    h2 {
        text-align: center;
    }
</style>

<section class="feedback-page">
    <div class="container">
        <h2>Submit Your Feedback</h2>
        
        <!-- Feedback Form -->
        <div class="feedback-form">
            <form action="feedback.php" method="POST">
                <label for="comments">Comments</label>
                <textarea placeholder="Enter your feedback here" name="comments" rows="5" required></textarea>

                <label for="rating">Rating (1 to 5)</label>
                <input type="number" name="rating" min="1" max="5" required>

                <button type="submit">Submit Feedback</button>

                <?php if (!empty($errorMessage)) { ?>
                    <p class="error-text"><?php echo $errorMessage; ?></p>
                <?php } ?>

                <?php if (!empty($successMessage)) { ?>
                    <p class="success-text"><?php echo $successMessage; ?></p>
                <?php } ?>
            </form>
        </div>
    </div>

</section>

<?php
include 'footer.php';
$conn->close();
?>
