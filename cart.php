<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$userId = $_SESSION['user_id'];
require 'db.php';

// Add to cart
if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :product_id");
    $stmt->execute([':product_id' => $productId]);
    $product = $stmt->fetch();

    if ($product) {
        $price = $product['price'];
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        $cartItem = $stmt->fetch();

        if ($cartItem) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1, total_price = total_price + :price WHERE id = :id");
            $stmt->execute([':price' => $price, ':id' => $cartItem['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, total_price) VALUES (:user_id, :product_id, 1, :total_price)");
            $stmt->execute([':user_id' => $userId, ':product_id' => $productId, ':total_price' => $price]);
        }
    }
}

// Increase quantity
if (isset($_GET['increase_quantity'])) {
    $productId = $_GET['increase_quantity'];
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :product_id");
    $stmt->execute([':product_id' => $productId]);
    $product = $stmt->fetch();
    if ($product) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1, total_price = total_price + :price WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':price' => $product['price'], ':user_id' => $userId, ':product_id' => $productId]);
    }
}

// Decrease quantity
if (isset($_GET['decrease_quantity'])) {
    $productId = $_GET['decrease_quantity'];
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    $cartItem = $stmt->fetch();

    if ($cartItem && $cartItem['quantity'] > 1) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :product_id");
        $stmt->execute([':product_id' => $productId]);
        $product = $stmt->fetch();
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity - 1, total_price = total_price - :price WHERE id = :id");
        $stmt->execute([':price' => $product['price'], ':id' => $cartItem['id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
    }
}

// Search query
$searchQuery = $_GET['search_query'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :search_query");
$stmt->execute([':search_query' => "%$searchQuery%"]);
$filteredProducts = $stmt->fetchAll();

// Get cart items
$stmt = $pdo->prepare("SELECT p.name, p.price, p.image, c.quantity, c.total_price, c.product_id 
                       FROM cart c JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$cart = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart - O'Clocks</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100" style="font-family: 'Cambria', serif;">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg border-r border-gray-200 fixed h-full ">
        <div class="flex items-center gap-3 p-3 ">
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
                    <a href="cart.php" class="flex items-center px-6 py-3 hover:bg-gray-100 ">
                        <i class="fa fa-shopping-cart w-6"></i> My Cart
                    </a>
                </li>

                <li>
                 <a href="wallet.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-credit-card-alt" aria-hidden="true"></i> My Wallet</a>
                </li>

                <li>
                    <a href="logout.php" class="flex items-center px-6 py-3 hover:bg-gray-100">
                        <i class="fa fa-sign-out-alt w-6"></i> Sign Out
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 ml-64 p-10">

        <!-- SEARCH BAR -->
       

        <h2 class="text-3xl font-bold mb-6">Clock's Running – Your Cart is Ready!</h2>

        <!-- CART TABLE -->
        <!-- CART TABLE -->
<?php if ($cart): ?>
    <form action="checkout.php">
        <div class="overflow-x-auto bg-white shadow rounded-lg p-6">
            <table class="w-full text-left">
                <thead class="border-b text-lg">
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $product): ?>
                        <tr class="border-b text-gray-700">
                            <td class="py-3">
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-20 h-20 object-cover rounded">
                            </td>
                            <td class="py-3 font-semibold"><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= number_format($product['price'], 2) ?> PHP</td>
                            <td class="flex items-center gap-2 py-3">
                                <a href="?decrease_quantity=<?= $product['product_id'] ?>" class="px-3 py-1 bg-gray-300 rounded">-</a>
                                <?= $product['quantity'] ?>
                                <a href="?increase_quantity=<?= $product['product_id'] ?>" class="px-3 py-1 bg-gray-300 rounded">+</a>
                            </td>
                            <td><?= number_format($product['price'] * $product['quantity'], 2) ?> PHP</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-between mt-6 text-lg font-bold">
                <div>
                    Total: <?= number_format(array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $cart)), 2) ?> PHP
                </div>
                <button class="px-6 py-3 bg-black text-white rounded">Proceed to Checkout</button>
            </div>
        </div>
    </form>
<?php else: ?>
    <p>Your cart is empty.</p>
<?php endif; ?>

        </div>

    </main>
</div>

</body>
</html>
