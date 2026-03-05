<?php
session_start();
$conn = mysqli_connect("localhost","root","","alkhaleej_db");

if(!isset($_SESSION['customer_id'])){
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'];

/* ================= GET OR CREATE CART ================= */

$cart_query = mysqli_query($conn,"SELECT * FROM cart WHERE customer_id='$customer_id'");

if(mysqli_num_rows($cart_query) > 0){
    $cart = mysqli_fetch_assoc($cart_query);
    $cart_id = $cart['id'];
}else{
    mysqli_query($conn,"INSERT INTO cart(customer_id,created_at) VALUES('$customer_id',NOW())");
    $cart_id = mysqli_insert_id($conn);
}

/* ================= ADD TO CART ================= */

if(isset($_GET['add'])){
    $menu_id = $_GET['add'];

    $check = mysqli_query($conn,"SELECT * FROM cart_items 
        WHERE cart_id='$cart_id' AND menu_id='$menu_id'");

    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart_items 
            SET quantity = quantity + 1
            WHERE cart_id='$cart_id' AND menu_id='$menu_id'");
    }else{
        mysqli_query($conn,"INSERT INTO cart_items(cart_id,menu_id,quantity)
            VALUES('$cart_id','$menu_id',1)");
    }

    header("Location: customer_dashboard.php");
    exit();
}

/* ================= REMOVE ITEM ================= */

if(isset($_GET['remove'])){
    $remove_id = $_GET['remove'];

    mysqli_query($conn,"DELETE FROM cart_items 
        WHERE id='$remove_id' AND cart_id='$cart_id'");

    header("Location: customer_dashboard.php");
    exit();
}

/* ================= PLACE ORDER ================= */

if(isset($_POST['place_order'])){

    $order_type = "dine-in";

    $cart_items = mysqli_query($conn,"
        SELECT ci.*, m.price 
        FROM cart_items ci
        JOIN menu m ON ci.menu_id=m.menu_id
        WHERE ci.cart_id='$cart_id'
    ");

    $total = 0;

    while($item=mysqli_fetch_assoc($cart_items)){
        $total += $item['price'] * $item['quantity'];
    }

    mysqli_query($conn,"
        INSERT INTO orders(customer_id,order_date,order_type,total_amount,status)
        VALUES('$customer_id',NOW(),'$order_type','$total','pending')
    ");

    $order_id = mysqli_insert_id($conn);

    $cart_items = mysqli_query($conn,"
        SELECT * FROM cart_items
        WHERE cart_id='$cart_id'
    ");

    while($item=mysqli_fetch_assoc($cart_items)){
        mysqli_query($conn,"
            INSERT INTO order_items(order_id,menu_id,quantity)
            VALUES('$order_id','".$item['menu_id']."','".$item['quantity']."')
        ");
    }

    mysqli_query($conn,"DELETE FROM cart_items WHERE cart_id='$cart_id'");

    echo "<script>alert('Order placed successfully! Staff will prepare it.');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Dashboard</title>

<style>

body{
    font-family: Arial;
    margin:0;
    background:#f5f5f5;
}

/* Header */

.header{
    background:#7a1f2b;
    color:white;
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header a{
    color:white;
    text-decoration:none;
    font-weight:bold;
}

/* Menu Section */

.menu-container{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    padding:30px;
}

.menu-card{
    background:white;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    padding:20px;
    transition:0.3s;
}

.menu-card:hover{
    transform:translateY(-5px);
}

.menu-title{
    font-size:18px;
    font-weight:bold;
}

.menu-price{
    color:#7a1f2b;
    font-weight:bold;
    float:right;
}

.add-btn{
    margin-top:15px;
    width:100%;
    padding:10px;
    background:#7a1f2b;
    border:none;
    color:white;
    border-radius:5px;
    cursor:pointer;
}

.add-btn:hover{
    background:#5d1620;
}

/* Cart Section */

.cart-section{
    padding:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

th{
    background:#7a1f2b;
    color:white;
}

.remove-btn{
    color:red;
    text-decoration:none;
}

.place-btn{
    margin-top:20px;
    padding:12px 25px;
    background:#1f4e5f;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.place-btn:hover{
    background:#163742;
}

</style>

</head>

<body>

<div class="header">

<h2>Welcome, <?php echo $customer_name; ?></h2>

<a href="logout.php">Logout</a>

</div>

<h2 style="padding-left:30px;">🍽 Our Menu</h2>

<div class="menu-container">

<?php
$menu_query = mysqli_query($conn,"SELECT * FROM menu WHERE status='available'");

while($menu = mysqli_fetch_assoc($menu_query)){
?>

<div class="menu-card">

<span class="menu-title">
<?php echo $menu['item_name']; ?>
</span>

<span class="menu-price">
₹<?php echo $menu['price']; ?>
</span>

<br><br>

<a href="?add=<?php echo $menu['menu_id']; ?>">
<button class="add-btn">Add to Cart</button>
</a>

</div>

<?php } ?>

</div>

<div class="cart-section">

<h2>🛒 Your Cart</h2>

<form method="POST">

<table>

<tr>
<th>Item</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
<th>Action</th>
</tr>

<?php

$total = 0;

$cart_items = mysqli_query($conn,"
SELECT ci.*, m.item_name, m.price 
FROM cart_items ci
JOIN menu m ON ci.menu_id = m.menu_id
WHERE ci.cart_id='$cart_id'
");

if(mysqli_num_rows($cart_items) > 0){

while($item = mysqli_fetch_assoc($cart_items)){

$item_total = $item['price'] * $item['quantity'];

$total += $item_total;
?>

<tr>

<td><?php echo $item['item_name']; ?></td>

<td>₹<?php echo $item['price']; ?></td>

<td><?php echo $item['quantity']; ?></td>

<td>₹<?php echo $item_total; ?></td>

<td>
<a class="remove-btn" href="?remove=<?php echo $item['id']; ?>">Remove</a>
</td>

</tr>

<?php } }

else{

echo "<tr><td colspan='5'>Cart is empty</td></tr>";

}

?>

<tr>
<td colspan="3"><b>Grand Total</b></td>
<td colspan="2"><b>₹<?php echo $total; ?></b></td>
</tr>

</table>

<br>

<button class="place-btn" type="submit" name="place_order">
Place Order
</button>

</form>

</div>

</body>
</html>