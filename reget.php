<?php
require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $address = trim($_POST['address']);
    $zip_code = trim($_POST['zip_code']);

    if ($password !== $confirm_password) {
        die('Passwords do not match.');
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Check duplicate username/email
        $stmt = $pdo->prepare("SELECT 1 FROM user_account WHERE user_name = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);

        if ($stmt->rowCount() > 0) {
            die('Username or email already exists.');
        }

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO user_account 
            (first_name, middle_name, last_name, user_name, email, password, address, zip_code) 
            VALUES (:first_name, :middle_name, :last_name, :username, :email, :password, :address, :zip_code)");

        $stmt->execute([
            ':first_name' => $first_name,
            ':middle_name' => $middle_name,
            ':last_name' => $last_name,
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':address' => $address,
            ':zip_code' => $zip_code
        ]);

        // Create wallet for new user
        $new_user_id = $pdo->lastInsertId();
        $wallet = $pdo->prepare("INSERT INTO wallet (user_id, balance, updated_at) 
                                 VALUES (:user_id, 0, NOW())");
        $wallet->execute([':user_id' => $new_user_id]);

        // AUTO login + session
        session_start();
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['username'] = $username;

        // Redirect to homepage
        header("Location: homepage.php");
        exit();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
