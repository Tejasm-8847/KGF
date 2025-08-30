<?php
// Start the session
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacher_logged_in']) || !$_SESSION['teacher_logged_in']) {
    // If the teacher is not logged in, redirect to the login page
    header("Location: teacher_login.php");
    exit;
}

// Retrieve teacher information from session
$teacher_id = $_SESSION['teacher_id']; // TeacherID from the session
$teacher_email = $_SESSION['teacher_email']; // Teacher email from the session

// Log out the user if the logout link is clicked
if (isset($_GET['logout'])) {
    session_unset();  // Clear all session variables
    session_destroy();  // Destroy the session
    header('Location: teacher_login.php');  // Redirect to login page
    exit();
}

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

// Validate TeacherID from the session (Check if teacher exists in TeacherLogin table)
$teacher_check_sql = "SELECT * FROM TeacherLogin WHERE TeacherID = ?";
$stmt = $conn->prepare($teacher_check_sql);
$stmt->bind_param("i", $teacher_id);  // Use 'i' for integer (TeacherID is an INT)
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // TeacherID does not exist, log out the user
    session_unset();  // Clear all session variables
    session_destroy();  // Destroy the session
    header("Location: teacher_login.php?error=Invalid teacher credentials");
    exit();
}

// Handle form submission to send notification
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_notification'])) {
  $subject = $_POST['subject'];
  $content = $_POST['content'];

  // Get the current date in the format YYYY-MM-DD for MySQL
  $current_date = date('Y-m-d');  // Format: YYYY-MM-DD

  // Insert the notification into the Student_Notification table with current date
  $stmt = $conn->prepare("INSERT INTO Student_Notification (TeacherID, Subject, Content, DateSent) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isss", $teacher_id, $subject, $content, $current_date);

  if ($stmt->execute()) {
      $success_message = "Notification sent successfully!";
  } else {
      $error_message = "Error sending notification: " . $stmt->error;
  }
  $stmt->close();
}

// Handle delete notification
if (isset($_GET['delete_notification'])) {
    $notification_id = $_GET['delete_notification'];

    $delete_stmt = $conn->prepare("DELETE FROM Student_Notification WHERE NotificationID = ?");
    $delete_stmt->bind_param("i", $notification_id);
    
    if ($delete_stmt->execute()) {
        $delete_message = "Notification deleted successfully!";
    } else {
        $delete_message = "Error deleting notification: " . $delete_stmt->error;
    }
    $delete_stmt->close();
}

// Retrieve notifications sent by the teacher
$notification_query = "SELECT NotificationID, Subject, Content, DATE_FORMAT(DateSent, '%d/%m/%Y') AS FormattedDate FROM Student_Notification WHERE TeacherID = ? ORDER BY DateSent DESC";
$stmt = $conn->prepare($notification_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$notifications_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Student Notification - Mentor</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* General Layout */
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      margin: 0;
      font-family: 'Open Sans', sans-serif;
    }

    /* Topbar */
    #topbar {
      width: 100%;
      height: 60px;
      background: #ff4925;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      position: fixed;
      top: 0;
      z-index: 1000;
    }

    #topbar .logo {
      font-size: 24px;
      font-weight: bold;
    }

    /* Sidebar */
    #sidebar {
      width: 250px;
      background: #ff4925;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
      position: fixed;
      top: 60px; /* Below topbar */
      bottom: 0;
    }

    #sidebar .logo {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 30px;
      text-align: center;
    }

    #sidebar ul {
      list-style-type: none;
      padding: 0;
      width: 100%;
    }

    #sidebar ul li {
      padding: 15px;
      cursor: pointer;
      text-align: center;
      border-radius: 5px;
      transition: background 0.3s;
      display: flex;
      align-items: center;
    }

    #sidebar ul li:hover {
      background: rgb(255, 42, 0);
    }

    #sidebar ul li i {
      margin-right: 10px;
    }

    #sidebar a {
      color: #fff;
    }

    /* Wrapper to position sidebar */
    .main-wrapper {
      display: flex;
      flex: 1;
    }

    /* Main Content */
    #main-content {
      flex-grow: 1;
      margin-left: 250px; /* Sidebar width */
      padding: 20px;
      background-color: #f8f9fa;
    }

    /* Success Message Style */
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #c3e6cb;
    }

    /* Notification Form */
    .notification-form {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .notification-form .form-group {
      margin-bottom: 15px;
    }

    .notification-form textarea {
      width: 100%;
      height: 150px;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ddd;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .notification-form .char-count {
      font-size: 12px;
      color: #888;
    }

    .notification-form .btn-primary {
      background-color: #ff4925;
      border-color: #ff4925;
      color: white;
      margin-top: 10px;
    }

    .notification-form .btn-primary:hover {
      background-color: #e43e1f;
      border-color: #e43e1f;
    }

    /* Notification Table */
    .notification-table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }

    .notification-table th,
    .notification-table td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }

    .notification-table th {
      background-color: #ff4925;
      color: white;
    }

    .notification-table td {
      font-size: 14px;
    }
  </style>
</head>

<body>

  <!-- Topbar -->
  <div id="topbar">
    <div class="logo" style="padding-left: 25px;">KGF Tuitions</div>
  </div>

  <!-- Main Wrapper (Sidebar + Content) -->
  <div class="main-wrapper">

    <!-- Sidebar -->
    <div id="sidebar">
      <a href="teacher_main_pg.php"><div class="logo">Dashboard</div></a><hr>
      <ul>
        <a href="teacher_student_pg.php"><li><i class="bi bi-person-fill"></i> Students</li></a>
        <a href="teacher_studentreport_pg.php"><li><i class="bi bi-file-earmark-bar-graph-fill"></i> Student Report</li></a>
        <a href="teacher_studentnotification_pg.php"><li><i class="bi bi-bell"></i> Notification</li></a>
      </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content"><br><br><br><br>
      <h1>Student Notifications</h1>
      <p>Here you can manage all notifications related to students. You can view important updates and announcements.</p>

      <!-- Display success message if set -->
      <?php if (isset($success_message)) : ?>
        <div class="alert alert-success">
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>

      <!-- Display error message if set -->
      <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <!-- Notification Form -->
      <form class="notification-form" method="POST">
        <div class="form-group">
          <label for="subject">Subject</label>
          <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="form-group">
          <label for="content">Content</label>
          <textarea class="form-control" id="content" name="content" maxlength="150" required></textarea>
          <div class="char-count" id="charCount">0/150</div>
        </div>
        <button type="submit" name="send_notification" class="btn btn-primary">Send to Your Students</button>
      </form>

      <br><br>

      <!-- Notifications Table -->
      <div class="notifications-container">
        <h2>Sent Notifications</h2>
        <table class="notification-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Subject</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($row = $notifications_result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['FormattedDate'] . "</td>";
              echo "<td>" . $row['Subject'] . "</td>";
              echo "<td><a href='teacher_studentnotification_pg.php?delete_notification=" . $row['NotificationID'] . "' class='btn btn-danger btn-sm'>Delete</a></td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

  <script>
    // Update character count for the content textarea
    document.getElementById('content').addEventListener('input', function() {
      var charCount = this.value.length;
      document.getElementById('charCount').textContent = charCount + "/150";
    });
  </script>

</body>

</html>
