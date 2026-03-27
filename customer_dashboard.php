<?php
session_start();
$conn = mysqli_connect("localhost","root","","alkhaleej_db");

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

if(!isset($_SESSION['customer_id'])){
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_email = $_SESSION['customer_email'];
$customer_username = explode("@", $customer_email)[0];

/* ===== CART CREATE ===== */
$cart_query = mysqli_query($conn,"SELECT * FROM cart WHERE customer_id='$customer_id' ORDER BY cart_id DESC LIMIT 1");
if(mysqli_num_rows($cart_query) > 0){
    $cart = mysqli_fetch_assoc($cart_query);
    $cart_id = $cart['cart_id'];
} else {
    mysqli_query($conn,"INSERT INTO cart(customer_id) VALUES('$customer_id')");
    $cart_id = mysqli_insert_id($conn);
}

/* ===== CART COUNT ===== */
$count_query = mysqli_query($conn,"SELECT SUM(quantity) as total FROM cart_items WHERE cart_id='$cart_id'");
$count_data = mysqli_fetch_assoc($count_query);
$cart_count = $count_data['total'] ?? 0;

/* ===== ADD TO CART ===== */
if(isset($_POST['add_to_cart'])){
    $menu_id = intval($_POST['menu_id']);
    $qty = intval($_POST['quantity']);

    $check = mysqli_query($conn,"SELECT * FROM cart_items WHERE cart_id='$cart_id' AND menu_id='$menu_id'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart_items SET quantity=quantity+$qty WHERE cart_id='$cart_id' AND menu_id='$menu_id'");
    } else {
        mysqli_query($conn,"INSERT INTO cart_items(cart_id,menu_id,quantity) VALUES('$cart_id','$menu_id','$qty')");
    }
    header("Location: customer_dashboard.php");
    exit();
}

/* ===== REMOVE/UPDATE QTY ===== */
if(isset($_GET['remove'])){
    $id = intval($_GET['remove']);
    mysqli_query($conn,"DELETE FROM cart_items WHERE items_id='$id' AND cart_id='$cart_id'");
    header("Location: customer_dashboard.php?view=cart");
    exit();
}

if(isset($_GET['inc'])){
    $id = intval($_GET['inc']);
    mysqli_query($conn,"UPDATE cart_items SET quantity=quantity+1 WHERE items_id='$id' AND cart_id='$cart_id'");
    header("Location: customer_dashboard.php?view=cart");
    exit();
}

if(isset($_GET['dec'])){
    $id = intval($_GET['dec']);
    $q = mysqli_query($conn,"SELECT quantity FROM cart_items WHERE items_id='$id'");
    $d = mysqli_fetch_assoc($q);
    if($d['quantity'] > 1){
        mysqli_query($conn,"UPDATE cart_items SET quantity=quantity-1 WHERE items_id='$id'");
    }
    header("Location: customer_dashboard.php?view=cart");
    exit();
}

/* ===== PLACE ORDER ===== */
if(isset($_POST['place_order'])){

    // Fetch all cart items
    $cart_items_res = mysqli_query($conn,"
        SELECT ci.*, m.price 
        FROM cart_items ci
        JOIN menu m ON ci.menu_id = m.menu_id
        WHERE ci.cart_id='$cart_id'
    ");

    if(mysqli_num_rows($cart_items_res) == 0){
        echo "<script>alert('Your cart is empty!');</script>";
    } else {
        $total = 0;
        $cart_items = [];
        while($row = mysqli_fetch_assoc($cart_items_res)){
            $row['total_price'] = $row['quantity'] * $row['price'];
            $total += $row['total_price'];
            $cart_items[] = $row;
        }

        $order_date = date('Y-m-d H:i:s');
        $order_type = 'Delivery'; // or 'Dine-in'
        $status = 'pending';

        // Insert into orders table
        $order_query = mysqli_query($conn,"
            INSERT INTO orders (customer_id, order_date, order_type, total_amount, status)
            VALUES ('$customer_id', '$order_date', '$order_type', '$total', '$status')
        ");

        if($order_query){
            $order_id = mysqli_insert_id($conn);

            // Insert into order_items
            foreach($cart_items as $item){
                mysqli_query($conn,"
                    INSERT INTO order_items (order_id, menu_id, quantity, price)
                    VALUES ('$order_id', '{$item['menu_id']}', '{$item['quantity']}', '{$item['price']}')
                ");
            }

            // Clear cart items AND cart
            mysqli_query($conn,"DELETE ci, c FROM cart c LEFT JOIN cart_items ci ON ci.cart_id=c.cart_id WHERE c.cart_id='$cart_id'");

            // Reset floating cart counter
            $cart_count = 0;

            // Redirect to payment page
            header("Location: payment.php?order_id=$order_id");
            exit();
        } else {
            echo "<script>alert('Failed to place order: ".mysqli_error($conn)."');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<style>
body{font-family:Arial;margin:0;background:#f4f4f4;}
.header{background:#7a1f2b;color:white;padding:15px;display:flex;justify-content:space-between;}
.tabs{display:flex;gap:10px;padding:15px;}
.tab-btn{padding:10px 20px;border:none;border-radius:20px;background:#ddd;cursor:pointer;}
.tab-btn.active{background:#7a1f2b;color:white;}
.menu-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;padding:20px;}
.menu-card{background:white;padding:15px;border-radius:10px;box-shadow:0 2px 5px rgba(0,0,0,0.2);}
.menu-price{float:right;color:#7a1f2b;font-weight:bold;}
.add-btn{width:100%;margin-top:10px;padding:8px;background:#7a1f2b;color:white;border:none;border-radius:5px;}
.qty-box{display:flex;justify-content:center;gap:10px;margin-top:10px;}
.qty-box button{width:30px;height:30px;background:#7a1f2b;color:white;border:none;border-radius:5px;}
.qty-box input{width:40px;text-align:center;}
.cart-section{padding:20px;}
table{width:100%;background:white;border-radius:10px;overflow:hidden;}
th,td{padding:10px;text-align:center;}
th{background:#7a1f2b;color:white;}
.action-btn{color:#7a1f2b;text-decoration:none;font-weight:bold;}
.floating-cart{position:fixed;bottom:20px;right:20px;background:#7a1f2b;color:white;padding:12px 18px;border-radius:50px;cursor:pointer;font-weight:bold;box-shadow:0 4px 10px rgba(0,0,0,0.3);}
</style>
<script>
function showCategory(id,btn){
    document.querySelectorAll('.category-box').forEach(div=>div.style.display='none');
    document.getElementById(id).style.display='block';
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
}
function showCart(){
    document.getElementById('menuSection').style.display='none';
    document.getElementById('cartSection').style.display='block';
}
function showMenu(){
    document.getElementById('cartSection').style.display='none';
    document.getElementById('menuSection').style.display='block';
}
function increaseQty(btn){let i=btn.parentElement.querySelector("input"); i.value=parseInt(i.value)+1;}
function decreaseQty(btn){let i=btn.parentElement.querySelector("input"); if(i.value>1) i.value=parseInt(i.value)-1;}
window.onload = function(){const params = new URLSearchParams(window.location.search); if(params.get('view') === 'cart'){showCart();}}
</script>
</head>
<body>

<div class="header">
<h3>Welcome <?php echo $customer_username;?></h3>
<a href="logout.php" style="color:white;">Logout</a>
</div>

<!-- MENU -->
<div id="menuSection">
<h2 style="padding-left:20px;">Menu</h2>

<?php
$cat_query=mysqli_query($conn,"SELECT DISTINCT category FROM menu WHERE status='available'");
$i=0;
?>
<div class="tabs">
<?php while($cat=mysqli_fetch_assoc($cat_query)){ 
$cat_id=str_replace(' ','_',$cat['category']); ?>
<button class="tab-btn <?php if($i==0) echo 'active';?>" onclick="showCategory('<?php echo $cat_id;?>',this)">
<?php echo $cat['category'];?>
</button>
<?php $i++; } ?>
</div>

<?php mysqli_data_seek($cat_query,0); $i=0;
while($cat=mysqli_fetch_assoc($cat_query)){
$cat_id=str_replace(' ','_',$cat['category']); ?>
<div id="<?php echo $cat_id;?>" class="category-box" style="display:<?php echo ($i==0)?'block':'none'; ?>">

<div class="menu-container">
<?php
$q=mysqli_query($conn,"SELECT * FROM menu WHERE category='".$cat['category']."' AND status='available'");
while($m=mysqli_fetch_assoc($q)){ ?>
<div class="menu-card">
<b><?php echo $m['item_name'];?></b>
<span class="menu-price">₹<?php echo $m['price'];?></span>

<form method="POST">
<input type="hidden" name="menu_id" value="<?php echo $m['menu_id'];?>">

<div class="qty-box">
<button type="button" onclick="decreaseQty(this)">-</button>
<input type="number" name="quantity" value="1">
<button type="button" onclick="increaseQty(this)">+</button>
</div>

<button name="add_to_cart" class="add-btn">Add to Cart</button>
</form>
</div>
<?php } ?>
</div>
</div>
<?php $i++; } ?>
</div>

<!-- CART -->
<div id="cartSection" style="display:none;" class="cart-section">

<div style="display:flex;justify-content:space-between;">
<h2>Your Cart</h2>
<button onclick="showMenu()">← Back</button>
</div>

<table>
<tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th></tr>

<?php
$total=0;
$q=mysqli_query($conn,"SELECT ci.*,m.item_name,m.price FROM cart_items ci JOIN menu m ON ci.menu_id=m.menu_id WHERE ci.cart_id='$cart_id'");
while($item=mysqli_fetch_assoc($q)){
$t=$item['price']*$item['quantity'];
$total+=$t;
?>

<tr>
<td><?php echo $item['item_name'];?></td>
<td>₹<?php echo $item['price'];?></td>
<td>
<a href="?dec=<?php echo $item['items_id'];?>&view=cart">➖</a>
<?php echo $item['quantity'];?>
<a href="?inc=<?php echo $item['items_id'];?>&view=cart">➕</a>
</td>
<td>₹<?php echo $t;?></td>
<td><a class="action-btn" href="?remove=<?php echo $item['items_id'];?>&view=cart">Remove</a></td>
</tr>

<?php } ?>

<tr>
<td colspan="3">Total</td>
<td colspan="2">₹<?php echo $total;?></td>
</tr>

</table>

<form method="POST">
<button name="place_order">Place Order</button>
</form>

</div>

<!-- FLOAT -->
<div class="floating-cart" onclick="showCart()">
🛒 <span id="cartCounter"><?php echo $cart_count;?></span>
</div>

<script>
// Update floating cart counter dynamically after order
<?php if(isset($_POST['place_order']) && $cart_count==0){ ?>
document.getElementById('cartCounter').textContent = '0';
<?php } ?>
</script>

</body>
</html>