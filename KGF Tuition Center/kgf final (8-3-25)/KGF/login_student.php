<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Update with your MySQL username
$password = ""; // Update with your MySQL password
$dbname = "kgf"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the email exists in the Student table
    $sql = "SELECT * FROM Student WHERE Email = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password); // Bind email and password
    $stmt->execute();
    $result = $stmt->get_result();

    // If a matching student is found
    if ($result->num_rows > 0) {
        // Fetch student data (optional, to store student details in the session)
        $student = $result->fetch_assoc();

        // Start a session and store the student data
        $_SESSION['student_logged_in'] = true;
        $_SESSION['student_id'] = $student['StudentID']; // Assuming StudentID is the primary key
        $_SESSION['student_email'] = $student['Email'];

        // Redirect to student_main_pg.php (or whatever page the student should land on)
        header('Location: student_main_pg.php');
        exit;
    } else {
        // If no matching student, redirect back with error
        header('Location: student_login.php?error=Invalid email or password');
        exit;
    }
}

$conn->close();
?>
