<?php
session_start();
include "db.php";

// Protect page
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// ===== DAILY REPORT =====
$daily_query = "SELECT COUNT(*) AS total_orders, SUM(total_amount) AS total_revenue FROM orders WHERE DATE(order_date) = CURDATE()";
$daily_result = mysqli_query($conn, $daily_query);
$daily = mysqli_fetch_assoc($daily_result);

// ===== MONTHLY REPORT =====
$monthly_query = "SELECT COUNT(*) AS total_orders, SUM(total_amount) AS total_revenue FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
$monthly_result = mysqli_query($conn, $monthly_query);
$monthly = mysqli_fetch_assoc($monthly_result);

// ===== ORDER STATUS REPORT =====
$status_query = "SELECT status, COUNT(*) AS total FROM orders GROUP BY status";
$status_result = mysqli_query($conn, $status_query);

// ===== TOP 5 SELLING ITEMS =====
$top_items_query = "SELECT m.item_name, SUM(oi.quantity) AS total_sold FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id GROUP BY m.item_name ORDER BY total_sold DESC LIMIT 5";
$top_items_result = mysqli_query($conn, $top_items_query);
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin - Reports</title>
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
    .logout { background:#7a1f2b; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; }
    
    /* ===== Reports ===== */
    .card-container { display:flex; gap:20px; margin-bottom:40px; flex-wrap:wrap; }
    .card { background:white; padding:25px; border-radius:10px; flex:1; min-width:200px; box-shadow:0 5px 15px rgba(0,0,0,0.05); text-align:center; transition:0.3s; }
    .card:hover { transform:translateY(-5px); }
    .card h3 { margin-bottom:10px; color:#7a1f2b; font-size:16px; font-weight:500; }
    .card p { font-size:28px; font-weight:bold; color:#333; }
    
    .section-title { font-family:'Playfair Display', serif; font-size:24px; margin-bottom:15px; color:#333; }
    .table-container { background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); margin-bottom:30px; }
    table { width:100%; border-collapse:collapse; border-radius:8px; overflow:hidden; }
    th, td { padding:15px; text-align:center; border-bottom:1px solid #ddd; }
    th { background:#7a1f2b; color:white; font-weight:500; }
    tr:last-child td { border-bottom:none; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Al-Khaleej Arabian Restaurant</h2>
    <a href="admin_dash.php">Dashboard</a>
    <a href="manage_menu.php">Manage Menu</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="admin_orders.php">Orders</a>
    <a href="reports.php" style="background:#9e2f3d;">Reports</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>Reports & Analytics</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="card-container">
        <div class="card">
            <h3>Today's Orders</h3>
            <p><?php echo $daily['total_orders'] ?? 0; ?></p>
        </div>
        <div class="card">
            <h3>Today's Revenue</h3>
            <p>₹ <?php echo number_format($daily['total_revenue'] ?? 0, 2); ?></p>
        </div>
        <div class="card">
            <h3>Monthly Orders</h3>
            <p><?php echo $monthly['total_orders'] ?? 0; ?></p>
        </div>
        <div class="card">
            <h3>Monthly Revenue</h3>
            <p>₹ <?php echo number_format($monthly['total_revenue'] ?? 0, 2); ?></p>
        </div>
    </div>

    <!-- GRIDS -->
    <div style="display:flex; gap:30px; flex-wrap:wrap;">
        <div style="flex:1; min-width:300px;">
            <h2 class="section-title">Order Status Summary</h2>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Total Orders</th>
                    </tr>
                    <?php 
                    if(mysqli_num_rows($status_result) > 0){
                        while($row = mysqli_fetch_assoc($status_result)) { ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo ucfirst($row['status']); ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                    <?php } } else { echo "<tr><td colspan='2'>No data</td></tr>"; } ?>
                </table>
            </div>
        </div>

        <div style="flex:1; min-width:300px;">
            <h2 class="section-title">Top 5 Selling Items</h2>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                    </tr>
                    <?php 
                    if(mysqli_num_rows($top_items_result) > 0){
                        while($row = mysqli_fetch_assoc($top_items_result)) { ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo $row['item_name']; ?></td>
                        <td><?php echo $row['total_sold']; ?></td>
                    </tr>
                    <?php } } else { echo "<tr><td colspan='2'>No data</td></tr>"; } ?>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>