<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "kgf"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the email already exists in the database
$email = 'admin@kgf.com'; // You can change this as needed
$plainPassword = 'admin'; // The password to be hashed

// Check if email exists
$sql_check = "SELECT * FROM Admin WHERE Email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email); // Bind email
$stmt_check->execute();
$result_check = $stmt_check->get_result();

// If email exists, do not insert again
if ($result_check->num_rows == 0) {
    // Hash the password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Insert new admin into the table
    $sql_insert = "INSERT INTO Admin (Email, Password) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $email, $hashedPassword); // Bind email and hashed password
    if ($stmt_insert->execute()) {
        echo "Admin user inserted successfully.";
    } else {
        echo "Error inserting admin user: " . $conn->error;
    }
} else {
    echo "Email already exists. Skipping insertion.";
}

// Check if form is submitted for login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $inputPassword = $_POST['password']; // This is the entered password

    // Query to check if the email exists in the admin table
    $sql = "SELECT * FROM admin WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Bind email
    $stmt->execute();
    $result = $stmt->get_result();

    // If a matching user is found
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Retrieve the hashed password from the database
        $storedHashedPassword = $user['Password']; // The hashed password stored in the database

        // Verify the entered password with the hashed password using password_verify()
        if (password_verify($inputPassword, $storedHashedPassword)) {
            // Password is correct, store user data in the session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $user['Name'];  // Assuming column 'Name' exists
            $_SESSION['admin_email'] = $user['Email'];  // Assuming column 'Email' exists

            // Redirect to admin_main.php
            header('Location: admin_main.php');
            exit;
        } else {
            // Incorrect password, redirect back with error
            header('Location: admin_login.php?error=Invalid email or password');
            exit;
        }
    } else {
        // If no matching user, redirect back with error
        header('Location: admin_login.php?error=Invalid email or password');
        exit;
    }
}

$conn->close();
?>
