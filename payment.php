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
<title>Payment</title>

<style>
body{font-family:Arial;background:#f4f4f4;text-align:center;}

.box{
    background:white;
    padding:30px;
    margin:50px auto;
    width:350px;
    border-radius:10px;
}

button{
    background:#7a1f2b;
    color:white;
    padding:10px;
    border:none;
    width:100%;
    margin-top:15px;
}
</style>
</head>

<body>

<div class="box">
<h2>Payment</h2>

<p><b>Order ID:</b> <?php echo $order_id;?></p>
<p><b>Total Amount:</b> ₹<?php echo $order['total_amount'];?></p>

<form method="POST">

<label><input type="radio" name="payment_method" value="UPI" required> UPI</label><br><br>
<label><input type="radio" name="payment_method" value="Card"> Card</label><br><br>
<label><input type="radio" name="payment_method" value="Cash"> Cash</label><br><br>

<button name="pay">Pay Now</button>

</form>
</div>

</body>
</html>