<?php
session_start();
require 'db.php';

// Ensure login
if (empty($_SESSION['user_id'])) {
    echo "<p>You must be logged in to checkout.</p>";
    exit;
}

$userId = $_SESSION['user_id'];
$isBuyNow = false;
$cart = [];
$showInsufficientModal = false; // Flag to trigger modal

/* ------------------------------------------------------
   BUY NOW HANDLING
------------------------------------------------------ */
if (isset($_GET['buy_now'])) {
    $isBuyNow = true;
    $productId = intval($_GET['buy_now']);

    $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = :id");
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $cart[] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => 1
        ];
    }
} else {
    /* ------------------------------------------------------
       REGULAR CART CHECKOUT
    ------------------------------------------------------ */
    $stmt = $pdo->prepare("
        SELECT p.id AS product_id, p.name, p.price, c.quantity
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :user_id
    ");
    $stmt->execute([':user_id' => $userId]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ------------------------------------------------------
   FETCH USER WALLET
------------------------------------------------------ */
$stmt = $pdo->prepare("SELECT balance FROM wallet WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);
$wallet_balance = $wallet ? $wallet['balance'] : 0;

/* ------------------------------------------------------
   FETCH USER INFO
------------------------------------------------------ */
$stmt = $pdo->prepare("
    SELECT first_name, middle_name, last_name, email, address
    FROM user_account WHERE id = :user_id
");
$stmt->execute([':user_id' => $userId]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

/* ------------------------------------------------------
   HANDLE CHECKOUT SUBMISSION
------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_POST['agree'])) {
        echo "<script>alert('You must agree to the Terms & Conditions.'); window.history.back();</script>";
        exit;
    }

    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $payment_method = $_POST['payment_method'];

    // Calculate totals
    $total_price = 0;
    $total_items = 0;
    $summary = [];

    foreach ($cart as $item) {
        $line_total = $item['price'] * $item['quantity'];
        $total_price += $line_total;
        $total_items += $item['quantity'];
        $summary[] = $item['name'] . " (x" . $item['quantity'] . ")";
    }

    $item_summary = implode(", ", $summary);
    $order_status = "Pending";

    /* ------------------------------------------------------
       WALLET PAYMENT CHECK
    ------------------------------------------------------ */
    if ($payment_method === "Wallet") {
        if ($wallet_balance < $total_price) {
            // Insufficient funds, show modal
            $showInsufficientModal = true;
        } else {
            // Deduct wallet balance
            $new_balance = $wallet_balance - $total_price;
            $stmt = $pdo->prepare("UPDATE wallet SET balance = :bal WHERE user_id = :uid");
            $stmt->execute([':bal' => $new_balance, ':uid' => $userId]);
            $order_status = "Paid";
        }
    }

    // Only insert order if funds are sufficient or payment is not wallet
    if (!$showInsufficientModal) {
        // INSERT ORDER
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, name, address, email, phone, payment_method, total_price, items_summary, total_items, status)
            VALUES (:user_id, :name, :address, :email, :phone, :pm, :total, :summary, :items, :status)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':address' => $address,
            ':email' => $email,
            ':phone' => $phone,
            ':pm' => $payment_method,
            ':total' => $total_price,
            ':summary' => $item_summary,
            ':items' => $total_items,
            ':status' => $order_status
        ]);

        $orderId = $pdo->lastInsertId();

        // INSERT WALLET TRANSACTION
        if ($payment_method === "Wallet") {
            $stmt = $pdo->prepare("
                INSERT INTO wallet_transactions (user_id, type, amount, order_id, created_at)
                VALUES (:uid, 'PAYMENT', :amount, :oid, NOW())
            ");
            $stmt->execute([
                ':uid' => $userId,
                ':amount' => $total_price,
                ':oid' => $orderId
            ]);
        }

        // INSERT ORDER ITEMS & UPDATE STOCK
        foreach ($cart as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:oid, :pid, :qty, :price)
            ");
            $stmt->execute([
                ':oid' => $orderId,
                ':pid' => $item['product_id'],
                ':qty' => $item['quantity'],
                ':price' => $item['price']
            ]);

            $updateStock = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :pid");
            $updateStock->execute([
                ':qty' => $item['quantity'],
                ':pid' => $item['product_id']
            ]);
        }

        // CLEAR CART IF NOT BUY NOW
        if (!$isBuyNow) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
        }

        header("Location: order_confirmation.php");
        exit;
    }

} // END POST block
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - O'Clocks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: 'Cambria', serif; background-image: url('check.png'); background-size: cover; background-position: center; background-repeat: no-repeat; }
.checkout-form { background-color: white; padding: 20px; margin-top: 15px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
.cart-table th, .cart-table td { padding: 10px; text-align: center; }
.cart-table th { background-color: #f8f9fa; }
.btn-primary { background-color: #000; border-color: #000; }
.btn-primary:hover { background-color: #333; border-color: #333; }
.logo { width: 150px; height: auto; margin-top: 20px; }
.header-container { display: flex; flex-direction: column; align-items: center; justify-content: center; }
.checkout-header { font-size: 2rem; margin-top: 20px; }
</style>
</head>
<body>

<div class="container">
    <div class="header-container">
        <a href="homepage.php"><img src="oclocks.png" class="logo" alt="Logo"></a>
        <div class="checkout-header"><h2>Clock In Your Order</h2></div>
    </div>

    <div class="checkout-form">
        <h4>Your Cart</h4>
        <table class="table table-bordered cart-table">
            <thead>
                <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']); ?></td>
                        <td><?= number_format($product['price'], 2); ?> PHP</td>
                        <td><?= $product['quantity']; ?></td>
                        <td><?= number_format($product['price'] * $product['quantity'], 2); ?> PHP</td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Price:</strong></td>
                    <td><strong><?= number_format(array_sum(array_map(fn($p)=>$p['price']*$p['quantity'],$cart)), 2); ?> PHP</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="checkout-form">
        <h4>Billing Information</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" name="name" 
                    value="<?= htmlspecialchars(trim(($user_info['first_name'] ?? '') . ' ' . ($user_info['middle_name'] ?? '') . ' ' . ($user_info['last_name'] ?? ''))); ?>" 
                    required>
            </div>

            <div class="mb-3">
                <label for="address">Shipping Address</label>
                <textarea class="form-control" name="address" rows="3" required><?= htmlspecialchars($user_info['address'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user_info['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone">Phone Number</label>
                <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($user_info['phone'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="payment_method">Payment Method</label>
                <select class="form-select" name="payment_method" required>
                    <option value="Cash on Delivery" selected>Cash on Delivery</option>
                    <?php if ($wallet_balance > 0): ?>
                        <option value="Wallet">Pay with My Wallet (₱<?= number_format($wallet_balance, 2); ?>)</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3">
                <input type="checkbox" id="agree" name="agree" class="form-check-input">
                <label for="agree" class="form-check-label">
                    I have read and agree to the <a href="terms.php" target="_blank" class="text-decoration-underline">Terms & Conditions</a>
                </label>
            </div>

            <button type="submit" id="placeOrderBtn" class="btn btn-primary btn-small btn-block" disabled>Place Order</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Insufficient Funds Modal -->
<div class="modal fade" id="insufficientFundsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation"></i> Insufficient Funds</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p>Your wallet balance is not enough to complete this order.</p>
        <p><strong>Would you like to add funds?</strong></p>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <a href="wallet.php" class="btn btn-dark w-50">Add Funds</a>
        <button type="button" class="btn btn-secondary w-50 ms-2" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
// Enable place order button only if terms are checked
const agreeCheckbox = document.getElementById('agree');
const placeOrderBtn = document.getElementById('placeOrderBtn');
agreeCheckbox.addEventListener('change', function() {
    placeOrderBtn.disabled = !this.checked;
});

// Trigger insufficient funds modal if needed
<?php if ($showInsufficientModal): ?>
document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('insufficientFundsModal'));
    myModal.show();
});
<?php endif; ?>
</script>

</body>
</html>
