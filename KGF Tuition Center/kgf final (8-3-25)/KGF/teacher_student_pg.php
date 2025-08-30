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

// Handle Add Student functionality
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Insert student into the database
    $add_student_query = "INSERT INTO Student (Name, Email, Password, TeacherID) VALUES ('$name', '$email', '$password', $teacher_id)";
    if ($conn->query($add_student_query) === TRUE) {
        $success_message = "Student added successfully!";
    } else {
        $error_message = "Error adding student: " . $conn->error;
    }
}

// Handle Attendance functionality
if (isset($_POST['mark_attendance'])) {
  if (isset($_POST['attendance'])) {
      $attendance_data = $_POST['attendance'];

      // Check if teacher_id is set and is valid
      if (isset($teacher_id)) {
          
          // Step 1: Increment TotalClasses for all students of the teacher
          $update_total_classes_query = "UPDATE Student SET TotalClasses = TotalClasses + 1 WHERE TeacherID = ?";
          $stmt = $conn->prepare($update_total_classes_query);
          $stmt->bind_param("i", $teacher_id);

          if ($stmt->execute()) {
              // Step 2: Update ClassesAttended for the students who are marked present
              foreach ($attendance_data as $student_id => $attendance_status) {
                  if ($attendance_status == 1) {  // Now checking if the checkbox is checked (value is 1)
                      // Only update ClassesAttended if the checkbox is ticked (presence)
                      $update_classes_attended_query = "UPDATE Student SET ClassesAttended = ClassesAttended + 1 WHERE StudentID = ?";
                      $stmt = $conn->prepare($update_classes_attended_query);
                      $stmt->bind_param("i", $student_id);

                      if ($stmt->execute()) {
                          // Success message for this student
                          $attendance_message = "Attendance updated for student ID: " . $student_id;
                      } else {
                          // Error message if this query fails
                          $attendance_message = "Error updating attendance for student ID: " . $student_id;
                      }
                  }
              }
              // General success message after marking attendance for all selected students
              $attendance_message = "Attendance marked successfully for all selected students.";
          } else {
              // If error updating total classes
              $attendance_message = "Error updating total classes for teacher ID: " . $teacher_id;
          }
      } else {
          // If teacher_id is not set
          $attendance_message = "Teacher ID is not set.";
      }
  } else {
      // If no students are selected for attendance
      $attendance_message = "Please select at least one student.";
  }
}




// Handle student deletion
if (isset($_GET['delete_student_id'])) {
    $student_id_to_delete = $_GET['delete_student_id'];

    // Delete student from the database
    $delete_query = "DELETE FROM Student WHERE StudentID = $student_id_to_delete AND TeacherID = $teacher_id";
    if ($conn->query($delete_query) === TRUE) {
        $success_message = "Student deleted successfully!";
    } else {
        $error_message = "Error deleting student: " . $conn->error;
    }
}

// Fetch students for the teacher
$sql = "SELECT * FROM Student WHERE TeacherID = $teacher_id";
$students = $conn->query($sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Teacher Dashboard</title>
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

    /* Toggle Button */
    #toggleTableBtn {
      margin-top: 20px;
      background-color: #ff4925;
      border-color: #ff4925;
      color: white;
    }

    /* Initially hide student table */
    #studentTableContainer {
      display: block; /* Display table by default */
    }

    /* Delete Button Style */
    .delete-btn {
      color: red;
      cursor: pointer;
      font-weight: bold;
    }

    .delete-btn:hover {
      text-decoration: underline;
    }

    /* Delete Student Section */
    .delete-student-form {
      background-color: #fff;
      padding: 20px;
      margin-top: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .delete-student-form select {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border-radius: 5px;
      border: 1px solid #ddd;
      margin-bottom: 10px;
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
        <a href="teacher_student_pg.php"><li><i class="bi bi-person"></i> Students</li></a>
        <a href="teacher_studentreport_pg.php"><li><i class="bi bi-file-earmark-bar-graph-fill"></i> Student Report</li></a>
        <a href="teacher_studentnotification_pg.php"><li><i class="bi bi-bell-fill"></i> Notification</li></a>
      </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content">
      <h1>Teacher Dashboard</h1>
      <h1>Student Management</h1>
<p>Here you can manage all your students. You can add, delete, and view important information about each student, such as their attendance and other details.</p>


      <!-- Display success or error message -->
      <?php if (isset($success_message)) : ?>
        <div class="success-message">
          <?php echo $success_message; ?>
        </div>
      <?php elseif (isset($error_message)) : ?>
        <div class="error-message">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      <br>
      <!-- Add Student Form -->
      <h2>Add Student</h2>
      <form method="POST" class="notification-form">
  <div class="mb-3">
    <label for="name" class="form-label">Student Name</label>
    <input type="text" class="form-control" name="name" id="name" placeholder="Enter student name" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Student Email</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="Enter student email" required>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Student Password</label>
    <input type="password" class="form-control" name="password" id="password" placeholder="Enter student password" required>
  </div>
  <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
</form>

<br><br>
      <!-- Attendance Form -->
<h2>Mark Attendance</h2>
<form method="POST" class="notification-form"> 
  <div class="table-responsive">
    <table class="notification-table">
      <thead class="bg-danger text-white">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Password</th>
          <th>Mark Attendance</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Display students in the table
        if ($students->num_rows > 0) {
            while ($row = $students->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['Name'] . "</td>
                        <td>" . $row['Email'] . "</td>
                        <td>" . $row['Password'] . "</td>
                        <td>
                          <input type='checkbox' name='attendance[" . $row['StudentID'] . "]' value='1'>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students found to mark attendance.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
</form>

<?php
// Display success or error message after form submission
if (isset($attendance_message)) {
    echo "<div class='alert alert-success'>" . $attendance_message . "</div>";
}
?>

<br><br>


      <!-- Delete Student -->
      <h2>Delete Student</h2>
      <form method="GET" class="delete-student-form notification-form">
  <div class="form-group">
    <label for="delete_student_id">Select Student to Delete</label>
    <select name="delete_student_id" id="delete_student_id" class="form-control" required>
      <option value="">Select Student</option>
      <?php
      // Fetch students for the teacher
      $students->data_seek(0); // Reset result set
      while ($row = $students->fetch_assoc()) {
          echo "<option value='" . $row['StudentID'] . "'>" . $row['Name'] . "</option>";
      }
      ?>
    </select>
  </div>
  <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete Student</button>
</form>


  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>

</body>
</html>
