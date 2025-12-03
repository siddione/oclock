<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db_username = 'root';
$db_password = '';
$database = 'oclock';
$conn = new mysqli($host, $db_username, $db_password, $database);
if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username'], $_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $admin = $result->fetch_assoc();
                if ($password === $admin['password']) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header("Location: ad_board.php");
                    exit();
                } else { $error_message = "Invalid username or password."; }
            } else { $error_message = "Invalid username or password."; }
            $stmt->close();
        }
    } else { $error_message = "Please enter both username and password."; }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - O'Clock</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');
    body { font-family: 'Playfair Display', serif; background-color: #f9f9f9; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center">

    <div class="flex w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden">

        <!-- Left Panel: Shop Introduction -->
        <div class="w-1/2 bg-gray-900 text-white p-10 flex flex-col justify-center">
            <h2 class="text-center text-4xl font-bold mb-4">Welcome, Admin!</h2>
            <p class="text-justify text-gray-300 mb-6">Manage the O'Clock store efficiently. Here you can track inventory, process orders, and oversee all operations to ensure a seamless experience for our valued customers.</p>
        
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-1/2 p-10 flex flex-col justify-center">
            <!-- Logo -->
            <img src="/Project01/oclocks.png" alt="O'Clocks" class="w-28 mb-6 mx-auto">

            <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center tracking-wide">Admin Login</h1>

            <?php if (!empty($error_message)): ?>
                <div class="mb-4 w-full text-center text-red-700 bg-red-50 py-2 rounded-lg border border-red-200 font-medium">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="w-full space-y-5">
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition"/>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-5 py-3 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition"/>
                </div>

                <button type="submit"
                    class="w-full py-3 mt-3 rounded-xl bg-gradient-to-r from-gray-900 to-black text-white font-semibold shadow-lg hover:from-black hover:to-gray-900 transition-all">
                    Login
                </button>
            </form>

            <p class="text-center text-gray-500 mt-6 text-sm">© 2025 O'Clock Admin</p>
        </div>

    </div>
</body>
</html>