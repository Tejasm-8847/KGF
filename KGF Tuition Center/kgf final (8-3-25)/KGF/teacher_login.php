
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Teacher Login</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Mentor
  * Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
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
  
    .role-selection {
      margin-bottom: -10px;
      text-align: center;
    }
  
    .role-selection .btn-group {
      margin-bottom: 20px;
    }
  
    .form-label {
      margin-bottom: 5px;
    }
  
    .form-control {
      margin-bottom: 15px;
    }
  
    .btn-primary {
      width: 100%;
    }

    #select-teacher{
      background-color:  #ff4925;
      color: white;
    }
    #select-student{
      color:  #ff4925;
    }
    #select-student:hover{
      background-color:  #ff4925;
      color: white;
    }

  </style>
</head>

<body class="starter-page-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/logo.jpg" alt="">
        <h1 class="sitename" style="color: #ff4925;">KGF TUTION CENTER</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html" onmouseover="this.style.color='#ff4925'" onmouseout="this.style.color='inherit'">Home<br></a></li>
          <li><a href="about.html"onmouseover="this.style.color='#ff4925'" onmouseout="this.style.color='inherit'">About</a></li>
          <li><a href="courses.html"onmouseover="this.style.color='#ff4925'" onmouseout="this.style.color='inherit'">Courses</a></li>
          <li><a href="trainers.html"onmouseover="this.style.color='#ff4925'" onmouseout="this.style.color='inherit'">Trainers</a></li>
          <li><a href="teacher_login.php" class="active"style="color: #ff4925;" onmouseover="this.style.color='#ff4925'" onmouseout="this.style.color='#ff4925'">Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="contact.html" style="background-color: #ff4925;">Contact Us</a>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title" data-aos="fade" style="background-color: #ff4925;">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center">
            <div class="col-lg-8">
              <h1>Login</h1>
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs" style="background-color:rgb(255, 42, 0);">
        <div class="container">
          <ol>
            <li><a href="index.html">Home</a></li>
            <li class="current">Teacher Login</li>
          </ol>
        </div>
      </nav>
    </div><!-- End Page Title -->

    <section id="starter-section" class="starter-section section">
      <!-- Section Title -->
       <center>
      <div class="container section-title" data-aos="fade-up">
        <p>Logging in as?<br></p>
      </div></center><!-- End Section Title -->
    
      <div class="container" data-aos="fade-up">
        <!-- Role Selection -->
        <div class="role-selection">
          <div class="btn-group" role="group" aria-label="Role Selection">
            <button type="button" class="btn btn-outline-primary" id="select-teacher" style="border-color:  #ff4925; fill:  #ff4925;">Teacher</button>
            <button type="button" class="btn btn-outline-secondary" id="select-student" style="border-color:  #ff4925;" onclick="window.location.href='student_login.php'">Student</button>
          </div>
        </div>
    
        <!-- Form Container -->
        <div class="form-container">
          <form action="login_teacher.php" method="post" class="row g-3">
            <input type="hidden" id="role" name="role" value="" required>
            <div class="col-12">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="col-12">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary" style="background-color:#5fcf80;border-color:  #ff4925;background-color: #ff4925;">Login</button>
            </div>
          </form>
        </div>
      </div>
    </section>

  </main>

  <footer id="footer" class="footer position-relative bg-light pt-4 pb-3">
    <div class="container">
      <div class="row gy-4 justify-content-center">
        
        <!-- Contact Details - Added Margin for Spacing -->
        <div class="col-lg-4 col-md-6 text-center text-md-start mb-4">
          <a href="index.html" class="logo d-flex align-items-center justify-content-center justify-content-md-start mb-3">
            <h5 class="fw-bold text-dark">KGF TUTION CENTER</h5>
          </a>
          <ul class="list-unstyled small">
          <li><i class="bi bi-geo-alt me-2"></i>120/134 Slv compex,2nd & top floor, hongsandra, bommanahalli to begur main road, Bengaluru</li><br>
            <li><i class="bi bi-telephone me-2"></i><strong>Phone:</strong> +91 63662 13279</li><br>
            <li><i class="bi bi-envelope me-2"></i><strong>Email:</strong> kgftutioncenter@gmail.com</li>
          </ul>
          <div class="d-flex justify-content-center justify-content-md-start">
            <a href="#" class="me-3"><i class="bi bi-twitter-x fs-5" style="color: #ff4925;"></i></a>
            <a href="#" class="me-3"><i class="bi bi-facebook fs-5" style="color: #ff4925;"></i></a>
            <a href="#" class="me-3"><i class="bi bi-instagram fs-5" style="color: #ff4925;"></i></a>
            <a href="#"><i class="bi bi-linkedin fs-5" style="color: #ff4925;"></i></a>
          </div>
        </div>
  
        <!-- Useful Links - Added Margin for Spacing -->
        <div class="col-lg-3 col-md-6 text-center text-md-start mb-4">
          <h5 class="fw-bold">Quick Links</h5>
          <ul class="list-unstyled small">
            <li><i class="bi bi-chevron-right me-2"></i><a href="index.html" class="text-dark text-decoration-none">Home</a></li>
            <li><i class="bi bi-chevron-right me-2"></i><a href="about.html" class="text-dark text-decoration-none">About Us</a></li>
            <li><i class="bi bi-chevron-right me-2"></i><a href="courses.html" class="text-dark text-decoration-none">Courses</a></li>
            <li><i class="bi bi-chevron-right me-2"></i><a href="trainers.html" class="text-dark text-decoration-none">Trainers</a></li>
            <li><i class="bi bi-chevron-right me-2"></i><a href="admin_login.php" class="text-dark text-decoration-none">Admin Login</a></li>
          </ul>
        </div>
      </div>
  
      <!-- Copyright Section - Fully Centered and Clean -->
      <div class="row mt-4">
        <div class="col-12 text-center">
          <p class="fw-semibold mb-1 fs-6">&copy; 2024 <strong>KGF Tution Center</strong>. All Rights Reserved.</p>
          <p class="small">Designed by <a href="#" class="text-primary text-decoration-none">BMSCE-MCA</a></p>
        </div>
      </div>
  
      <!-- Commented Sections (Do Not Delete) -->
      <!-- 
      <div class="col-lg-2 col-md-3 footer-links">
        <h4>Our Services</h4>
        <ul>
          <li><a href="#">Web Design</a></li>
          <li><a href="#">Web Development</a></li>
          <li><a href="#">Product Management</a></li>
          <li><a href="#">Marketing</a></li>
          <li><a href="#">Graphic Design</a></li>
        </ul>
      </div>
  
      <div class="col-lg-4 col-md-12 footer-newsletter">
        <h4>Our Newsletter</h4>
        <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
        <form action="forms/newsletter.php" method="post" class="php-email-form">
          <div class="newsletter-form">
            <input type="email" name="email">
            <input type="submit" value="Subscribe">
          </div>
          <div class="loading">Loading</div>
          <div class="error-message"></div>
          <div class="sent-message">Your subscription request has been sent. Thank you!</div>
        </form>
      </div>
      -->
  
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" style="background-color: #ff4925;"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>