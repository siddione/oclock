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
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        $error = "Invalid amount. Please enter a value above 0.";
    } else {
        try {
            // Update wallet balance
            $update_wallet = $pdo->prepare("
                UPDATE wallet 
                SET balance = balance + :amount, updated_at = NOW() 
                WHERE user_id = :user_id
            ");
            $update_wallet->bindParam(':amount', $amount);
            $update_wallet->bindParam(':user_id', $user_id);
            $update_wallet->execute();

            // Insert transaction record
            $insert_trans = $pdo->prepare("
                INSERT INTO wallet_transactions (user_id, type, amount, created_at) 
                VALUES (:user_id, 'TOP-UP', :amount, NOW())
            ");
            $insert_trans->bindParam(':user_id', $user_id);
            $insert_trans->bindParam(':amount', $amount);
            $insert_trans->execute();

            // Success message
            $_SESSION['topup_success'] = "You have successfully added ₱" . number_format($amount, 2);

            header("Location: wallet.php");
            exit();

        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Funds - Wallet</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #f9f9f9, #e0e0e0);
            font-family: 'Cambria', serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .topup-box {
            max-width: 450px;
            width: 100%;
            background: #fff;
            padding: 30px 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-left: 6px solid #000;
            transition: transform 0.3s;
        }

        .topup-box:hover {
            transform: translateY(-3px);
        }

        h2 {
            font-weight: bold;
            color: #111;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        input[type="number"] {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: border 0.3s;
        }

        input[type="number"]:focus {
            border-color: #000;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            outline: none;
        }

        .btn-black {
            background: linear-gradient(to right, #000, #444);
            color: #fff;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 15px;
            transition: background 0.3s;
        }

        .btn-black:hover {
            background: linear-gradient(to right, #222, #555);
        }

        .btn-outline-dark {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 15px;
            transition: background 0.3s, color 0.3s;
        }

        .btn-outline-dark:hover {
            background-color: #000;
            color: #fff;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.95rem;
        }

        /* Side-by-side button styling */
        .d-flex.gap-3 button,
        .d-flex.gap-3 a {
            flex: 1;
        }
    </style>
</head>

<body>

<div class="topup-box">
    <h2 class="text-center mb-4"><i class="fa-solid fa-wallet me-2"></i>Add Funds</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Enter Amount (₱)</label>
            <input type="number" name="amount" class="form-control" step="0.01" min="1" placeholder="0.00" required>
        </div>

        <!-- Side-by-side buttons -->
        <div class="d-flex gap-3 mt-4">
            <a href="wallet.php" class="btn btn-outline-dark"><i class="fa-solid fa-xmark me-2"></i>Cancel</a>
            <button type="submit" class="btn btn-black"><i class="fa-solid fa-plus me-2"></i>Add Funds</button>
        </div>
    </form>
</div>

</body>
</html>
