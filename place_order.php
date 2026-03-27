<?php
session_start();
include "db.php";

// 1️⃣ Validate customer login
if(!isset($_SESSION['customer_id'])){
    die("<h2>Please login first!</h2><a href='customer_login.php'>Login</a>");
}

$customer_id = intval($_SESSION['customer_id']);

// 2️⃣ Fetch customer's cart
$cart_res = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id='$customer_id'");
$cart = mysqli_fetch_assoc($cart_res);

if(!$cart){
    die("<h2>Your cart is empty!</h2><a href='mainpage.html'>Go Back</a>");
}

$cart_id = $cart['cart_id'];

// 3️⃣ Fetch cart items
$cart_items_res = mysqli_query($conn, "SELECT ci.*, m.price 
                                      FROM cart_items ci 
                                      JOIN menu m ON ci.menu_id = m.menu_id 
                                      WHERE ci.cart_id='$cart_id'");

if(mysqli_num_rows($cart_items_res) == 0){
    die("<h2>No items in cart!</h2><a href='mainpage.html'>Go Back</a>");
}

// 4️⃣ Calculate total
$total = 0;
$cart_items = [];
while($row = mysqli_fetch_assoc($cart_items_res)){
    $row['total_price'] = $row['quantity'] * $row['price'];
    $total += $row['total_price'];
    $cart_items[] = $row;
}

// 5️⃣ Insert order
$order_date = date('Y-m-d H:i:s');
$order_type = 'Delivery'; // or Dine-in
$status = 'pending';

$order_query = "INSERT INTO orders (customer_id, order_date, order_type, total_amount, status) 
                VALUES ('$customer_id', '$order_date', '$order_type', '$total', '$status')";

if(!mysqli_query($conn, $order_query)){
    die("Failed to create order: " . mysqli_error($conn));
}

$order_id = mysqli_insert_id($conn);

// 6️⃣ Insert order_items
foreach($cart_items as $item){
    $menu_id = $item['menu_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];

    $item_query = "INSERT INTO order_items (order_id, menu_id, quantity, price) 
                   VALUES ('$order_id', '$menu_id', '$quantity', '$price')";
    if(!mysqli_query($conn, $item_query)){
        die("Failed to insert order item: " . mysqli_error($conn));
    }
}

// 7️⃣ Clear cart
mysqli_query($conn, "DELETE FROM cart_items WHERE cart_id='$cart_id'");
mysqli_query($conn, "DELETE FROM cart WHERE cart_id='$cart_id'");

// 8️⃣ Redirect to payment
header("Location: payment.php?order_id=$order_id");
exit();
?>