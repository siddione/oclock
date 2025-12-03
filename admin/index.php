
<?php 
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
    <style>
        .font-serif { font-family: "Playfair Display", serif; }
    </style>
</head>

<body class="bg-[#F5F4F0] min-h-screen">

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

            <a href="inventory.php" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-zinc-700 hover:bg-zinc-100">
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

    <!-- MAIN CONTENT -->
    <main class="flex-1">

        <!-- HEADER -->
        <header class="bg-[#FAFAF8] border-b border-[#D4CFC4] px-8 py-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-serif text-[#2A2A2A]">Add Product</h1>
                <p class="text-sm text-[#7A7A7A] mt-1">Manage your product inventory</p>
            </div>
        </header>

        <div class="px-8 py-10 max-w-2xl">
            <div class="bg-[#FAFAF8] border border-[#D4CFC4] shadow-sm rounded-xl p-8">
                <h2 class="font-serif text-xl text-[#2A2A2A] mb-6">Add New Product</h2>

                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-[#4A4A4A] mb-1">Product Name</label>
                        <input type="text" name="name" required class="w-full border border-zinc-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-black focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#4A4A4A] mb-1">Product Price</label>
                        <input type="number" step="0.01" name="price" required class="w-full border border-zinc-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-black focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[#4A4A4A] mb-1">Product Image</label>
                        <input type="file" name="image" required class="w-full border border-zinc-300 rounded-lg px-3 py-2 bg-[#FAFAF8] focus:ring-1 focus:ring-black focus:outline-none">
                    </div>

                    <button type="submit" name="submit" class="px-5 py-3 bg-[#1A1A1A] text-white rounded-lg text-sm hover:bg-[#3A3A3A]">
                        Add Product
                    </button>
                </form>

                <?php
                if (isset($_POST['submit'])) {
                    $name = $_POST['name'];
                    $price = $_POST['price'];
                    $image = $_FILES['image']['name'];

                    $targetDir = 'uploads/';
                    $targetFile = $targetDir . basename($image);

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO products (name, price, image) VALUES (:name, :price, :image)");
                            $stmt->execute([
                                ':name' => $name,
                                ':price' => $price,
                                ':image' => $image
                            ]);
                            echo '<div class="mt-6 p-4 bg-green-600 text-white rounded-lg">Product added successfully!</div>';
                        } catch (PDOException $e) {
                            echo '<div class="mt-6 p-4 bg-red-600 text-white rounded-lg">Error: ' . $e->getMessage() . '</div>';
                        }
                    } else {
                        echo '<div class="mt-6 p-4 bg-red-600 text-white rounded-lg">Failed to upload image.</div>';
                    }
                }
                ?>
            </div>
        </div>

    </main>
</div>

</body>
</html>