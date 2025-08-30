<?php
// Database credentials
$servername = "localhost";
$username = "root";   // Update with your DB username
$password = "";       // Update with your DB password
$dbname = "kgf";      // The name of your database

// Start the session to manage logged-in student
session_start(); 

// Check if the student is logged in
if (!isset($_SESSION['student_logged_in']) || !$_SESSION['student_logged_in']) {
    header('Location: student_login.php'); // Redirect to login if not logged in
    exit();
}

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the student's name from the Student table using StudentID from session
$studentID = $_SESSION['student_id'];  // Assuming the student ID is stored in session after login
$query = "SELECT TeacherID FROM Student WHERE StudentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$stmt->bind_result($teacherID);
$stmt->fetch();
$stmt->close();

// Fetch notifications for the student based on TeacherID (no direct StudentID link anymore)
$notificationQuery = "
    SELECT 
        sn.NotificationID, sn.Subject, sn.Content, sn.DateSent
    FROM 
        Student_Notification sn
    WHERE 
        sn.TeacherID = ? 
    ORDER BY 
        sn.DateSent DESC
";
$stmt = $conn->prepare($notificationQuery);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$result = $stmt->get_result();

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header('Location: student_login.php'); // Redirect to the login page
    exit();
}
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
      background:  #ff4925;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      position: sticky;
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
      background:  #ff4925;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
      position: fixed;
      top: 60px;
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
      background:rgb(255, 42, 0);
    }

    #sidebar ul li i {
      margin-right: 10px;
    }

    #sidebar a {
      color: #fff;
      color: inherit;
      text-decoration: none;
    }

    /* Main Content */
    #main-content {
      flex-grow: 1;
      margin-left: 250px;
      padding: 20px;
      background-color: #f8f9fa;
    }

    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }

    table th, table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }

    table th {
      background-color: #ff4925;
      color: white;
    }

    .content {
      max-width: 400px;  /* Adjust width for better content visibility */
      white-space: normal;  /* Allow text to wrap */
      word-wrap: break-word;  /* Ensure long words break to the next line */
      height: auto;  /* Allow for multiline wrapping */
    }

    .dashboard-boxes {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-top: 20px;
    }

    .dashboard-box {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 20px;
      text-align: center;
      transition: transform 0.3s ease;
      cursor: pointer;
    }

    .dashboard-box:hover {
      transform: scale(1.05);
    }

    .dashboard-box i {
      font-size: 50px;
      margin-bottom: 15px;
      color:  #ff4925;
    }

    .dashboard-box h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .dashboard-box p {
      font-size: 16px;
      color: #777;
    }

    .main-wrapper {
      display: flex;
      flex: 1;
    }
  </style>
</head>

<body>

  <!-- Topbar -->
  <div id="topbar">
    <div class="logo" style="padding-left: 25px;">KGF Tutions</div>
  </div>

  <!-- Main Wrapper (Sidebar + Content) -->
  <div class="main-wrapper">

  <!-- Sidebar -->
  <div id="sidebar">
    <a href="student_main_pg.php"><div class="logo">Dashboard</div></a><hr>
    <ul>
        <a href="student_attendence_pg.php"><li><i class="bi bi-check-circle-fill"></i> Attendance</li></a>
        <a href="student_report_pg.php"><li><i class="bi bi-bar-chart-fill"></i> Performance</li></a>
        <a href="student_notification_pg.php"><li><i class="bi bi-bell"></i> Notifications</li></a>
    </ul>
  </div>

    <!-- Main Content -->
    <div id="main-content">
      <h1>Student Notifications</h1>

      <table>
  <thead>
    <tr>
      <th>Date Sent</th>
      <th>Subject</th>
      <th>Content</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Fetch the results from your database (for example, this could be the result of a SQL query)
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // Create a DateTime object from the DateSent value
        $date = new DateTime($row['DateSent']);
        // Format it to dd-mm-yyyy
        $formattedDate = $date->format('d-m-Y'); 
        // Truncate content to 150 characters if necessary
        $content = strlen($row['Content']) > 150 ? substr($row['Content'], 0, 150) . '...' : $row['Content'];
        
        echo "<tr>
                <td>" . $formattedDate . "</td>
                <td>" . $row['Subject'] . "</td>
                <td class='content'>" . $content . "</td>
              </tr>";
      }
    } else {
      echo "<tr><td colspan='3'>No notifications found.</td></tr>";
    }
    ?>
  </tbody>
</table>

    </div>

  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>
