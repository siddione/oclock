<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">

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
            max-width: 450px;
            height: 90vh;
            padding: 25px;
            border-radius: 0px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
            overflow-y: auto;
            color: #fff;
            z-index: 10;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.6);
            color: #fff;
            backdrop-filter: blur(10px);
            margin-bottom: 12px;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: #fff;
            box-shadow: 0 0 6px rgba(255, 255, 255, 0.7);
        }

        .btn-outline-dark {
            border: 1px solid #fff;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: bold;
            width: 100%;
        }

        .btn-outline-dark:hover {
            background: #fff;
            color: #000;
        }

        .text-center a {
            color: #fff;
            font-weight: bold;
        }

        .text-center a:hover {
            color: #eaeaea;
        }

        label.form-label {
            font-size: 0.90rem;
            font-weight: bold;
        }
    </style>

</head>
<body>

<video autoplay muted loop playsinline id="bg-video">
    <source src="backgroundvid.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<div class="form-container">
    <div class="logo-container">
        <img src="whitelogo.png" alt="Logo">
    </div>

    <h2 class="text-center mb-3">Create Account</h2>

    <form method="POST" action="reget.php">

        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" required>

        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" class="form-control">

        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" required>

        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>

        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>

        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>

        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>

        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" required></textarea>

        <label class="form-label">Zip Code</label>
        <input type="text" name="zip_code" class="form-control" required>

        <button type="submit" class="btn btn-outline-dark mt-2">Register</button>

    </form>

    <p class="mt-3 text-center">
        Already have an account?
        <a href="index.php" class="text-decoration-none">Login</a>
    </p>
</div>

<script src="js/bootstrap.min.js"></script>
</body>
</html>
