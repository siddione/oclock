<?php
// Include the database connection
include 'db.php';

try {
    // Fetch total orders
    $orderQuery = "SELECT COUNT(*) AS total_orders FROM orders";
    $orderResult = $pdo->query($orderQuery);
    $totalOrders = $orderResult->fetch(PDO::FETCH_ASSOC)['total_orders'];

    // Fetch total users
    $userQuery = "SELECT COUNT(*) AS users_active FROM user_account";
    $userResult = $pdo->query($userQuery);
    $usersActive = $userResult->fetch(PDO::FETCH_ASSOC)['users_active'];

    // Fetch today's sales
    $salesQuery = "SELECT SUM(total_price) AS todays_sales FROM orders WHERE DATE(created_at) = CURDATE()";
    $salesResult = $pdo->query($salesQuery);
    $todaysSales = $salesResult->fetch(PDO::FETCH_ASSOC)['todays_sales'] ?? 0;

    // Fetch monthly sales
    $monthlyQuery = "SELECT SUM(total_price) AS monthly_sales FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    $monthlyResult = $pdo->query($monthlyQuery);
    $monthlySales = $monthlyResult->fetch(PDO::FETCH_ASSOC)['monthly_sales'] ?? 0;

    // Fetch recent orders
    $recentOrdersQuery = "SELECT id, items_summary, name, created_at, total_price, payment_method, status FROM orders ORDER BY created_at DESC LIMIT 5";
    $recentOrdersResult = $pdo->query($recentOrdersQuery);

    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['status'];
        $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute([$newStatus, $orderId]);
        
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- TAILWIND CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .font-serif { font-family: "Playfair Display", serif; }
    </style>
</head>

<body class="bg-zinc-50 min-h-screen">

    <!-- HEADER -->
    <header class="bg-white border-b border-zinc-200 px-8 py-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-serif text-zinc-900">Dashboard</h1>
            <p class="text-sm text-zinc-500 mt-1">Welcome back to O'Clock Admin</p>
        </div>

        <div class="space-x-4">
            <a href="inventory.php" class="text-zinc-700 hover:text-black text-sm">Inventory</a>
            <a href="logout.php" class="text-red-600 hover:text-red-800 font-medium text-sm">Logout</a>
        </div>
    </header>

    <div class="p-8">

        <!-- METRICS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            <!-- Total Orders -->
            <div class="bg-white border border-zinc-200 shadow-sm rounded-xl p-6">
                <p class="text-sm text-zinc-500">Total Orders</p>
                <h3 class="text-3xl font-semibold text-zinc-900 mt-2">
                    <?= $totalOrders ?>
                </h3>
            </div>

            <!-- Total Users -->
            <div class="bg-white border border-zinc-200 shadow-sm rounded-xl p-6">
                <p class="text-sm text-zinc-500">Total Users</p>
                <h3 class="text-3xl font-semibold text-zinc-900 mt-2">
                    <?= $usersActive ?>
                </h3>
            </div>

            <!-- Today's Sales -->
            <div class="bg-white border border-zinc-200 shadow-sm rounded-xl p-6">
                <p class="text-sm text-zinc-500">Today's Sales</p>
                <h3 class="text-3xl font-semibold text-zinc-900 mt-2">
                    ₱<?= number_format($todaysSales, 2) ?>
                </h3>
            </div>

            <!-- Monthly Sales -->
            <div class="bg-white border border-zinc-200 shadow-sm rounded-xl p-6">
                <p class="text-sm text-zinc-500">Monthly Sales</p>
                <h3 class="text-3xl font-semibold text-zinc-900 mt-2">
                    ₱<?= number_format($monthlySales, 2) ?>
                </h3>
            </div>

        </div>

        <!-- RECENT ORDERS -->
        <div class="bg-white border border-zinc-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-zinc-200">
                <h2 class="font-serif text-lg text-zinc-900">Recent Orders</h2>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-zinc-100 text-zinc-700 text-sm">
                            <th class="p-3">Order ID</th>
                            <th class="p-3">Ordered Items</th>
                            <th class="p-3">Customer</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Total Price</th>
                            <th class="p-3">Payment Method</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-zinc-700">
                        <?php while ($row = $recentOrdersResult->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="border-b border-zinc-200">
                            <td class="p-3 font-medium">#<?= $row['id'] ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['items_summary']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="p-3"><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                            <td class="p-3">₱<?= number_format($row['total_price'], 2) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['payment_method']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['status']) ?></td>
                            <td class="p-3">
                                <form method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                                    <select name="status"
                                        class="border border-zinc-300 rounded-md p-1 text-sm">
                                        <option value="Pending"    <?= $row['status']=='Pending'    ? 'selected' : '' ?>>Pending</option>
                                        <option value="To Ship"    <?= $row['status']=='To Ship'    ? 'selected' : '' ?>>To Ship</option>
                                        <option value="To Deliver" <?= $row['status']=='To Deliver' ? 'selected' : '' ?>>To Deliver</option>
                                        <option value="Completed"  <?= $row['status']=='Completed'  ? 'selected' : '' ?>>Completed</option>
                                        <option value="Cancelled"  <?= $row['status']=='Cancelled'  ? 'selected' : '' ?>>Cancelled</option>
                                    </select>

                                    <button class="px-3 py-1 bg-black text-white text-xs rounded-md">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="text-right mt-6">
                    <a href="index.php"
                            button class = "px-5 py-3 bg-black text-white text-small rounded-md"
                       class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md">
                        Add New Products
                    </a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
cdn.tailwindcss.com