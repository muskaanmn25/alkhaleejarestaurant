<?php
require_once "db.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Our Menu - Al-Khaleej</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#f4efec; color:#333; }

/* NAVBAR */
.navbar { background:#7a1f2b; padding:15px 40px; display:flex; justify-content:space-between; align-items:center; color:white; box-shadow:0 4px 10px rgba(0,0,0,0.1); position:sticky; top:0; z-index:100; }
.navbar h1 { font-family:'Playfair Display', serif; font-size:24px; cursor:pointer;}
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
.menu-card { background:white; border-radius:15px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.05); transition:0.3s; display:flex; flex-direction:column; padding:20px;}
.menu-card:hover { transform:translateY(-8px); box-shadow:0 12px 25px rgba(0,0,0,0.1); }
.menu-title-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.menu-title { font-family:'Playfair Display', serif; font-size:20px; font-weight:600; color:#111; }
.menu-price { font-size:18px; font-weight:600; color:#7a1f2b; }
.menu-desc { font-size:14px; color:#777; margin-bottom:20px; flex:1; }

.login-btn { display:inline-block; background:#7a1f2b; color:white; border:none; padding:10px; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; transition:0.3s; text-decoration:none; text-align:center; margin-top:auto;}
.login-btn:hover { background:#5c1520; }

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
</script>
</head>
<body>

<div class="navbar">
    <h1 onclick="window.location.href='mainpage.html'">Al-Khaleej</h1>
    <div class="nav-links">
        <a href="mainpage.html">Home</a>
    </div>
</div>

<div class="hero">
    <h2>Our Menu</h2>
    <p>Discover the authentic taste of Arabian cuisine</p>
</div>

<!-- MENU SECTION -->
<div class="menu-section">

<?php
$cat_query=mysqli_query($conn,"SELECT DISTINCT category FROM menu WHERE status='available'");
$i=0;
?>
<div class="tabs-container">
<?php while($cat=mysqli_fetch_assoc($cat_query)){ 
$cat_id=str_replace(' ','_',$cat['category']); ?>
    <button class="tab-btn <?php if($i==0) echo 'active';?>" onclick="showCategory('<?php echo $cat_id;?>',this)">
        <?php echo ucfirst($cat['category']);?>
    </button>
<?php $i++; } ?>
</div>

<?php 
mysqli_data_seek($cat_query,0); 
$i=0;
while($cat=mysqli_fetch_assoc($cat_query)){
    $cat_id=str_replace(' ','_',$cat['category']); 
?>
    <div id="<?php echo $cat_id;?>" class="category-box" style="display:<?php echo ($i==0)?'block':'none'; ?>">
        <div class="menu-container">
        <?php
        $q=mysqli_query($conn,"SELECT * FROM menu WHERE category='".$cat['category']."' AND status='available'");
        while($m=mysqli_fetch_assoc($q)){ 
        ?>
            <div class="menu-card">
                <div class="menu-title-row">
                    <span class="menu-title"><?php echo $m['item_name'];?></span>
                    <span class="menu-price">₹<?php echo $m['price'];?></span>
                </div>
                
                <div class="menu-desc"><?php echo rtrim(!empty($m['description']) ? $m['description'] : "Delicious authentic ".$m['item_name'], "."); ?>.</div>
                
                <a href="customer_login.php" class="login-btn">Login to Order</a>
            </div>
        <?php } ?>
        </div>
    </div>
<?php $i++; } ?>
</div>

</body>
</html>
