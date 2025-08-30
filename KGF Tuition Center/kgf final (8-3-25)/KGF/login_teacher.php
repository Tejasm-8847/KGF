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

    // Query to check if the email exists in the TeacherLogin table
    $sql = "SELECT * FROM TeacherLogin WHERE Email = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password); // Bind email and password
    $stmt->execute();
    $result = $stmt->get_result();

    // If a matching teacher is found
    if ($result->num_rows > 0) {
        // Fetch teacher data (optional, to store teacher details in the session)
        $teacher = $result->fetch_assoc();

        // Start a session and store the teacher data
        $_SESSION['teacher_logged_in'] = true;
        $_SESSION['teacher_id'] = $teacher['TeacherID']; // Assuming TeacherID is the primary key
        $_SESSION['teacher_email'] = $teacher['Email'];

        // Redirect to teacher_main_pg.php
        header('Location: teacher_main_pg.php');
        exit;
    } else {
        // If no matching teacher, redirect back with error
        header('Location: teacher_login.php?error=Invalid email or password');
        exit;
    }
}

$conn->close();
?>
