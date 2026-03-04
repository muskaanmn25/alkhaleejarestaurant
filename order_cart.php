<?php
session_start();
include "db.php";

// Initialize cart if not exists
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// Add to cart
if(isset($_POST['add_to_cart'])){
    $menu_id = $_POST['menu_id'];
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    
    if(isset($_SESSION['cart'][$menu_id])){
        $_SESSION['cart'][$menu_id] += $quantity;
    } else {
        $_SESSION['cart'][$menu_id] = $quantity;
    }
    header("Location: order_cart.php");
    exit();
}

// Remove from cart
if(isset($_GET['remove'])){
    $menu_id = $_GET['remove'];
    unset($_SESSION['cart'][$menu_id]);
    header("Location: order_cart.php");
    exit();
}

// Update cart
if(isset($_POST['update_cart'])){
    foreach($_POST['quantity'] as $menu_id => $qty){
        if($qty > 0){
            $_SESSION['cart'][$menu_id] = $qty;
        } else {
            unset($_SESSION['cart'][$menu_id]);
        }
    }
    header("Location: order_cart.php");
    exit();
}

// Calculate total
$cart_items = [];
$total = 0;

foreach($_SESSION['cart'] as $menu_id => $qty){
    $item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu WHERE menu_id=$menu_id"));
    if($item){
        $item['quantity'] = $qty;
        $item['subtotal'] = $item['price'] * $qty;
        $cart_items[] = $item;
        $total += $item['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shopping Cart - Al-Khaleej Arabian Restaurant</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:#f4efec;
    padding:20px;
}

.container{
    max-width:900px;
    margin:0 auto;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
    background:white;
    padding:20px;
    border-radius:10px;
}

.header h1{
    font-family:'Playfair Display', serif;
    color:#7a1f2b;
}

.header a{
    background:#7a1f2b;
    color:white;
    padding:10px 20px;
    text-decoration:none;
    border-radius:5px;
}

.cart-content{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
}

.cart-items{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

.cart-summary{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    height:fit-content;
}

.cart-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 0;
    border-bottom:1px solid #eee;
}

.item-info{
    flex:1;
}

.item-info h3{
    color:#7a1f2b;
    margin-bottom:5px;
}

.item-info p{
    color:#999;
    font-size:13px;
}

.item-controls{
    display:flex;
    gap:10px;
    align-items:center;
}

.qty-input{
    width:60px;
    padding:5px;
    border:1px solid #ddd;
    border-radius:5px;
    text-align:center;
}

.remove-btn{
    background:#FFB6C1;
    color:#c00;
    border:none;
    padding:5px 10px;
    border-radius:5px;
    cursor:pointer;
}

.item-price{
    font-weight:bold;
    color:#7a1f2b;
    min-width:80px;
    text-align:right;
}

.empty-cart{
    text-align:center;
    padding:40px;
    color:#999;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    padding:10px 0;
    border-bottom:1px solid #eee;
}

.summary-row.total{
    font-weight:bold;
    font-size:18px;
    color:#7a1f2b;
    border:none;
    margin-top:10px;
    padding-top:15px;
    border-top:2px solid #eee;
}

.checkout-btn{
    width:100%;
    padding:15px;
    background:#7a1f2b;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
    margin-top:20px;
    font-weight:600;
}

.checkout-btn:hover{
    background:#9e2f3d;
}

.checkout-btn:disabled{
    background:#ccc;
    cursor:not-allowed;
}

.continue-shopping{
    text-align:center;
    margin-top:15px;
}

.continue-shopping a{
    color:#7a1f2b;
    text-decoration:none;
}

@media (max-width: 700px){
    .cart-content{
        grid-template-columns:1fr;
    }
}
</style>

</head>
<body>

<div class="container">

    <div class="header">
        <h1>🛒 Your Cart</h1>
        <a href="customer_menu.php">← Continue Shopping</a>
    </div>

    <div class="cart-content">
        <div class="cart-items">
            <?php if(count($cart_items) > 0): ?>
                
                <form method="POST">
                    <?php foreach($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-info">
                            <h3><?php echo $item['item_name']; ?></h3>
                            <p><?php echo ucfirst($item['category']); ?></p>
                        </div>
                        <div class="item-controls">
                            <input type="number" name="quantity[<?php echo $item['menu_id']; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" min="1" class="qty-input">
                            <span class="item-price">AED <?php echo number_format($item['subtotal'], 2); ?></span>
                            <a href="?remove=<?php echo $item['menu_id']; ?>" class="remove-btn">Remove</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" name="update_cart" style="width:100%; padding:10px; background:#7a1f2b; color:white; border:none; border-radius:5px; margin-top:20px;">Update Cart</button>
                </form>

            <?php else: ?>
                <div class="empty-cart">
                    <h3>Your cart is empty 😢</h3>
                    <p>Add some delicious items to get started!</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="cart-summary">
            <h3 style="color:#7a1f2b; margin-bottom:20px;">Order Summary</h3>
            
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>AED <?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery:</span>
                <span>AED 25.00</span>
            </div>
            <div class="summary-row">
                <span>Tax (5%):</span>
                <span>AED <?php echo number_format($total * 0.05, 2); ?></span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>AED <?php echo number_format($total + 25 + ($total * 0.05), 2); ?></span>
            </div>

            <?php if(count($cart_items) > 0): ?>
                <a href="place_order.php?total=<?php echo $total + 25 + ($total * 0.05); ?>" style="text-decoration:none;">
                    <button class="checkout-btn">Proceed to Checkout</button>
                </a>
            <?php else: ?>
                <button class="checkout-btn" disabled>Proceed to Checkout</button>
            <?php endif; ?>

            <div class="continue-shopping">
                <a href="customer_menu.php">← Continue Shopping</a>
            </div>
        </div>
    </div>

</div>

</body>
</html>