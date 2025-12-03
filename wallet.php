<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$wallet_stmt = $pdo->prepare("SELECT balance, account_number, cvc FROM wallet WHERE user_id = :user_id");
$wallet_stmt->bindParam(':user_id', $user_id);
$wallet_stmt->execute();
$wallet = $wallet_stmt->fetch(PDO::FETCH_ASSOC);

$has_card = (!empty($wallet['account_number']) && !empty($wallet['cvc']));
$wallet_balance = $wallet ? $wallet['balance'] : 0;

$trans_stmt = $pdo->prepare("SELECT * FROM wallet_transactions WHERE user_id = :user_id ORDER BY created_at DESC");
$trans_stmt->bindParam(':user_id', $user_id);
$trans_stmt->execute();
$transactions = $trans_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Wallet</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Cambria', serif;
    background: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
}

/* Static Sidebar */
.sidebar {
    width: 260px;
    height: 100vh;
    background-color: rgba(255,255,255,0.95);
    backdrop-filter: blur(6px);
    border-right: 1px solid #ddd;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: fixed;
    left: 0;
    top: 0;
}
.sidebar img {
    width: 180px;
    margin: 0 auto 20px auto;
}
.sidebar a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
    padding: 10px;
    border-radius: 5px;
    transition: .3s;
}
.sidebar a:hover { background: rgba(0,0,0,0.05); }

/* Main content */
.main-content {
    margin-left: 260px;
    flex: 1;
    padding: 40px;
}

/* Wallet Box */
.wallet-box {
    max-width: 700px;
    margin: 0 auto;
    padding: 30px;
    background: rgba(255,255,255,0.95);
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    backdrop-filter: blur(8px);
}

/* Wallet Card */
.wallet-card {
    background: linear-gradient(135deg, #000000, #333333);
    color: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    font-size: 1.2rem;
    letter-spacing: 2px;
    box-shadow: 0px 6px 18px rgba(0,0,0,0.15);
}
.wallet-card .card-number {
    font-size: 1.4rem;
    font-weight: bold;
    margin-bottom: 6px;
}
.wallet-card .cvc {
    font-size: 1rem;
    opacity: 0.8;
}

.balance-display {
    font-size: 1.9rem;
    font-weight: bold;
    text-align: center;
    margin: 20px 0;
}

/* Button */
.btn-custom {
    display: block;
    width: 100%;
    margin-bottom: 15px;
    background-color: #000;
    color: #fff;
    border-radius: 8px;
    padding: 12px;
    text-decoration: none;
    transition: .3s;
}
.btn-custom:hover {
    background-color: #222;
    color: #fff;
}
</style>
</head>

<body>

<div class="sidebar">
    <img src="oclocks.png" alt="O'Clocks Logo">
    <a href="homepage.php"><i class="fa fa-home me-2"></i>Home</a>
    <a href="myprofile.php"><i class="fa fa-user me-2"></i>My Profile</a>
    <a href="items.php"><i class="fa fa-list me-2"></i>All Items</a>
    <a href="mypurchase.php"><i class="fa fa-box me-2"></i>My Orders</a>
    <a href="cart.php"><i class="fa fa-shopping-cart me-2"></i>My Cart</a>
    <a href="wallet.php"><i class="fa fa-credit-card-alt me-2"></i>My Wallet</a>
    <a href="aboutus.php"><i class="fa fa-envelope me-2"></i>About Us</a>
    <a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt me-2"></i>Sign Out</a>
</div>

<div class="main-content">
<div class="wallet-box">

<h2 class="text-center mb-4">My Wallet</h2>

<?php if (!$has_card): ?>

    <div class="alert alert-warning text-center">
        Please register your Wallet Card to activate funding features.
    </div>

    <form action="wallet_register_card.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Account Number</label>
            <input type="text" name="account_number" class="form-control" maxlength="16" required>
        </div>
        <div class="mb-3">
            <label class="form-label">CVC</label>
            <input type="text" name="cvc" class="form-control" maxlength="3" required>
        </div>
        <button type="submit" class="btn-custom">Register Wallet Card</button>
    </form>

<?php else: ?>

    <?php
$full_name = isset($_SESSION['username']) ? $_SESSION['username'] : "USER";
$account_number = $wallet['account_number'];
$expiry = !empty($wallet['expiry_date']) ? $wallet['expiry_date'] : "12/28"; // fallback expiry if none in DB
?>

<style>
.credit-card {
    width: 360px;
    height: 210px;
    border-radius: 18px;
    padding: 20px;
    background: linear-gradient(135deg, #0E1220, #1CCBA7);
    color: #fff;
    font-family: Arial, sans-serif;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    margin: 0 auto 20px auto;
}
.credit-card .chip {
    width: 45px;
    height: 35px;
    background: linear-gradient(90deg, #d7d2cc, #304352);
    border-radius: 6px;
}
.credit-card-number {
    font-size: 1.35rem;
    letter-spacing: 3px;
    font-weight: bold;
    margin-top: 15px;
}
.credit-card-details {
    display: flex;
    justify-content: space-between;
    font-size: .9rem;
    margin-top: 5px;
}
.card-holder {
    text-transform: uppercase;
    font-weight: 600;
}
.bank-logo {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: 1px;
}

 .d-flex.gap-3 button,
        .d-flex.gap-3 a {
            flex: 1;
        }
</style>

<!-- Digital Wallet Card -->
<div class="credit-card">
    <div class="bank-logo">MY WALLET</div>
    <div class="chip"></div>

    <div class="credit-card-number">
        <?= htmlspecialchars($account_number); ?>
    </div>

    <div class="card-holder">
        <?= htmlspecialchars($full_name); ?>
    </div>

    <div class="credit-card-details">
        <div>
            <label style="opacity:.7;font-size:.75rem;">VALID THRU</label><br>
            <?= htmlspecialchars($expiry); ?>
        </div>
        <div>
            <label style="opacity:.7;font-size:.75rem;">CVC</label><br>
            <?= htmlspecialchars($wallet['cvc']); ?>
        </div>
    </div>
</div>

<!-- Balance + Add Funds -->

 <div class="d-flex gap-3 mt-4">
    <a  ><i class="fa-solid me-2">Balance: ₱<?= number_format($wallet_balance, 2);?></i></a>
            <a href="topup.php" class="btn btn-outline-dark"><i class="fa-solid me-2"></i>Add Funds</a>
        </div>

<?php endif; ?>

<h4 class="mt-4">Transaction History</h4>

<table class="table table-bordered mt-3">
<thead>
<tr>
<th>Type</th>
<th>Amount</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php if (empty($transactions)): ?>
<tr><td colspan="3" class="text-center">No transactions yet.</td></tr>
<?php else: ?>
<?php foreach ($transactions as $t): ?>
<tr>
<td><?php echo htmlspecialchars($t['type']); ?></td>
<td>₱<?php echo number_format($t['amount'], 2); ?></td>
<td><?php echo $t['created_at']; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

</div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
