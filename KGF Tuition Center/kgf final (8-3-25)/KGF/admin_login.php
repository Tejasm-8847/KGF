
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Admin Login - KGF Tuitions</title>
    
    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

    <style>
        .form-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            max-width: 400px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            margin-bottom: 5px;
        }

        .form-control {
            margin-bottom: 15px;
        }

        .btn-primary {
            width: 100%;
            background-color: #ff4925;
            border-color: #ff4925;
        }
        
        .btn-primary:hover {
            background-color: #ff4925;
            border-color: #ff4925;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="container">
        <h2><a href="index.html" style="color: #ff4925;">KGF Tutuion Center</a><h2>
        </div>
    </header>

    <main class="main">
        <!-- Page Title -->
        <div class="page-title text-center" style="background-color: #ff4925;">
            <h1>Admin Login</h1>
        </div>

        <!-- Login Form Section -->
        <section id="login-section" class="section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <!-- Form Container -->
                        <div class="form-container">
                            <form action="admin_login_process.php" method="post">
                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>

                            <!-- Error Message Display -->
                            <?php
                            if (isset($_GET['error'])) {
                                echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p>© <strong>KGF Tuitions</strong> All Rights Reserved</p>
        </div>
    </footer>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

</body>
</html>
