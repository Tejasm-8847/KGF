<?php
// contact_form.php

// Database connection settings
$servername = "localhost"; // replace with your server name (e.g., localhost)
$username = "root";        // replace with your database username
$password = "";            // replace with your database password
$dbname = "kgf";           // replace with your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for any connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Prepare the SQL query to insert the data into the Demo table
    $sql = "INSERT INTO Demo (Name, PhoneNumber, Subject, Message) VALUES ('$name', '$email', '$subject', '$message')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        $success_message = "Your message has been sent successfully!";
    } else {
        $error_message = "Your message has been sent successfully!";
    }

    // Close the database connection
    $conn->close();
}
?>