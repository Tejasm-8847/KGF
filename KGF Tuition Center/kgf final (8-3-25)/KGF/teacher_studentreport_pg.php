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
$teacher_id = $_SESSION['teacher_id'];  // TeacherID from the session

// Database connection (using provided code)
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

// Fetch teacher's students
$students_query = "SELECT StudentID, Name FROM Student WHERE TeacherID = ?";
$stmt = $conn->prepare($students_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$students_result = $stmt->get_result();

// Fetch all reports for the teacher
$reports_query = "SELECT r.ReportID, s.Name AS student_name, r.Test1, r.Test2, r.Test3, r.Test4, r.Test5, r.Test6, r.Test7, r.Test8, r.Test9, r.Test10 
                  FROM Student_Report r
                  JOIN Student s ON r.StudentID = s.StudentID
                  WHERE r.TeacherID = ?";
$stmt_reports = $conn->prepare($reports_query);
$stmt_reports->bind_param("i", $teacher_id);
$stmt_reports->execute();
$reports_result = $stmt_reports->get_result();

// Handle form submission to update report
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_report'])) {
    // Get data from the form
    $student_id = $_POST['student_id'];
    $test_number = $_POST['test_number'];
    $percentage = $_POST['percentage'];

    // Check if student_id, test_number, and percentage are valid
    if (empty($student_id) || empty($test_number) || empty($percentage)) {
        $error_message = "Please fill in all the fields.";
    } else {
        // Ensure percentage is a valid number
        if (!is_numeric($percentage) || $percentage < 0 || $percentage > 100) {
            $error_message = "Please enter a valid percentage (between 0 and 100).";
        } else {
            // Check if the student exists in the report table
            $check_query = "SELECT ReportID FROM Student_Report WHERE StudentID = ? AND TeacherID = ?";
            $stmt_check = $conn->prepare($check_query);
            $stmt_check->bind_param("ii", $student_id, $teacher_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            // Determine which test column to update based on test_number
            $column = 'Test' . $test_number;

            // If the student exists, update the report
            if ($result_check->num_rows > 0) {
                // Update query for existing report
                $update_report_query = "UPDATE Student_Report SET $column = ? WHERE StudentID = ? AND TeacherID = ?";
                $stmt_update = $conn->prepare($update_report_query);
                $stmt_update->bind_param("dii", $percentage, $student_id, $teacher_id);
                
                if ($stmt_update->execute()) {
                    $success_message = "Report updated successfully!";
                } else {
                    $error_message = "Error: " . $stmt_update->error;
                }
            } else {
                // If the student doesn't exist in the report, insert a new report row
                $insert_report_query = "INSERT INTO Student_Report (StudentID, TeacherID, $column) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($insert_report_query);
                $stmt_insert->bind_param("iid", $student_id, $teacher_id, $percentage);

                if ($stmt_insert->execute()) {
                    $success_message = "Report submitted successfully!";
                } else {
                    $error_message = "Error: " . $stmt_insert->error;
                }
            }
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $report_id = $_GET['edit'];
    $edit_query = "SELECT * FROM Student_Report WHERE ReportID = ?";
    $stmt_edit = $conn->prepare($edit_query);
    $stmt_edit->bind_param("i", $report_id);
    $stmt_edit->execute();
    $edit_result = $stmt_edit->get_result();

    if ($edit_result->num_rows > 0) {
        $report = $edit_result->fetch_assoc();
        // If report exists, populate the form with the existing data
        $selected_student_id = $report['StudentID'];
        $selected_test_number = array_search($report['Test1'], $report) ?: 1; // Set selected test number dynamically based on existing data
        $selected_percentage = $report['Test1']; // Default to Test 1 value or set the correct one.
    }
}

// Handle delete request
if (isset($_GET['delete'])) {
    $report_id = $_GET['delete'];
    $delete_query = "DELETE FROM Student_Report WHERE ReportID = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("i", $report_id);
    $stmt_delete->execute();
    header("Location: teacher_studentreport_pg.php"); // Redirect after deletion
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Student Report Management</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

    <!-- Custom Styles -->
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

        /* Error Message Style */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
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

    <!-- Sidebar -->
    <div id="sidebar">
        <a href="teacher_main_pg.php"><div class="logo">Dashboard</div></a><hr>
        <ul>
            <a href="teacher_student_pg.php"><li><i class="bi bi-person-fill"></i> Students</li></a>
            <a href="teacher_studentreport_pg.php"><li><i class="bi bi-file-earmark-bar-graph"></i> Student Report</li></a>
            <a href="teacher_studentnotification_pg.php"><li><i class="bi bi-bell-fill"></i> Notification</li></a>
        </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content"><br><br><br><br>
        <h1>Student Report</h1>
        <p>Here you can view and manage all student reports, track their performance, and analyze academic progress.</p>

        <!-- Report Form -->
        <form action="" method="POST" class="notification-form">
            <div class="form-group">
                <label for="student_id">Select Student</label>
                <select name="student_id" id="student_id" class="form-control">
                    <option value="">Select Student</option>
                    <?php while ($student = $students_result->fetch_assoc()) { ?>
                        <option value="<?php echo $student['StudentID']; ?>"
                          <?php if (isset($selected_student_id) && $selected_student_id == $student['StudentID']) echo 'selected'; ?>>
                          <?php echo $student['Name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="test_number">Select Test</label>
                <select name="test_number" id="test_number" class="form-control">
                    <option value="">Select Test</option>
                    <?php for ($i = 1; $i <= 10; $i++) { ?>
                        <option value="<?php echo $i; ?>"
                          <?php if (isset($selected_test_number) && $selected_test_number == $i) echo 'selected'; ?>>
                          Test <?php echo $i; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="percentage">Enter Percentage</label>
                <input type="text" name="percentage" id="percentage" class="form-control" value="<?php echo isset($selected_percentage) ? $selected_percentage : ''; ?>">
            </div>

            <button type="submit" name="submit_report" class="btn btn-primary">Submit Report</button>
        </form>

        <!-- Success/Error Message -->
        <?php if (isset($success_message)) { ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php } ?>

        <?php if (isset($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>

        <!-- Report Table -->
        <br><br>
        <button type="button" class="btn btn-secondary" onclick="window.location.reload();">Refresh Table</button>
        <table class="notification-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Test 1</th>
                    <th>Test 2</th>
                    <th>Test 3</th>
                    <th>Test 4</th>
                    <th>Test 5</th>
                    <th>Test 6</th>
                    <th>Test 7</th>
                    <th>Test 8</th>
                    <th>Test 9</th>
                    <th>Test 10</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($report = $reports_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $report['student_name']; ?></td>
                        <td><?php echo $report['Test1']; ?></td>
                        <td><?php echo $report['Test2']; ?></td>
                        <td><?php echo $report['Test3']; ?></td>
                        <td><?php echo $report['Test4']; ?></td>
                        <td><?php echo $report['Test5']; ?></td>
                        <td><?php echo $report['Test6']; ?></td>
                        <td><?php echo $report['Test7']; ?></td>
                        <td><?php echo $report['Test8']; ?></td>
                        <td><?php echo $report['Test9']; ?></td>
                        <td><?php echo $report['Test10']; ?></td>
                        <td>
                            <a href="teacher_studentreport_pg.php?edit=<?php echo $report['ReportID']; ?>">Edit</a> | 
                            <a href="teacher_studentreport_pg.php?delete=<?php echo $report['ReportID']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
    </div>
</body>

</html>
