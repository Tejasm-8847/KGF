<?php
$servername = "localhost";
$username = "root";   // Update with your DB username
$password = "";       // Update with your DB password
$dbname = "kgf";      // The name of your database

session_start(); // Start the session

if (!isset($_SESSION['teacher_logged_in']) || !$_SESSION['teacher_logged_in']) {
    header('Location: teacher_login.php'); // Redirect to login if not logged in
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch teacher's name from TeacherLogin using TeacherID from session
$teacherID = $_SESSION['teacher_id'];
$query = "SELECT Name FROM TeacherLogin WHERE TeacherID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherID);
$stmt->execute();
$stmt->bind_result($teacherName);
$stmt->fetch();
$stmt->close();

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header('Location: teacher_login.php'); // Redirect to the login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>KGF Tuitions - Teacher Dashboard</title>
  
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
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
      background: #343a40;
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
      background: #343a40;
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
      color: #007bff;
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
  <div id="topbar" style="background-color: #ff4925;">
    <div class="logo" style="padding-left: 25px;">KGF Tuitions</div>
    <div class="user-tab" id="user-tab">
      <div class="user-name"><?php echo htmlspecialchars($teacherName); ?></div> <!-- Display teacher's name -->
      <div class="dropdown-menu" id="dropdown-menu">
        <a href="?logout=true">Logout</a> <!-- Logout link -->
      </div>
    </div>
  </div>

  <!-- Main Wrapper (Sidebar + Content) -->
  <div class="main-wrapper">

    <!-- Sidebar -->
    <div id="sidebar" style="background-color: #ff4925;">
      <a href="teacher_main_pg.php"><div class="logo">Dashboard</div></a><hr>
      <ul>
        <a href="teacher_student_pg.php"><li><i class="bi bi-person-fill"></i> Students</li></a>
        <a href="teacher_studentreport_pg.php"><li><i class="bi bi-file-earmark-bar-graph-fill"></i> Student Report</li></a>
        <a href="teacher_studentnotification_pg.php"><li><i class="bi bi-bell-fill"></i> Notification</li></a>
      </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content">
      <h1>Welcome, <?php echo htmlspecialchars($teacherName); ?>!</h1> <!-- Display teacher's name -->
      
      <!-- Dashboard Boxes -->
      <div class="dashboard-boxes">
        <a href="teacher_student_pg.php">
          <div class="dashboard-box">
            <i class="bi bi-person-fill" style="color: #ff4925;"></i>
            <h3>Students</h3>
            <p>Manage and view student information.</p>
          </div>
        </a>

        <a href="teacher_studentreport_pg.php">
          <div class="dashboard-box">
            <i class="bi bi-file-earmark-bar-graph-fill" style=" color: #ff4925;"></i>
            <h3>Student Report</h3>
            <p>View reports on student performance.</p>
          </div>
        </a>

        <a href="teacher_studentnotification_pg.php">
          <div class="dashboard-box">
            <i class="bi bi-bell-fill" style="color: #ff4925;"></i>
            <h3>Notification</h3>
            <p>Post updates and important announcements.</p>
          </div>
        </a>
      </div>
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
        dropdownMenu.style.display = 'none';
      }
    });
  </script>

</body>
</html>
