<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name

$cartCount = 0; // Default cart count

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = count($_SESSION['cart']); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <title>MiMie Burger</title>
</head>
<body>
  <!-- Header Section Start -->
  <header class="header_section header_section_shadow">
    <div class="container">
      <nav class="navbar navbar-expand-lg custom_nav-container ">
        <a class="navbar-brand" href="index.php">
          <span>MiMie Burger</span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class=""></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
              <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item <?php echo $current_page == 'menu.php' ? 'active' : ''; ?>">
              <a class="nav-link" href="menu.php">Menu</a>
            </li>
            <li class="nav-item <?php echo $current_page == 'deals.php' ? 'active' : ''; ?>">
              <a class="nav-link" href="deals.php">Deals</a>
            </li>
            <li class="nav-item <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
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
</body>
</html>
