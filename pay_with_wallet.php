<?php
session_start();
require 'db.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $total_price = floatval($_POST['total_price']); // should match your orders.total_price

    try {
        // Check wallet balance
        $stmt = $pdo->prepare("SELECT balance FROM wallet WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet || $wallet['balance'] < $total_price) {
            $_SESSION['payment_error'] = "Insufficient wallet balance!";
            header("Location: checkout.php");
            exit();
        }

        // Deduct wallet balance
        $update_wallet = $pdo->prepare("
            UPDATE wallet 
            SET balance = balance - :amount, updated_at = NOW() 
            WHERE user_id = :user_id
        ");
        $update_wallet->bindParam(':amount', $total_price);
        $update_wallet->bindParam(':user_id', $user_id);
        $update_wallet->execute();

        // Create order
        $insert_order = $pdo->prepare("
            INSERT INTO orders (user_id, total_price, payment_method, created_at)
            VALUES (:user_id, :total_price, 'Wallet', NOW())
        ");
        $insert_order->bindParam(':user_id', $user_id);
        $insert_order->bindParam(':total_price', $total_price);
        $insert_order->execute();
        $order_id = $pdo->lastInsertId();

        // Log wallet transaction
        $insert_trans = $pdo->prepare("
            INSERT INTO wallet_transactions (user_id, type, amount, order_id, created_at)
            VALUES (:user_id, 'PAYMENT', :amount, :order_id, NOW())
        ");
        $insert_trans->bindParam(':user_id', $user_id);
        $insert_trans->bindParam(':amount', $total_price); // match total_price
        $insert_trans->bindParam(':order_id', $order_id);
        $insert_trans->execute();

        $_SESSION['payment_success'] = "Payment of ₱" . number_format($total_price, 2) . " completed successfully!";
        header("Location: orders.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['payment_error'] = "Error: " . $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
}
?>
