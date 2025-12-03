<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Homepage - O'clock</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    body {
        font-family: 'Cambria', serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    /* Sidebar Styling */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: -260px;
        width: 260 px;
        height: 100%;
        background-color: rgba(255,255,255,0.95);
        backdrop-filter: blur(4px);
        border-right: 1px solid #ddd;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        z-index: 100;
        transition: left 0.3s ease;
    }
    .sidebar-overlay.show { left: 0; }
    .sidebar-overlay img { width: 180px; margin: 0 auto; }
    .sidebar-overlay a {
        color: #333;
        text-decoration: none;
        font-weight: 500;
        padding: 10px;
        border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }
    .sidebar-overlay a:hover { background-color: rgba(0,0,0,0.05); color: #111; }

    /* Carousel Videos */
    .carousel-item video {
        width: auto;
        height: 700px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        cursor: pointer;
    }

    .carousel-inner::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
    }

    .carousel-item { position: relative; z-index: 1; }

    /* Carousel Background */
    .carousel-bg {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('hp2.jpg') center/cover no-repeat;
        z-index: 0;
    }

    /* Product Images */
    .product-images-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        padding: 20px;
    }
    .product-image {
        width: 300px;
        height: 300px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
        cursor: pointer;
    }
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-image:hover { transform: scale(1.05); transition: all 0.3s ease; }
</style>
</head>
<body>

<!-- Sidebar -->

<div class="sidebar-overlay" id="sidebar">
    
    <img src="oclocks.png" alt="O'Clocks Logo">
    
    <a href="myprofile.php" class="flex items-center px-6 py-3 hover:bg-gray-100" >
                        <i class="fa fa-user w-6"></i>  My Profile</a>

    <a href="items.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-list w-6"> </i> All Items</a>

    <a href="mypurchase.php"  class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-box w-6"></i>My Orders</a>

    <a href="cart.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-shopping-cart w-6"></i>My Cart</a>

    <a href="wallet.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
         <i class="fa fa-credit-card-alt" aria-hidden="true"></i> My Wallet</a>

    <a href="aboutus.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
       <i class="fa fa-envelope" aria-hidden="true"></i> About Us</a>

    <a href="logout.php" class="flex items-center px-6 py-3 hover:bg-red-100 text-red-600">
                        <i class="fa fa-sign-out-alt w-6"></i> Sign Out</a>
</div>

</nav>
<!-- Carousel -->
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" style="position: relative;">
    <!-- Background Image -->
    <div class="carousel-bg"></div>

    <div class="carousel-inner" style="position: relative; z-index: 1;">
        <!-- Video Slide 1 -->
        <div class="carousel-item active">
            <video class="carousel-video d-block mx-auto" autoplay loop muted>
                <source src="vid1.mp4" type="video/mp4">
            </video>
            <div class="carousel-caption d-none d-md-block">
                <h5>Santos de Cartier Medium Watch</h5>
                <p>$456,774.95</p>
            </div>
        </div>
        <!-- Video Slide 2 -->
        <div class="carousel-item">
            <video class="carousel-video d-block mx-auto" autoplay loop muted>
                <source src="vid2.mp4" type="video/mp4">
            </video>
            <div class="carousel-caption d-none d-md-block">
                <h5>Cartier Baignoire</h5>
                <p>$368,366</p>
            </div>
        </div>
        <!-- Video Slide 3 -->
        <div class="carousel-item">
            <video class="carousel-video d-block mx-auto" autoplay loop muted>
                <source src="vid3.mp4" type="video/mp4">
            </video>
            <div class="carousel-caption d-none d-md-block">
                <h5>Rolex Day-Date 40</h5>
                <p>$120,280</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

<!-- Product Images -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Explore Our Collection</h2>
    <div class="product-images-container">
        <div class="product-image" onclick="location.href='product12.php';">
            <img src="prod1.jpg" alt="Product 1">
        </div>
        <div class="product-image" onclick="location.href='product13.php';">
            <img src="prod2.jpg" alt="Product 2">
        </div>
        <div class="product-image" onclick="location.href='product14.php';">
            <img src="prod3.jpg" alt="Product 3">
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const videos = document.querySelectorAll('.carousel-video');

    // Open sidebar when any video is clicked
    videos.forEach(video => {
        video.addEventListener('click', () => {
            sidebar.classList.add('show');
        });
    });

    // Close sidebar when clicking outside sidebar or videos
    document.addEventListener('click', (event) => {
        if (!sidebar.contains(event.target) && ![...videos].some(v => v.contains(event.target))) {
            sidebar.classList.remove('show');
        }
    });
</script>

</body>
</html>
