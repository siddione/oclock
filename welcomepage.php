<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>O'clock Shop</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<style>
    body { font-family: 'Cambria', serif; margin:0; padding:0; background-color:#f8f8f8; color:#333; }
    
    /* Sidebar overlay */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
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
    .sidebar-overlay a {
        color: #333; text-decoration: none; font-weight: 500;
        padding: 10px; border-radius: 5px;
        transition: background-color 0.3s, color 0.3s;
    }
    .sidebar-overlay a:hover {
        background-color: rgba(0,0,0,0.05); color: #111;
    }

    /* Carousel */
    .swiper { width: 100%; height: 500px; margin-bottom: 40px; }
    .swiper-slide video {
        width: 100%; height: 100%; object-fit: cover; border-radius:10px; cursor:pointer;
    }

    .swiper-slide {
    display: flex;
    justify-content: center;
    align-items: center;
}

.swiper-slide video {
    width: auto;
    height: 100%;
    object-fit: cover;
}


    /* Sale Cards */
    .sale-card { background-color:#fff; border:1px solid #ddd; transition: all 0.3s; }
    .sale-card h3, .sale-card p, .sale-card a { color:#333; }
    .sale-card a { border:1px solid #bbb; display:block; text-align:center; padding:8px 0; border-radius:5px; margin-top:10px; transition: background-color 0.3s, color 0.3s; }
    .sale-card a:hover { background-color:#f0f0f0; color:#111; }

    /* Product Cards */
    .product { background-color:#fff; border:1px solid #ddd; transition: all 0.3s; }
    .product h4, .product p { color:#333; }
    .product a { background-color:#eee; color:#333; display:block; text-align:center; padding:8px 0; border-radius:5px; margin-top:8px; transition: background-color 0.3s; }
    .product a:hover { background-color:#ddd; }

    footer { background-color:#f0f0f0; color:#666; padding:15px 0; text-align:center; }
</style>
</head>
<body>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebar">
    <div class="mb-6">
        <img src="oclocks.png" alt="O'Clocks Logo" class="w-32 mx-auto">
    </div>
    <a href="index.php">Login</a>
    <a href="register.php">Signup</a>
    <a href="aboutus.php">About</a>
</div>

<!-- Carousel -->
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-inner" style="position: relative; background-image: url('hp2.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 700px;">

        <!-- Swiper Slides -->
        <div class="swiper mySwiper" style="height: 700px;">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <video class="videoToggle" autoplay muted loop playsinline style="width: auto; height: 100%; object-fit: cover; cursor:pointer;">
                        <source src="vid1.mp4" type="video/mp4">
                    </video>
                </div>
                <div class="swiper-slide">
                    <video class="videoToggle" autoplay muted loop playsinline style="width: auto; height: 100%; object-fit: cover; cursor:pointer;">
                        <source src="vid2.mp4" type="video/mp4">
                    </video>
                </div>
                <div class="swiper-slide">
                    <video class="videoToggle" autoplay muted loop playsinline style="width: auto; height: 100%; object-fit: cover; cursor:pointer;">
                        <source src="vid3.mp4" type="video/mp4">
                    </video>
                </div>
            </div>

            <!-- Swiper Controls -->
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next text-gray-700"></div>
            <div class="swiper-button-prev text-gray-700"></div>
        </div>

    </div>

    <!-- Bootstrap Carousel Controls (Optional) -->
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>



  

<!-- Christmas Sale Cards -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-4">Christmas Exclusive Offers</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Unwrap incredible savings this holiday season with our special promotions</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $offers = [
                ["title"=>"Holiday Sale","discount"=>"40% Off","description"=>"On selected luxury watches","icon"=>"🎁"],
                ["title"=>"Christmas Special","discount"=>"Buy 1 Get 1","description"=>"Limited edition timepieces","icon"=>"🎀"],
                ["title"=>"Limited Time","discount"=>"Extra 25% Off","description"=>"Use code: XMAS2024","icon"=>"✨"],
                ["title"=>"Gift with Purchase","discount"=>"Free Gift Box","description"=>"With every watch purchase","icon"=>"⭐"]
            ];
            foreach($offers as $offer){
                echo "
                <div class='cursor-pointer hover:shadow-lg hover:scale-105 sale-card rounded-xl p-6'>
                    <h3 class='text-xl font-semibold mb-2'>{$offer['icon']} {$offer['title']}</h3>
                    <p class='text-2xl font-bold mb-2'>{$offer['discount']}</p>
                    <p class='text-sm mb-4'>{$offer['description']}</p>
                    <a href='index.php'>Claim Offer →</a>
                </div>
                ";
            }
            ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-16">
    <div class="container mx-auto px-4 text-center">
        <h3 class="text-4xl font-bold mb-10">O'Clock Bestsellers</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 justify-center">
            <?php
            $products = [
                ["img"=>"prod7.jpg","name"=>"Elegant Timepiece","price"=>"PHP 100,000.00"],
                ["img"=>"prod8.jpg","name"=>"Rolex Milgauss","price"=>"PHP 150,000.00"],
                ["img"=>"prod4.jpg","name"=>"Casio Vintage","price"=>"PHP 1,080.00"],
                ["img"=>"prod6.jpg","name"=>"Rolex Day-Date 40","price"=>"PHP 120,280.00"],
            ];
            foreach($products as $p){
                echo "
                <div class='group cursor-pointer hover:shadow-lg hover:-translate-y-2 product rounded-xl p-4'>
                    <div class='relative overflow-hidden bg-gray-100 aspect-square rounded-xl'>
                        <img src='{$p['img']}' alt='{$p['name']}' class='w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 rounded-xl'>
                    </div>
                    <h4 class='text-lg font-semibold mt-4'>{$p['name']}</h4>
                    <p class='text-gray-600 font-bold'>{$p['price']}</p>
                </div>
                ";
            }
            ?>
        </div>
    </div>
</section>

<footer class="bg-gray-100 text-gray-600 py-12 text-center">
    <p>&copy; 2025 O'clock Shop. All Rights Reserved.</p>
</footer>

<script>
    // Swiper
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        pagination: { el: ".swiper-pagination", clickable: true },
        navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        autoplay: { delay: 4000, disableOnInteraction: false },
    });

    // Sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const toggleBtns = document.querySelectorAll('.videoToggle');

toggleBtns.forEach(video => {
    video.addEventListener('click', () => {
        sidebar.classList.add('show');
    });
});


    // Close sidebar when clicking outside
document.addEventListener('click', (event) => {
    // Check if click is NOT on sidebar or any video
    if (!sidebar.contains(event.target) && ![...toggleBtns].some(video => video.contains(event.target))) {
        sidebar.classList.remove('show');
    }
});

</script>

</body>
</html>