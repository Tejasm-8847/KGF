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
$query = "SELECT Name FROM Student WHERE StudentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentID);
$stmt->execute();
$stmt->bind_result($studentName);
$stmt->fetch();
$stmt->close();

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
  <title>Student Report - KGF Tuitions</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Vendor CSS Files -->
  <link href="assets/img/favicon.png" rel="icon">
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

    #topbar .user-tab {
      display: flex;
      align-items: center;
      position: relative;
      background: #fff;
      border-radius: 8px;
      padding: 5px 10px;
      cursor: pointer;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin-left: 20px;
    }

    #topbar .user-tab .user-name {
      color: #000;
    }

    #topbar .user-tab .dropdown-menu {
      position: absolute;
      top: 50px;
      right: 0;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border-radius: 5px;
      display: none;
      min-width: 160px;
    }

    #topbar .user-tab .dropdown-menu a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #000;
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
      color: #ff4925;
    }

    .dashboard-box h3 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .dashboard-box p {
      font-size: 16px;
      color: #777;
    }
    /* General Layout */
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      margin: 0;
      font-family: 'Open Sans', sans-serif;
    }

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

    .average {
      font-weight: bold;
    }
  </style>
</head>

<body>

  <!-- Topbar -->
  <div id="topbar">
    <div class="logo" style="padding-left: 25px;">KGF Tuitions</div>
      </div>
    </div>
  </div>

  <!-- Main Wrapper (Sidebar + Content) -->
  <div class="main-wrapper">

    <!-- Sidebar -->
    <div id="sidebar">
      <a href="student_main_pg.php"><div class="logo">Dashboard</div></a><hr>
      <ul>
        <a href="student_attendence_pg.php"><li><i class="bi bi-check-circle-fill"></i> Attendance</li></a>
        <a href="student_report_pg.php"><li><i class="bi bi-bar-chart"></i> Performance</li></a>
        <a href="student_notification_pg.php"><li><i class="bi bi-bell-fill"></i> Notifications</li></a>
      </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content">
      <h1>Student Report</h1>

      <?php
      // Fetch student test results from the database
      $query = "SELECT Test1, Test2, Test3, Test4, Test5, Test6, Test7, Test8, Test9, Test10 
                FROM Student_Report WHERE StudentID = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("i", $studentID);
      $stmt->execute();
      $stmt->bind_result($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10);
      $stmt->fetch();
      $stmt->close();

      // Calculate the average for the tests
      $tests = array($test1, $test2, $test3, $test4, $test5, $test6, $test7, $test8, $test9, $test10);
      $valid_tests = array_filter($tests, function($test) { return $test !== NULL; });
      $average = count($valid_tests) > 0 ? array_sum($valid_tests) / count($valid_tests) : 0;

      // Display test results
      echo "<table>";
      echo "<thead><tr>
                <th>Test 1</th><th>Test 2</th><th>Test 3</th><th>Test 4</th><th>Test 5</th>
                <th>Test 6</th><th>Test 7</th><th>Test 8</th><th>Test 9</th><th>Test 10</th>
                <th>Average</th>
              </tr></thead>";
      echo "<tbody>";
      echo "<tr>";
      foreach ($tests as $test) {
        echo "<td>" . ($test !== NULL ? $test : 'N/A') . "</td>";
      }
      echo "<td class='average'>" . number_format($average, 2) . "</td>";
      echo "</tr>";
      echo "</tbody></table>";
      ?>

    </div>

  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

  <script>
    // Toggle the dropdown menu when clicking on the user section
    document.getElementById('user-tab').addEventListener('click', function (event) {
      event.stopPropagation(); // Prevent the click from closing the dropdown immediately
      var dropdownMenu = document.getElementById('dropdown-menu');
      // Toggle display of the dropdown menu
      if (dropdownMenu.style.display === 'block') {
        dropdownMenu.style.display = 'none';
      } else {
        dropdownMenu.style.display = 'block';
      }
    });

    // Close the dropdown if clicking outside the user tab
    document.addEventListener('click', function (event) {
      var userTab = document.getElementById('user-tab');
      var dropdownMenu = document.getElementById('dropdown-menu');
      if (!userTab.contains(event.target)) {
        dropdownMenu.style.
