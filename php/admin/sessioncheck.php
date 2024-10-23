<?php
    session_start();
    include 'db.php'; // Include your DB connection file

    // Check if the employee is logged in and if the role is 'staff'
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
        header("Location: login.php"); // Redirect to login page if not staff
        exit();
    }
?>