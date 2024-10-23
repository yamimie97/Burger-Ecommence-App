<?php
session_start();
$cartCount = 0; // Default cart count

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = count($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
  <title>MiMie Burger</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <!-- Owl Carousel -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <!-- Custom styles -->
  <link rel="stylesheet" href="css/style.css" />
  <!-- Responsive styles -->
  <link rel="stylesheet" href="css/responsive.css" />
</head>
<body>

  <div class="hero_area">
    <!-- Background Image -->
    <div class="bg-box">
      <img src="../../image/background/homepagebg4.webp" alt="Background Image">
    </div>
    
    <!-- Header Section Start -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="index.php">
            <span>MiMie Burger</span>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto">
              <li class="nav-item active">
                <a class="nav-link" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="menu.php">Menu</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="deals.php">Deals</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php">About</a>
              </li>
            </ul>
            <div class="user_option">
              <style>
                .dropdown-menu {
                  min-width: 150px; 
                  padding: 0;
                  margin: 0;
                }

                .dropdown-menu .dropdown-item {
                  font-size: 16px; 
                  border-bottom: 1px solid #ddd;
                  padding: 10px;
                  margin: 0;
                  text-align: center;
                }
                
                .dropdown-menu .dropdown-item:last-child {
                  border-bottom: none;
                }

                .dropdown-menu .dropdown-item:hover {
                  background-color: #37B7C3;
                  color: #fff;
                  border-radius: 4px; 
                }
              </style>
              <?php if (isset($_SESSION['username'])): ?>
                  <div class="dropdown">
                    <span class="user-greeting"><?php echo $_SESSION['username']; ?></span>
                    <a href="#" class="dropdown-toggle user_link" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-user" aria-hidden="true"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                      <a class="dropdown-item" href="account.php">Account</a>
                      <a class="dropdown-item" href="feedback.php">Feedback</a>
                      <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                  </div>
                  <a href="cart.php" class="order_online">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    Cart (<?php echo $cartCount; ?>)
                  </a>
              <?php else: ?>
                  <a href="signin.php" class="user_link">
                      <i class="fa fa-user" aria-hidden="true"></i>
                      Login
                  </a>
              <?php endif; ?>             
            </div>
          </div>
        </nav>
      </div>
    </header>
    <!-- Header Section End -->
    
    <!-- Slider Section Start -->
    <section class="slider_section">
      <div id="customCarousel1" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
          <!-- Slide 1 -->
          <div class="carousel-item active">
            <div class="container">
              <div class="row">
                <div class="col-md-7 col-lg-6">
                  <div class="detail-box">
                    <h1>Fast Food Restaurant</h1>
                    <p>Enjoy delicious burgers, crafted with fresh ingredients. Order your favorite burger now!</p>
                    <div class="btn-box">
                      <a href="menu.php" class="btn1">Order Now</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Slide 2 -->
          <div class="carousel-item">
            <div class="container">
              <div class="row">
                <div class="col-md-7 col-lg-6">
                  <div class="detail-box">
                    <h1>Burger Paradise</h1>
                    <p>Discover a variety of burgers with a unique blend of flavors. Taste the difference.</p>
                    <div class="btn-box">
                      <a href="menu.php" class="btn1">View Menu</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Slide 3 -->
          <div class="carousel-item">
            <div class="container">
              <div class="row">
                <div class="col-md-7 col-lg-6">
                  <div class="detail-box">
                    <h1>Special Deals</h1>
                    <p>Enjoy special discounts and offers exclusively at MiMie Burger. Order now!</p>
                    <div class="btn-box">
                      <a href="deals.php" class="btn1">View Deals</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container">
          <ol class="carousel-indicators">
            <li data-target="#customCarousel1" data-slide-to="0" class="active"></li>
            <li data-target="#customCarousel1" data-slide-to="1"></li>
            <li data-target="#customCarousel1" data-slide-to="2"></li>
          </ol>
        </div>
      </div>
    </section>
    <!-- Slider Section End -->
  </div>
