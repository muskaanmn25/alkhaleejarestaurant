<?php
session_start();

if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","alkhaleej_db");
if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

if(!isset($_GET['order_id'])){
    header("Location: staff_orders.php");
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order information
$order_query = mysqli_query($conn,"SELECT * FROM orders WHERE id='$order_id'");
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
    <title>View Order #<?php echo $order_id; ?></title>
    <style>
        body{ font-family: Arial; padding:20px; background:#f4efec; }
        h2{ margin-bottom:15px; }
        p{ margin:5px 0; }
        table{ width:80%; border-collapse:collapse; background:white; margin-top:15px; }
        th, td{ padding:10px; border:1px solid #ddd; text-align:center; }
        th{ background:#7a1f2b; color:white; }
        button{ padding:6px 12px; background:#1f4e5f; color:white; border:none; border-radius:4px; cursor:pointer; margin-top:15px; }
    </style>
</head>
<body>

<h2>Order #<?php echo $order_id; ?> Details</h2>

<p><strong>Customer ID:</strong> <?php echo $order['customer_id']; ?></p>
<p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
<p><strong>Order Type:</strong> <?php echo $order['order_type']; ?></p>
<p><strong>Total Amount:</strong> ₹ <?php echo $order['total_amount']; ?></p>

<h3>Items Ordered</h3>
<table>
<tr>
    <th>Menu Item</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Subtotal</th>
</tr>

<?php 
while($item = mysqli_fetch_assoc($items_query)){ 
    $subtotal = $item['quantity'] * $item['price'];
?>
<tr>
    <td><?php echo $item['item_name']; ?></td>
    <td><?php echo $item['quantity']; ?></td>
    <td>₹ <?php echo $item['price']; ?></td>
    <td>₹ <?php echo $subtotal; ?></td>
</tr>
<?php } ?>
</table>

<a href="staff_orders.php"><button>Back to Orders</button></a>

</body>
</html>