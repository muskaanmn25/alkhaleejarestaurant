<?php
session_start();
include "db.php";

// Protect page
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Get today's orders
$query = "SELECT orders.*, customers.full_name AS customer_name, customers.email, payments.status AS payment_status, payments.method AS payment_method
          FROM orders
          JOIN customers ON orders.customer_id = customers.customer_id
          LEFT JOIN payments ON orders.order_id = payments.order_id
          WHERE DATE(order_date) = CURDATE()
          ORDER BY order_date DESC";
          
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
<title>Today's Orders - Admin Panel</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f4efec;
    min-height: 100vh;
}

/* ===== Sidebar ===== */
.sidebar{
    width:250px;
    background:#7a1f2b;
    color:white;
    padding:30px 20px;
}

.sidebar h2{
    font-family:'Playfair Display', serif;
    margin-bottom:40px;
    text-align:center;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px;
    margin-bottom:10px;
    border-radius:6px;
    transition:0.3s;
}

.sidebar a:hover{
    background:#9e2f3d;
}

/* ===== Main Content ===== */
.main{
    flex:1;
    padding:30px 40px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.topbar h1{
    font-family:'Playfair Display', serif;
}

.logout{
    background:#7a1f2b;
    color:white;
    border:none;
    padding:8px 15px;
    border-radius:5px;
    cursor:pointer;
}

/* ===== Table ===== */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
th, td{
    padding:15px;
    text-align:left;
    border-bottom:1px solid #ddd;
}
th{
    background:#7a1f2b;
    color:white;
    font-weight:500;
}
.status-pending { color: orange; font-weight: bold; }
.status-confirmed { color: #2ecc71; font-weight: bold; }
.status-preparing { color: blue; font-weight: bold; }
.status-completed { color: green; font-weight: bold; }
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Al-Khaleej Arabian Restaurant</h2>
    <a href="admin_dash.php">Dashboard</a>
    <a href="manage_menu.php">Manage Menu</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="admin_orders.php" style="background:#9e2f3d;">Orders</a>
    <a href="reports.php">Reports</a>
</div>

<!-- Main Content -->
<div class="main">

    <div class="topbar">
        <h1>Today's Orders</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Type</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Payment Type</th>
            <th>Order Time</th>
        </tr>

        <?php 
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) { 
                $display_name = !empty($row['customer_name']) ? htmlspecialchars($row['customer_name']) : 'Guest';
                if(strtolower($display_name) == 'guest' && !empty($row['email'])) {
                    $email_parts = explode('@', $row['email']);
                    $display_name = ucfirst(htmlspecialchars($email_parts[0]));
                }
        ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo $display_name; ?></td>
            <td><?php echo ucfirst(str_replace('_', ' ', $row['order_type'])); ?></td>
            <td style="font-weight:600;">₹ <?php echo $row['total_amount']; ?></td>
            <td class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></td>
            <td>
                <?php echo ucfirst($row['payment_status']); ?>
                <?php if(!empty($row['payment_method'])) { echo "<br><small style='color:#777;'>({$row['payment_method']})</small>"; } ?>
            </td>
            <td><?php echo date('h:i A', strtotime($row['order_date'])); ?></td>
        </tr>
        <?php 
            } 
        } else {
        ?>
        <tr>
            <td colspan="6" style="text-align:center; padding: 20px;">No orders found for today.</td>
        </tr>
        <?php } ?>

    </table>

</div>

</body>
</html>