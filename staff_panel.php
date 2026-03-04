<?php
session_start();
$conn = mysqli_connect("localhost","root","","alkhaleej_db");

/* LOGIN CHECK */
if(!isset($_SESSION['staff'])){
    header("Location: login.php");
    exit();
}

/* ================= UPDATE ORDER STATUS ================= */
if(isset($_POST['update_status'])){
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    mysqli_query($conn,
        "UPDATE orders SET status='$status' WHERE id='$order_id'"
    );

    header("Location: staff_panel.php?section=orders");
    exit();
}

/* ================= CONFIRM RESERVATION ================= */
if(isset($_GET['confirm_res'])){
    $id = intval($_GET['confirm_res']);

    mysqli_query($conn,
        "UPDATE reservation SET status='confirmed' WHERE id='$id'"
    );

    header("Location: staff_panel.php?section=reservation");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Staff Panel</title>

<style>
body{
    margin:0;
    font-family:Arial;
    display:flex;
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    background:linear-gradient(to bottom,#8b1e2d,#6e1422);
    color:white;
    padding:20px;
}

.sidebar a{
    display:block;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    background:rgba(255,255,255,0.2);
}

.sidebar a:hover{
    background:rgba(255,255,255,0.35);
}

.logout{
    margin-top:40px;
    background:#00000040;
}

/* CONTENT */
.content{
    flex:1;
    padding:30px;
    background:#f4f4f4;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

th{
    background:#eee;
}

select{
    padding:6px;
    border-radius:5px;
}

.status-pending{ color:orange; font-weight:bold;}
.status-preparing{ color:#007bff; font-weight:bold;}
.status-completed{ color:green; font-weight:bold;}

.confirm{
    background:blue;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:5px;
    cursor:pointer;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>👨‍🍳 Staff Panel</h2>

    <a href="?section=orders">📋 Orders</a>
    <a href="?section=reservation">📅 Reservations</a>

    <a href="logout.php" class="logout">🚪 Logout</a>
</div>

<div class="content">

<?php
$section = $_GET['section'] ?? 'orders';


/* ===================================================
   ORDERS SECTION
=================================================== */
if($section == 'orders'){

echo "<h2>Orders Management</h2>";

$result = mysqli_query($conn,
    "SELECT * FROM orders ORDER BY id DESC"
);

echo "<table>
<tr>
<th>Order ID</th>
<th>Customer</th>
<th>Date</th>
<th>Type</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>
</tr>";

while($row = mysqli_fetch_assoc($result)){

$statusClass = "status-".$row['status'];

echo "<tr>
<td>{$row['id']}</td>
<td>{$row['customer_id']}</td>
<td>{$row['order_date']}</td>
<td>{$row['order_type']}</td>
<td>₹ {$row['total_amount']}</td>
<td class='$statusClass'>{$row['status']}</td>

<td>
<form method='POST'>
<input type='hidden' name='order_id' value='{$row['id']}'>

<select name='status' onchange='this.form.submit()'>
<option value='pending' ".($row['status']=='pending'?'selected':'').">Pending</option>
<option value='preparing' ".($row['status']=='preparing'?'selected':'').">Preparing</option>
<option value='completed' ".($row['status']=='completed'?'selected':'').">Completed</option>
</select>

<input type='hidden' name='update_status'>
</form>
</td>

</tr>";
}

echo "</table>";
}


/* ===================================================
   RESERVATION SECTION
=================================================== */
if($section == 'reservation'){

echo "<h2>Reservations Management</h2>";

$result = mysqli_query($conn,
    "SELECT * FROM reservation ORDER BY id DESC"
);

echo "<table>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Table</th>
<th>Guests</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>
<th>Action</th>
</tr>";

while($row = mysqli_fetch_assoc($result)){

$statusClass =
($row['status']=='pending')
? 'status-pending'
: 'status-completed';

echo "<tr>
<td>{$row['id']}</td>
<td>{$row['customer_id']}</td>
<td>{$row['table_number']}</td>
<td>{$row['no_of_people']}</td>
<td>{$row['reservation_date']}</td>
<td>{$row['reservation_time']}</td>
<td class='$statusClass'>{$row['status']}</td>
<td>";

if($row['status']=='pending'){
echo "<a href='?confirm_res={$row['id']}&section=reservation'>
<button class='confirm'>Confirm</button>
</a>";
}else{
echo "✔ Confirmed";
}

echo "</td></tr>";
}

echo "</table>";
}
?>

</div>
</body>
</html>