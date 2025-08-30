<?php
// Start the session to manage user login state
session_start();

// Assuming you have a database connection
$servername = "localhost"; // your database server
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "kgf"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to find user by email
    $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'"; // Assuming simple password check, make sure to hash passwords in production
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Success, user found
        $_SESSION['email'] = $email; // Set session variable to track login state

        // Redirect to the admin dashboard (admin_main.php)
        header("Location: ../admin_main.php");
        exit(); // Ensure no further code is executed after redirection
    } else {
        // Invalid login
        echo "<script>alert('Invalid email or password'); window.location.href='../admin_login.php';</script>";
        exit(); // Stop further code execution
    }
}

// Close the database connection
$conn->close();
?>
