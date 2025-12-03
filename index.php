<?php
session_start();
require 'db.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM user_account WHERE user_name = :user_name");
        $stmt->bindParam(':user_name', $user_name);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['user_name'];
            header("Location: homepage.php");
            exit();
        } else {
            $error_message = 'Invalid username or password.';
        }
    } catch (PDOException $e) {
        $error_message = 'Login error. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to O'clock</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Cambria, serif;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        #bg-video {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            z-index: -2;
            object-fit: cover;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            z-index: -1;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 0px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
            z-index: 10;
            color: #fff; /* White text to contrast the dark overlay */
        }
        /* Make inputs transparent with blur */
        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.6);
            color: #fff;
            backdrop-filter: blur(10px);
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: #fff;
            box-shadow: 0 0 6px rgba(255, 255, 255, 0.7);
        }

        .form-control::placeholder {
            color: #eaeaea;
        }

        /* Transparent button */
        .btn-outline-dark {
            border: 1px solid #fff;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-weight: bold;
            backdrop-filter: blur(8px);
        }

        .btn-outline-dark:hover {
            background: rgba(255, 255, 255, 0.35);
            color: #000;
        }

        /* Transparent link highlight */
        a.text-decoration-none {
            color: #eceae2ff;
            font-weight: bold;
        }
        a.text-decoration-none:hover {
            color: #fff;
        }


            .logo-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden; /* Ensures circular cropping */
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 25px; /* Center and give space under logo */
           
           
}

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        
        .form-control {
            border: 1px solid #ffffffff;
            padding: 12px;
        }

        .btn-outline-dark:hover {
            background: #000;
            color: #fff;
        }

        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<video autoplay muted loop playsinline id="bg-video">
    <source src="backgroundvid.mp4" type="video/mp4">
</video>
<div class="overlay"></div>

<div class="form-container">
    <div class="logo-container img">
        <img src="whitelogo.png"  alt="Logo">
    </div>

    <?php if ($error_message): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label class="form-label">Username</label>
        <input type="text" class="form-control mb-3" name="user_name" required>

        <label class="form-label">Password</label>
        <input type="password" class="form-control mb-3" name="password" required>

        <button class="btn btn-outline-dark w-100" type="submit">Login</button>

        <p class="mt-3 text-center">
            Don’t have an account? <a href="register.php" class="text-decoration-none">Create New</a>
        </p>
    </form>
</div>

<script src="js/bootstrap.min.js"></script>

</body>
</html>
