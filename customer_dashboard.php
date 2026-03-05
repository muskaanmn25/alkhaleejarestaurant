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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Dashboard</title>
</head>
<body>

<h2>Welcome, <?php echo $customer_name; ?></h2>
<a href="logout.php">Logout</a>

<hr>

<h3>🍽 Our Menu</h3>

<?php
$menu_query = mysqli_query($conn,"SELECT * FROM menu WHERE status='available'");

while($menu = mysqli_fetch_assoc($menu_query)){
?>

<div style="margin-bottom:10px;">
    <b><?php echo $menu['item_name']; ?></b> -
    ₹<?php echo $menu['price']; ?>

    <a href="?add=<?php echo $menu['menu_id']; ?>">Add to Cart</a>
</div>

<?php } ?>

<hr>

<h3>🛒 Your Cart</h3>

<table border="1" cellpadding="10">
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
        <a href="?remove=<?php echo $item['id']; ?>">Remove</a>
    </td>
</tr>

<?php
    }
}else{
    echo "<tr><td colspan='5'>Cart is empty</td></tr>";
}
?>

<tr>
    <td colspan="3"><b>Grand Total</b></td>
    <td colspan="2"><b>₹<?php echo $total; ?></b></td>
</tr>

</table>

</body>
</html>