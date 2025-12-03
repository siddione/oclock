<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$account_number = trim($_POST['account_number']);
$cvc = trim($_POST['cvc']);

$stmt = $pdo->prepare("UPDATE wallet SET account_number = :account_number, cvc = :cvc WHERE user_id = :user_id");
$stmt->execute([
    ':account_number' => $account_number,
    ':cvc' => $cvc,
    ':user_id' => $user_id
]);

header("Location: wallet.php?card_added=1");
exit;
