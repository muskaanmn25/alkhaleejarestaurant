<?php
session_start();
require_once "db.php";

$order_id = intval($_GET['order_id']);

$order_query = mysqli_query($conn,"SELECT * FROM orders WHERE order_id='$order_id'");
if(mysqli_num_rows($order_query) == 0){
    echo "<script>alert('Order not found!'); window.location='customer_dashboard.php';</script>";
    exit();
}
$order = mysqli_fetch_assoc($order_query);

if(isset($_POST['pay'])){
    $method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    if($method == "Cash" || $method == "Card"){
        // Insert into billing table
        $total = $order['total_amount'];
        mysqli_query($conn,"INSERT INTO billing (order_id, subtotal, tax, total) VALUES ('$order_id', '$total', 0, '$total')");
        
        // Update order status
        mysqli_query($conn,"
            UPDATE orders 
            SET payment_status='pending', payment_method='$method', status='confirmed'
            WHERE order_id='$order_id'
        ");
        echo "<script>alert('Order Placed successfully! Please pay at the counter.'); window.location='customer_dashboard.php';</script>";
        exit();
    } 
    else if($method == "UPI"){
        // Set method, but redirect for payment proof
        mysqli_query($conn,"
            UPDATE orders 
            SET payment_method='UPI'
            WHERE order_id='$order_id'
        ");
        header("Location: upi_payment.php?order_id=".$order_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment - Al-Khaleej</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        body { background:#f4efec; display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .payment-card { background:white; padding:40px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.08); max-width:400px; width:90%; text-align:center; }
        .payment-card h2 { font-family:'Playfair Display', serif; font-size:32px; color:#111; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:15px;}
        .order-meta { margin-bottom:30px; }
        .order-meta p { font-size:16px; color:#555; margin-bottom:5px; }
        .amount { font-size:32px; font-weight:600; color:#7a1f2b; margin-top:10px; display:block; }
        
        .payment-methods { text-align:left; margin-bottom:30px; }
        .payment-option { display:block; padding:15px; border:1px solid #ddd; border-radius:10px; margin-bottom:15px; cursor:pointer; transition:0.3s; display:flex; align-items:center; gap:15px; }
        .payment-option:hover { border-color:#7a1f2b; background:#fcf7f7;}
        .payment-option input[type="radio"] { transform:scale(1.2); accent-color:#7a1f2b; }
        
        .btn-pay { width:100%; padding:15px; background:#7a1f2b; color:white; border:none; border-radius:10px; font-size:18px; font-weight:500; cursor:pointer; box-shadow:0 5px 15px rgba(122,31,43,0.3); transition:0.3s; }
        .btn-pay:hover { background:#5c1520; transform:translateY(-2px); box-shadow:0 8px 20px rgba(122,31,43,0.4); }
    </style>
    <script>
        function updateButtonText() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            const btn = document.getElementById('payBtn');
            if (method === 'UPI') {
                btn.innerText = 'Proceed to UPI Payment';
            } else if (method === 'Cash' || method === 'Card') {
                btn.innerText = 'Confirm Order (Pay at Counter)';
            }
        }
    </script>
</head>
<body>

<div class="payment-card">
    <h2>Complete Payment</h2>
    
    <div class="order-meta">
        <p>Order ID: <strong>#<?php echo htmlspecialchars($order_id);?></strong></p>
        <span class="amount">₹<?php echo number_format($order['total_amount'], 2);?></span>
    </div>

    <form method="POST">
        <div class="payment-methods">
            <label class="payment-option">
                <input type="radio" name="payment_method" value="UPI" onchange="updateButtonText()" required>
                <span style="font-weight:500; font-size:16px;">UPI / Wallet</span>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="Card" onchange="updateButtonText()">
                <span style="font-weight:500; font-size:16px;">Credit / Debit Card</span>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="Cash" onchange="updateButtonText()">
                <span style="font-weight:500; font-size:16px;">Pay at Restaurant (Cash)</span>
            </label>
        </div>

        <button type="submit" name="pay" id="payBtn" class="btn-pay">Proceed</button>
    </form>
</div>

<script>
    // Initialize button text if a radio is somehow pre-selected
    document.addEventListener("DOMContentLoaded", function() {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if(checked) updateButtonText();
    });
</script>

</body>
</html>