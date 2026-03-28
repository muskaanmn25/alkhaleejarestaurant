<?php
session_start();

if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit();
}

require_once "db.php";

if(!isset($_GET['order_id'])){
    header("Location: staff_orders.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order information
$order_query = mysqli_query($conn,"SELECT * FROM orders WHERE order_id='$order_id'");
$order = mysqli_fetch_assoc($order_query);

// Fetch ordered items with menu details
$items_query = mysqli_query($conn,"SELECT oi.quantity, m.item_name, m.price 
                                  FROM order_items oi
                                  JOIN menu m ON oi.menu_id = m.menu_id
                                  WHERE oi.order_id='$order_id'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Order #<?php echo $order_id; ?> - Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        body { display:flex; background:#f4efec; min-height: 100vh; }
        /* ===== Sidebar ===== */
        .sidebar { width:250px; background:#7a1f2b; color:white; padding:30px 20px; }
        .sidebar h2 { font-family:'Playfair Display', serif; margin-bottom:40px; text-align:center; }
        .sidebar a { display:block; color:white; text-decoration:none; padding:12px; margin-bottom:10px; border-radius:6px; transition:0.3s; }
        .sidebar a:hover { background:#9e2f3d; }
        /* ===== Main Content ===== */
        .main { flex:1; padding:30px 40px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .topbar h1 { font-family:'Playfair Display', serif; }
        
        .card { background:white; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); max-width: 800px;}
        .card h2 { font-family:'Playfair Display', serif; margin-bottom:20px; color:#333; }
        .card p { margin-bottom:10px; font-size:15px; color:#555;}
        .card p strong { color:#333; }
        
        table { width:100%; border-collapse:collapse; margin-top:25px; margin-bottom:25px;}
        th, td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
        th { background:#7a1f2b; color:white; font-weight:500; }
        .btn-back { display:inline-block; padding:10px 20px; background:#7a1f2b; color:white; text-decoration:none; border-radius:5px; transition:0.3s; }
        .btn-back:hover { background:#631723; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Al-Khaleej Staff Panel</h2>
    <a href="staff_dashboard.php">Dashboard</a>
    <a href="staff_orders.php" style="background:#9e2f3d;">Manage Orders</a>
    <a href="staff_reservations.php">Manage Reservations</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>Order Details</h1>
    </div>

    <div class="card">
        <h2>Order #<?php echo $order_id; ?> Details</h2>

        <p><strong>Customer ID:</strong> <?php echo $order['customer_id']; ?></p>
        <p><strong>Order Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
        <p><strong>Order Type:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['order_type'])); ?></p>
        <p><strong>Total Amount:</strong> <span style="font-weight:600; font-size:18px; color:#7a1f2b;">₹ <?php echo $order['total_amount']; ?></span></p>

        <h3 style="margin-top:30px; font-family:'Playfair Display', serif;">Items Ordered</h3>
        <table>
        <tr>
            <th>Menu Item</th>
            <th style="text-align:center;">Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>

        <?php 
        while($item = mysqli_fetch_assoc($items_query)){ 
            $subtotal = $item['quantity'] * $item['price'];
        ?>
        <tr>
            <td><?php echo $item['item_name']; ?></td>
            <td style="text-align:center;"><?php echo $item['quantity']; ?></td>
            <td>₹ <?php echo $item['price']; ?></td>
            <td style="font-weight:500;">₹ <?php echo $subtotal; ?></td>
        </tr>
        <?php } ?>
        </table>

        <a href="staff_orders.php" class="btn-back">← Back to Orders</a>
    </div>

</div>

</body>
</html>