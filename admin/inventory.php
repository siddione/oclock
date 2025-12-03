<?php
// Include the database connection
include 'db.php';

try {
    // Fetch products for inventory
    $inventoryQuery = "SELECT * FROM products";
    $inventoryResult = $pdo->query($inventoryQuery);

    // Handle product updates (name, price, stock)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['product_name'], $_POST['product_price'], $_POST['product_stock'])) {
        $productId = $_POST['product_id'];
        $productName = $_POST['product_name'];
        $productPrice = $_POST['product_price'];
        $productStock = $_POST['product_stock'];
        

        $updateProductQuery = "UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?";
        $stmt = $pdo->prepare($updateProductQuery);
        $stmt->execute([$productName, $productPrice, $productStock, $productId]);

        // Redirect to refresh the page
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Handle product deletion
    if (isset($_GET['delete_product_id'])) {
        $productId = $_GET['delete_product_id'];
        $deleteProductQuery = "DELETE FROM products WHERE id = ?";
        $stmt = $pdo->prepare($deleteProductQuery);
        $stmt->execute([$productId]);

        // Redirect to refresh the page
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }

    // Additional read-only stats (these do not change functionality)
    $totalProductsQ = "SELECT COUNT(*) AS cnt FROM products";
    $totalProducts = $pdo->query($totalProductsQ)->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;

    $totalStockQ = "SELECT SUM(stock) AS total_stock FROM products";
    $totalStock = $pdo->query($totalStockQ)->fetch(PDO::FETCH_ASSOC)['total_stock'] ?? 0;

    $lowStockQ = "SELECT COUNT(*) AS low_stock_count FROM products WHERE stock <= 3 AND stock > 0";
    $lowStock = $pdo->query($lowStockQ)->fetch(PDO::FETCH_ASSOC)['low_stock_count'] ?? 0;

    $totalValueQ = "SELECT SUM(price * stock) AS total_value FROM products";
    $totalValue = $pdo->query($totalValueQ)->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Product Inventory</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Optional: icon set -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>

  <style>
    body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    /* small helper so table images fit nicely */
    .product-thumb { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; }
  </style>
</head>
<body class="bg-zinc-50 min-h-screen">

  <div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-zinc-200 p-6 hidden lg:flex flex-col">
      <div class="mb-8">
        <a href="#" class="text-2xl font-semibold text-zinc-900">O'Clock Admin</a>
      </div>

      <nav class="flex-1 space-y-1">
        <a href="ad_board.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-zinc-700 hover:bg-zinc-100">
          <i class='bx bxs-dashboard'></i>
          Dashboard
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-zinc-900 bg-zinc-100">
          <i class='bx bxs-box'></i>
          Inventory
        </a>
        <a href="logout.php" class="flex items-center gap-3 mt-6 px-3 py-2 rounded-md text-sm font-medium text-red-600 hover:bg-red-50">
          <i class='bx bx-log-out'></i>
          Logout
        </a>
      </nav>

      <div class="mt-8 text-xs text-zinc-500">
        <p>Logged in as Admin</p>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="flex-1 p-6 lg:p-10">

      <!-- HEADER -->
      <header class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-semibold text-zinc-900">Inventory</h1>
          <p class="text-sm text-zinc-500 mt-1">Manage your product catalog</p>
        </div>

        <div class="flex items-center gap-4">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input id="searchInput" type="text" placeholder="Search products..." class="pl-10 pr-4 py-2 rounded-md border border-zinc-200 bg-white w-72" />
          </div>

          <a href="index.php" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-md text-sm">
            <i class='bx bxs-plus-circle'></i> Add Product
          </a>
        </div>
      </header>

      <!-- STATS -->
      <section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
          <p class="text-sm text-zinc-500">Total Products</p>
          <p class="text-2xl font-semibold text-zinc-900 mt-1"><?= htmlspecialchars($totalProducts) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
          <p class="text-sm text-zinc-500">Total Stock</p>
          <p class="text-2xl font-semibold text-zinc-900 mt-1"><?= htmlspecialchars($totalStock) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
          <p class="text-sm text-zinc-500">Low Stock Items (≤ 3)</p>
          <p class="text-2xl font-semibold text-amber-600 mt-1"><?= htmlspecialchars($lowStock) ?></p>
        </div>
        <div class="bg-white rounded-xl border border-zinc-200 p-5">
          <p class="text-sm text-zinc-500">Total Value</p>
          <p class="text-2xl font-semibold text-zinc-900 mt-1">₱<?= number_format((float)$totalValue, 2) ?></p>
        </div>
      </section>

      <!-- TABLE -->
      <section class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
          <h2 class="text-lg font-medium text-zinc-900">Product Inventory</h2>
          <p class="text-sm text-zinc-500">Manage items, pricing and stock</p>
        </div>

        <div class="p-4 overflow-x-auto">
          <table class="w-full table-auto">
            <thead class="text-sm text-zinc-600 uppercase bg-zinc-50">
              <tr>
                <th class="p-3 text-left">Product ID</th>
                <th class="p-3 text-left">Product</th>
                <th class="p-3 text-right">Price</th>
                <th class="p-3 text-center">Stock</th>
                <th class="p-3 text-center">Status</th>
                <th class="p-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody id="productsTableBody" class="text-sm text-zinc-700">
              <?php while ($product = $inventoryResult->fetch(PDO::FETCH_ASSOC)): ?>
                <?php
                  // Determine stock status for badge classes and label
                  $stock = (int)$product['stock'];
                  if ($stock === 0) {
                    $statusLabel = 'Out of Stock';
                    $statusClasses = 'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-50 text-red-600 border border-red-100';
                  } elseif ($stock <= 3) {
                    $statusLabel = 'Low Stock';
                    $statusClasses = 'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-600 border border-amber-100';
                  } else {
                    $statusLabel = 'In Stock';
                    $statusClasses = 'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-100';
                  }

                  // thumbnail source (image column expected in DB)
                 $thumb = !empty($product['image']) 
        ? 'uploads/' . htmlspecialchars($product['image']) 
        : 'uploads';

                ?>
                <tr class="group hover:bg-zinc-50" data-product-id="<?= htmlspecialchars($product['id']) ?>" data-product-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
                  <td class="p-3 align-top">
                    <div class="font-mono text-zinc-700">#<?= htmlspecialchars($product['id']) ?></div>
                  </td>

                  <td class="p-3 align-top">
                    <td class="p-3 align-top">
              <div class="font-medium text-zinc-900"><?= htmlspecialchars($product['name']) ?></div>
                <?php if (!empty($product['brand'])): ?>
                <div class="text-xs text-zinc-500 mt-1"><?= htmlspecialchars($product['brand']) ?></div>
            <?php endif; ?>
                    </td>

                  </td>

                  <td class="p-3 align-top text-right font-medium">₱<?= number_format((float)$product['price'], 2) ?></td>

                  <td class="p-3 align-top text-center"><?= htmlspecialchars($product['stock']) ?></td>

                  <td class="p-3 align-top text-center">
                    <span class="<?= $statusClasses ?>"><?= $statusLabel ?></span>
                  </td>

                  <td class="p-3 align-top text-right space-y-2">
                    <!-- Inline edit form (keeps exact POST field names) -->
                    <form method="POST" action="" class="inline-block w-full">
                      <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>" />
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                        <input type="text" name="product_name" value="<?= htmlspecialchars($product['name']) ?>" class="border border-zinc-200 rounded-md px-2 py-1 text-sm w-full" required />
                        <input type="number" step="0.01" name="product_price" value="<?= htmlspecialchars($product['price']) ?>" class="border border-zinc-200 rounded-md px-2 py-1 text-sm w-full" required />
                        <input type="number" name="product_stock" value="<?= htmlspecialchars($product['stock']) ?>" class="border border-zinc-200 rounded-md px-2 py-1 text-sm w-full" required />
                      </div>

                      <div class="flex items-center justify-end gap-2 mt-2">
                        <button type="submit" class="px-3 py-1 bg-zinc-900 text-white text-xs rounded-md">Update</button>

                        <a href="?delete_product_id=<?= urlencode($product['id']) ?>"
                           class="px-3 py-1 bg-red-600 text-white text-xs rounded-md"
                           onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                      </div>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>

  <script>
    // Client-side search (pure JS) — does not alter server behavior
    const searchInput = document.getElementById('searchInput');
    const tbody = document.getElementById('productsTableBody');

    searchInput.addEventListener('input', function () {
      const q = this.value.trim().toLowerCase();

      // Loop through rows and hide/show
      Array.from(tbody.querySelectorAll('tr')).forEach(row => {
        const pid = row.getAttribute('data-product-id')?.toLowerCase() || '';
        const pname = row.getAttribute('data-product-name')?.toLowerCase() || '';

        if (!q || pid.includes(q) || pname.includes(q)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  </script>

</body>
</html>
cdn.tailwindcss.com