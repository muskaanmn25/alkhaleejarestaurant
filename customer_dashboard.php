<?php
session_start();
$conn = mysqli_connect("localhost","root","","alkhaleej_db");

if(!isset($_SESSION['customer'])){
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer'];

/* ================= GET CUSTOMER CART ================= */
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
}

/* ================= PLACE ORDER ================= */
if(isset($_POST['place_order'])){

    $items = mysqli_query($conn,"SELECT ci.*, m.price 
            FROM cart_items ci
            JOIN menu m ON ci.menu_id = m.menu_id
            WHERE ci.cart_id='$cart_id'");

    $total = 0;
    while($row = mysqli_fetch_assoc($items)){
        $total += $row['price'] * $row['quantity'];
    }

    mysqli_query($conn,"INSERT INTO orders
        (customer_id,reservation_id,order_date,order_type,status,total_amount)
        VALUES('$customer_id',NULL,NOW(),'dine_in','pending','$total')");

    mysqli_query($conn,"DELETE FROM cart_items WHERE cart_id='$cart_id'");

    echo "<script>alert('Order Placed Successfully!');</script>";
}
?>