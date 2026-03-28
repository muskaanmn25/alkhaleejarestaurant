<?php
session_start();
require_once "db.php";

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    $order_query = mysqli_query($conn, "SELECT * FROM orders WHERE order_id='$order_id'");
    if (mysqli_num_rows($order_query) == 0) {
        echo "<script>alert('Order not found!'); window.location='customer_dashboard.php';</script>";
        exit();
    }
    $order = mysqli_fetch_assoc($order_query);
} else {
    header("Location: customer_dashboard.php");
    exit();
}

if (isset($_POST['paid'])) {
    $total = $order['total_amount'];
    $customer_id = $order['customer_id'];
    
    // Log transaction in dedicated payments table
    mysqli_query($conn,"INSERT INTO payments (order_id, customer_id, amount, method, status) VALUES ('$order_id', '$customer_id', '$total', 'UPI', 'Pending')");

    // Update order status
    mysqli_query($conn, "
        UPDATE orders 
        SET status='confirmed'
        WHERE order_id='$order_id'
    ");

    echo "<script>alert('Payment proof submitted successfully! Your order will be verified shortly.'); window.location='customer_dashboard.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>UPI Payment - Al-Khaleej</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4efec;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .payment-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .payment-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #111;
            margin-bottom: 10px;
        }

        .payment-card p.subtitle {
            color: #777;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .order-meta {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .amount {
            font-size: 36px;
            font-weight: 600;
            color: #7a1f2b;
            display: block;
        }

        .qr-container {
            margin: 20px auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 15px;
            display: inline-block;
            border: 1px dashed #ccc;
        }

        .qr-container img {
            width: 200px;
            height: 200px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 14px;
            color: #333;
        }

        .form-group input[type="file"] {
            width: 100%;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
            background: #fafafa;
        }

        .btn-pay {
            width: 100%;
            padding: 15px;
            background: #7a1f2b;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(122, 31, 43, 0.3);
            transition: 0.3s;
        }

        .btn-pay:hover {
            background: #5c1520;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(122, 31, 43, 0.4);
        }
    </style>
</head>

<body>

    <div class="payment-card">
        <h2>Scan & Pay</h2>
        <p class="subtitle">Complete your payment securely via UPI</p>

        <div class="order-meta">
            <span class="amount">₹<?php echo number_format($order['total_amount'], 2); ?></span>
            <p style="font-size:13px; color:#555; margin-top:5px;">Order ID:
                <strong>#<?php echo htmlspecialchars($order_id); ?></strong></p>
        </div>

        <div class="qr-container">
            <?php
            // The entire UPI string must be fully URL encoded so the QR generator doesn't strip the &am (amount) and &cu parameters!
            $upi_id = "muskaanmn25@oksbi";
            $name = "Muskaan Muskaan2005";
            $amount = $order['total_amount'];

            $upi_string = "upi://pay?pa={$upi_id}&pn=" . rawurlencode($name) . "&am={$amount}&cu=INR";
            $qr_data = rawurlencode($upi_string);
            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . $qr_data;
            ?>
            <img src="<?php echo htmlspecialchars($qr_url); ?>" alt="UPI QR Code">
        </div>

        <form method="POST">
            <button type="submit" name="paid" class="btn-pay">I have paid</button>
        </form>
    </div>

</body>

</html>