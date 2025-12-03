<?php 
require 'db.php';

// Fetch products
$sql = "SELECT id, name, price, image FROM products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products - o'clocks</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body { font-family: "Cambria", serif; }
        /* Dropdown menu styling */
        .dropdown-menu {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- ==================== SIDEBAR ==================== -->
<div class="flex min-h-screen">

    <aside class="w-64 bg-white shadow-lg border-r border-gray-200 fixed h-full">
        <div class="p-3 flex items-center gap-3">
            <img src="oclocks.png" class="h-25" />
        </div>

        <nav class="mt-0">
            <ul class="text-gray-700 space-y-1">
            

                <li>
                    <a href="homepage.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-home w-6"></i> Home
                    </a>
                </li>
                
                <li>
                    <a href="items.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-list w-6"></i> All Items
                    </a>
                </li>

                <li>
                    <a href="myprofile.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-user w-6"></i> My Profile
                    </a>
                </li>

                <li>
                    <a href="mypurchase.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-box w-6"></i> My Orders
                    </a>
                </li>

                <li>
                    <a href="cart.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-shopping-cart w-6"></i> My Cart
                    </a>
                </li>

                 <li>
                 <a href="wallet.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-credit-card-alt" aria-hidden="true"></i> My Wallet</a>
                </li>
                
                <li>
                    <a href="logout.php" class="flex items-center px-6 py-3 hover:bg-bg-gray-100">
                        <i class="fa fa-sign-out-alt w-6"></i> Sign Out
                    </a>
                </li>

            </ul>
        </nav>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="ml-64 flex-1 p-10">

        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            Time to meet our products – where every second counts!
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    
                    <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-200 hover:shadow-lg transition">
                        
                        <img src="<?= $product['image'] ?>" 
                             onclick="location.href='product<?= $product['id'] ?>.php';"
                             class="w-full h-56 object-cover cursor-pointer" 
                             alt="<?= htmlspecialchars($product['name']) ?>" />

                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>

                            <p class="text-gray-700 mt-1">
                                PHP <?= number_format($product['price'], 2) ?>
                            </p>

                            <!-- Add to Cart -->
                            <a href="cart.php?product_id=<?= $product['id'] ?>">
                                <button class="w-full mt-4 py-2 border border-gray-800 text-gray-800 rounded-lg font-semibold hover:bg-gray-800 hover:text-white transition">
                                    Add to Cart
                                </button>
                            </a>

                            <!-- Buy Now -->
                            <a href="checkout.php?buy_now=<?= $product['id'] ?>">
                                <button class="w-full mt-3 py-2 bg-black text-white rounded-lg font-semibold hover:bg-gray-700 transition">
                                    Buy Now
                                </button>
                            </a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-600">No products found!</p>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>

<?php $conn = null; ?>
