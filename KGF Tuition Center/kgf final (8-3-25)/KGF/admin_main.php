<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission for adding a new teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['password'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'kgf');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert new teacher into the database
    $sql = "INSERT INTO teacherlogin (Name, Email, Password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $name, $email, $password);
    
    if ($stmt->execute()) {
        $message = "Teacher added successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}

// Handle teacher deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_teacher_email'])) {
    $teacherEmail = $_POST['delete_teacher_email'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'kgf');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete teacher from the database
    $sql = "DELETE FROM teacherlogin WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $teacherEmail);

    if ($stmt->execute()) {
        echo "Teacher deleted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Page - KGF Tuitions</title>
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
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      margin: 0;
      font-family: 'Open Sans', sans-serif;
    }

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

    #main-content {
      flex-grow: 1;
      margin-left: 250px;
      padding: 20px;
      background-color: #f8f9fa;
    }

    .main-wrapper {
      display: flex;
      flex: 1;
    }

    .form-section {
      margin-top: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    button[type="submit"] {
      width: auto;
      margin-top: 10px;
    }

    .table {
      margin-top: 20px;
    }

    .message-container {
      margin-top: 20px;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      font-weight: bold;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      right: 0;
      background: #343a40;
      color: white;
      border-radius: 5px;
      padding: 5px;
    }

    .dropdown-menu a {
      color: white;
      padding: 5px 10px;
      display: block;
      text-decoration: none;
    }

    .dropdown-menu a:hover {
      background-color: ;
    }

    /* Hover effect for the user-name */
  #admin-name {
    transition:  color 0.3s, background-color 0.3s;
  }

  #admin-name:hover {
    color: #fff;
    background-color:rgb(255, 42, 0);
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
  }

  </style>
</head>

<body>

  <!-- Topbar -->
  <div id="topbar" style="background-color: #ff4925;">
    <div class="logo">KGF Tuitions - Admin</div>
    <div class="user-tab" id="user-tab">
      <div class="user-name" id="admin-name">Admin</div>
      <div class="dropdown-menu" id="dropdown-menu" style="background-color:rgb(255, 42, 0); border-color:rgb(255, 42, 0)">
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <!-- Main Wrapper (Sidebar + Content) -->
  <div class="main-wrapper">

    <!-- Sidebar -->
    <div id="sidebar" style="background-color: #ff4925;">
      <div class="logo">Admin Dashboard</div>
      <ul>
        <!-- Empty Sidebar -->
      </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content">
      <h2>Teacher Management</h2>

      <!-- Teachers Table -->
      <div class="table-responsive">
        <table class="table table-bordered" id="teachers-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Password</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'kgf');
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }

            // Fetch existing teachers from the database
            $sql = "SELECT * FROM teacherlogin";
            $result = $conn->query($sql);
            $counter = 1;
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr data-email='" . $row['Email'] . "'>
                        <td>" . $counter++ . "</td>
                        <td>" . $row['Name'] . "</td>
                        <td>" . $row['Email'] . "</td>
                        <td>" . $row['Password'] . "</td>
                        <td>
                          <a href='#' class='btn btn-danger btn-sm delete-btn' data-email='" . $row['Email'] . "'>Delete</a>
                        </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='5'>No teachers found</td></tr>";
            }
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>

      <!-- Add New Teacher Section -->
      <div class="form-section">
        <h4>Add New Teacher</h4>
        <form id="addTeacherForm">
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" style="width: 30%;" required>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" style="width: 30%;" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" style="width: 30%;" required>
          </div>
          <button type="submit" class="btn btn-danger"  style="background-color: #ff4925; border-color: #ff4925">Add Teacher</button>
        </form>

        <!-- Message container for success/error -->
        <div id="message-container" class="message-container" style="display: none;"></div>
      </div>

    </div>

  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    // Show dropdown menu on clicking "Admin"
    document.getElementById('admin-name').addEventListener('click', function() {
      var dropdownMenu = document.getElementById('dropdown-menu');
      dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
    });

    // Handle form submission to add a new teacher
    $('#addTeacherForm').on('submit', function (e) {
      e.preventDefault(); // Prevent page reload

      var name = $('#name').val();
      var email = $('#email').val();
      var password = $('#password').val();

      $.ajax({
        url: '', // We're processing everything within this same file
        method: 'POST',
        data: { name: name, email: email, password: password },
        success: function(response) {
          $('#message-container').html('<div class="success">Teacher added successfully.</div>').show();
          // Clear form inputs
          $('#name').val('');
          $('#email').val('');
          $('#password').val('');
        },
        error: function(xhr, status, error) {
          $('#message-container').html('<div class="error">Error: ' + error + '</div>').show();
        }
      });
    });

    // Handle delete button click
    $('.delete-btn').on('click', function () {
      var teacherEmail = $(this).data('email'); // Get the teacher's email from data-email attribute
      if (confirm("Are you sure you want to delete this teacher?")) {
        $.ajax({
          url: '', // We're processing everything within this same file
          method: 'POST',
          data: { delete_teacher_email: teacherEmail },
          success: function(response) {
            alert("Teacher deleted successfully.");
            location.reload();
          },
          error: function(xhr, status, error) {
            alert("Error: " + error);
          }
        });
      }
    });
  </script>

</body>
</html>
