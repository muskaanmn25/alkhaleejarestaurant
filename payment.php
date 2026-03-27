<?php
session_start();
$conn = mysqli_connect("localhost","root","","alkhaleej_db");

$order_id = $_GET['order_id'];

$order = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM orders WHERE order_id='$order_id'
"));

if(isset($_POST['pay'])){
    $method = $_POST['payment_method'];
    mysqli_query($conn,"
        UPDATE orders 
        SET payment_status='paid', payment_method='$method', status='confirmed'
        WHERE order_id='$order_id'
    ");
    echo "<script>alert('Payment Successful!'); window.location='customer_dashboard.php';</script>";
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
</head>
<body>

<div class="payment-card">
    <h2>Complete Payment</h2>
    
    <div class="order-meta">
        <p>Order ID: <strong>#<?php echo $order_id;?></strong></p>
        <span class="amount">₹<?php echo number_format($order['total_amount'], 2);?></span>
    </div>

    <form method="POST">
        <div class="payment-methods">
            <label class="payment-option">
                <input type="radio" name="payment_method" value="UPI" required>
                <span style="font-weight:500; font-size:16px;">UPI / Wallet</span>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="Card">
                <span style="font-weight:500; font-size:16px;">Credit / Debit Card</span>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="Cash">
                <span style="font-weight:500; font-size:16px;">Cash on Delivery</span>
            </label>
        </div>

        <button name="pay" class="btn-pay">Pay & Place Order</button>
    </form>
</div>

</body>
</html>