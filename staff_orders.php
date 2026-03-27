<?php
session_start();
if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit();
}
$conn = mysqli_connect("localhost","root","","alkhaleej_db");
if(!$conn){ die("Connection Failed: " . mysqli_connect_error()); }

/* ===== UPDATE ORDER STATUS ===== */
if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    mysqli_query($conn,"UPDATE orders SET status='$status' WHERE order_id='$order_id'");
    header("Location: staff_orders.php");
    exit();
}

/* ===== DELETE ORDER ===== */
if(isset($_GET['delete'])){
    $order_id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM orders WHERE order_id='$order_id'");
    header("Location: staff_orders.php");
    exit();
}

/* ===== FETCH ORDERS ===== */
$orders = mysqli_query($conn,"
    SELECT o.*, 
           (SELECT GROUP_CONCAT(CONCAT(m.item_name, ' (x', oi.quantity, ')') SEPARATOR ', ')
            FROM order_items oi
            JOIN menu m ON oi.menu_id = m.menu_id
            WHERE oi.order_id = o.order_id) as items_list
    FROM orders o 
    ORDER BY o.order_id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Orders - Staff</title>
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
    /* ===== Table ===== */
    .table-container { background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); overflow-x: auto; }
    table { width:100%; border-collapse:collapse; border-radius:8px; overflow:hidden; }
    th, td { padding:15px; text-align:center; border-bottom:1px solid #ddd; font-size: 14px; }
    th { background:#7a1f2b; color:white; font-weight:500; }
    .status-pending { color: orange; font-weight: bold; }
    .status-preparing { color: blue; font-weight: bold; }
    .status-completed { color: green; font-weight: bold; }
    .status-cancelled { color: red; font-weight: bold; }
    select { padding:8px; border-radius:5px; border:1px solid #ccc; font-family:inherit; }
    button { padding:8px 12px; border:none; border-radius:5px; cursor:pointer; font-family:inherit; transition:0.3s; color:white; }
    .btn-update { background:#7a1f2b; } .btn-update:hover{ background:#631723; }
    .btn-view { background:#258752; text-decoration:none; padding:8px 12px; border-radius:5px; color:white; display:inline-block; } .btn-view:hover{ background:#1b663e; }
    .btn-delete { background:#c92a2a; text-decoration:none; padding:8px 12px; border-radius:5px; color:white; display:inline-block; } .btn-delete:hover{ background:#a02222; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Al-Khaleej Staff Panel</h2>
    <a href="staff_dashboard.php">Dashboard</a>
    <a href="staff_orders.php" style="background:#9e2f3d;">Manage Orders</a>
    <a href="staff_reservations.php">Manage Reservations</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>Manage Orders</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <div class="table-container">
        <table>
        <tr>
            <th>ID</th>
            <th>C.ID</th>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Items Ordered</th>
            <th>Status</th>
            <th>Update</th>
            <th>View</th>
            <th>Delete</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($orders)){ ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo $row['customer_id']; ?></td>
            <td><?php echo date('M d, H:i', strtotime($row['order_date'])); ?></td>
            <td><?php echo ucfirst(str_replace('_', ' ', $row['order_type'])); ?></td>
            <td style="font-weight:600;">₹ <?php echo $row['total_amount']; ?></td>
            <td style="font-size:0.9em; max-width:200px; text-align:left;"><?php echo $row['items_list'] ?? 'No items'; ?></td>

            <td class="status-<?php echo strtolower($row['status']); ?>">
                <?php echo ucfirst($row['status']); ?>
            </td>

            <td>
                <form method="POST" style="display:flex; gap:5px; flex-direction:column;">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <select name="status">
                        <option value="pending" <?php if($row['status']=='pending') echo "selected"; ?>>Pending</option>
                        <option value="preparing" <?php if($row['status']=='preparing') echo "selected"; ?>>Preparing</option>
                        <option value="completed" <?php if($row['status']=='completed') echo "selected"; ?>>Completed</option>
                        <option value="cancelled" <?php if($row['status']=='cancelled') echo "selected"; ?>>Cancelled</option>
                    </select>
                    <button name="update_status" class="btn-update">Update</button>
                </form>
            </td>

            <td>
                <a href="view_order.php?order_id=<?php echo $row['order_id']; ?>" class="btn-view">View</a>
            </td>

            <td>
                <a href="staff_orders.php?delete=<?php echo $row['order_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
        </table>
    </div>
</div>
</body>
</html>