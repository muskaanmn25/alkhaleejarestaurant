<?php
session_start();
require_once "db.php";

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
    $cat_id_post = isset($_POST['cat_id']) ? $_POST['cat_id'] : '';

    $check = mysqli_query($conn,"SELECT * FROM cart_items WHERE cart_id='$cart_id' AND menu_id='$menu_id'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn,"UPDATE cart_items SET quantity=quantity+$qty WHERE cart_id='$cart_id' AND menu_id='$menu_id'");
    } else {
        mysqli_query($conn,"INSERT INTO cart_items(cart_id,menu_id,quantity) VALUES('$cart_id','$menu_id','$qty')");
    }
    
    $redirect = "customer_dashboard.php";
    if(!empty($cat_id_post)) {
        $redirect .= "?cat=" . urlencode($cat_id_post);
    }
    header("Location: " . $redirect);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard - Menu</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#f4efec; color:#333; }

/* NAVBAR */
.navbar { background:#7a1f2b; padding:15px 40px; display:flex; justify-content:space-between; align-items:center; color:white; box-shadow:0 4px 10px rgba(0,0,0,0.1); position:sticky; top:0; z-index:100; }
.navbar h1 { font-family:'Playfair Display', serif; font-size:24px; }
.navbar .nav-links a { color:white; text-decoration:none; margin-left:20px; font-weight:500; transition:0.3s; }
.navbar .nav-links a:hover { color:#ffcccc; }

/* HERO BANNERS */
.hero { background:linear-gradient(rgba(122,31,43,0.8), rgba(122,31,43,0.8)), url('https://images.unsplash.com/photo-1544025162-811114bd4131?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover; padding:60px 40px; text-align:center; color:white; margin-bottom:40px; }
.hero h2 { font-family:'Playfair Display', serif; font-size:42px; margin-bottom:10px; }
.hero p { font-size:18px; font-weight:300; }

/* CATEGORY TABS */
.tabs-container { display:flex; justify-content:center; gap:15px; margin-bottom:40px; flex-wrap:wrap; padding:0 20px;}
.tab-btn { padding:12px 25px; border:none; border-radius:30px; background:white; color:#555; font-size:16px; font-weight:500; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.05); transition:0.3s; }
.tab-btn:hover { transform:translateY(-2px); box-shadow:0 6px 15px rgba(0,0,0,0.1); }
.tab-btn.active { background:#7a1f2b; color:white; }

/* MENU GRID */
.menu-section { max-width:1200px; margin:0 auto; padding:0 20px 80px; }
.menu-container { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:30px; }
.menu-card { background:white; border-radius:15px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.05); transition:0.3s; display:flex; flex-direction:column; }
.menu-card:hover { transform:translateY(-8px); box-shadow:0 12px 25px rgba(0,0,0,0.1); }
.menu-img { width:100%; height:200px; object-fit:cover; background:#f4f4f4; }
.menu-content { padding:20px; display:flex; flex-direction:column; flex:1; }
.menu-title-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.menu-title { font-family:'Playfair Display', serif; font-size:20px; font-weight:600; color:#111; }
.menu-price { font-size:18px; font-weight:600; color:#7a1f2b; }
.menu-desc { font-size:14px; color:#777; margin-bottom:20px; flex:1; }

.add-cart-form { display:flex; flex-direction:column; gap:15px; margin-top:auto;}
.qty-control { display:flex; align-items:center; justify-content:space-between; background:#f9f9f9; border-radius:8px; padding:5px 10px; border:1px solid #eee; }
.qty-btn { width:32px; height:32px; background:white; border:1px solid #ddd; border-radius:5px; cursor:pointer; font-weight:bold; color:#555; transition:0.2s;}
.qty-btn:hover { background:#7a1f2b; color:white; border-color:#7a1f2b; }
.qty-input { width:40px; text-align:center; border:none; background:transparent; font-weight:500; font-size:16px; outline:none; }
.add-btn { width:100%; background:#7a1f2b; color:white; border:none; padding:12px; border-radius:8px; font-size:15px; font-weight:500; cursor:pointer; transition:0.3s; }
.add-btn:hover { background:#5c1520; }

/* FLOATING CART */
.floating-cart { position:fixed; bottom:30px; right:30px; background:#111; color:white; padding:15px 25px; border-radius:50px; text-decoration:none; font-weight:600; display:flex; align-items:center; gap:10px; box-shadow:0 10px 25px rgba(0,0,0,0.3); transition:0.3s; z-index:100; }
.floating-cart:hover { transform:scale(1.05); background:#7a1f2b; }
.cart-badge { background:white; color:#7a1f2b; width:26px; height:26px; border-radius:50%; display:flex; justify-content:center; align-items:center; font-size:14px; font-weight:bold; }

@media(max-width:768px){
    .navbar { padding:15px 20px; }
    .hero { padding:40px 20px; }
    .hero h2 { font-size:32px; }
}
</style>
<script>
function showCategory(id,btn){
    document.querySelectorAll('.category-box').forEach(div=>div.style.display='none');
    document.getElementById(id).style.display='block';
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
}
function increaseQty(btn){let i=btn.parentElement.querySelector("input"); i.value=parseInt(i.value)+1;}
function decreaseQty(btn){let i=btn.parentElement.querySelector("input"); if(i.value>1) i.value=parseInt(i.value)-1;}
</script>
</head>
<body>

<div class="navbar">
    <h1>Al-Khaleej</h1>
    <div class="nav-links">
        <a href="customer_dashboard.php">Menu</a>
        <a href="submit_feedback.php">Feedback</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="hero">
    <h2>Welcome, <?php echo ucfirst($customer_username); ?></h2>
    <p>Discover the authentic taste of Arabian cuisine</p>
</div>

<!-- MENU SECTION -->
<div class="menu-section">

<?php
$cat_query=mysqli_query($conn,"SELECT DISTINCT category FROM menu WHERE status='available'");
$i=0;
$active_cat = isset($_GET['cat']) ? $_GET['cat'] : '';
?>
<div class="tabs-container">
<?php while($cat=mysqli_fetch_assoc($cat_query)){ 
$cat_id=str_replace(' ','_',$cat['category']); 
$is_active = ($active_cat === $cat_id) || ($active_cat === '' && $i === 0);
?>
    <button class="tab-btn <?php if($is_active) echo 'active';?>" onclick="showCategory('<?php echo $cat_id;?>',this)">
        <?php echo ucfirst($cat['category']);?>
    </button>
<?php $i++; } ?>
</div>

<?php 
mysqli_data_seek($cat_query,0); 
$i=0;
while($cat=mysqli_fetch_assoc($cat_query)){
    $cat_id=str_replace(' ','_',$cat['category']); 
    $is_active = ($active_cat === $cat_id) || ($active_cat === '' && $i === 0);
?>
    <div id="<?php echo $cat_id;?>" class="category-box" style="display:<?php echo $is_active ? 'block':'none'; ?>">
        <div class="menu-container">
        <?php
        $q=mysqli_query($conn,"SELECT * FROM menu WHERE category='".$cat['category']."' AND status='available'");
        while($m=mysqli_fetch_assoc($q)){ 
        ?>
            <div class="menu-card">
                <div class="menu-content">
                    <div class="menu-title-row">
                        <span class="menu-title"><?php echo $m['item_name'];?></span>
                        <span class="menu-price">₹<?php echo $m['price'];?></span>
                    </div>
                    
                    <div class="menu-desc"><?php echo rtrim(!empty($m['description']) ? $m['description'] : "Delicious authentic ".$m['item_name'], "."); ?>.</div>
                    
                    <form method="POST" class="add-cart-form">
                        <input type="hidden" name="menu_id" value="<?php echo $m['menu_id'];?>">
                        <input type="hidden" name="cat_id" value="<?php echo $cat_id;?>">
                        <div class="qty-control">
                            <span style="font-size:14px; color:#777; font-weight:500;">Quantity</span>
                            <div>
                                <button type="button" class="qty-btn" onclick="decreaseQty(this)">-</button>
                                <input type="number" class="qty-input" name="quantity" value="1" readonly>
                                <button type="button" class="qty-btn" onclick="increaseQty(this)">+</button>
                            </div>
                        </div>
                        <button name="add_to_cart" class="add-btn">Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
<?php $i++; } ?>
</div>

<!-- FLOATING CART -->
<a href="cart.php" class="floating-cart">
    <span>🛒 View Cart</span> 
    <div class="cart-badge" id="cartCounter"><?php echo $cart_count;?></div>
</a>

</body>
</html>